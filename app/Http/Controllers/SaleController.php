<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    /**
     * Verifica que haya stock suficiente para cada producto antes de descontarlo.
     * Suma las cantidades cuando el mismo producto aparece en varias filas.
     * Se ejecuta ANTES de tocar la base de datos, para no dejar el inventario
     * inconsistente si la validación falla.
     *
     * Al editar una venta ($existingSale), sus cantidades ya descontadas se
     * van a restaurar antes de aplicar las nuevas, así que cuentan como stock
     * disponible para esta validación aunque el registro en base de datos
     * todavía no se haya actualizado.
     */
    private function checkStockAvailability(array $productDetails, string $invoiceType, ?Sale $existingSale = null): void
    {
        if ($invoiceType === 'quotation') {
            return;
        }

        $requestedByProduct = [];
        foreach ($productDetails as $details) {
            $productId = $details['product_id'];
            $requestedByProduct[$productId] = ($requestedByProduct[$productId] ?? 0) + $details['quantity'];
        }

        $reservedByProduct = [];
        if ($existingSale && $existingSale->invoice_type !== 'quotation') {
            foreach ($existingSale->details as $oldDetail) {
                $reservedByProduct[$oldDetail->product_id] = ($reservedByProduct[$oldDetail->product_id] ?? 0) + $oldDetail->quantity;
            }
        }

        $errors = [];
        foreach ($requestedByProduct as $productId => $requestedQty) {
            $product = Product::find($productId);
            if (!$product) {
                continue;
            }
            $available = $product->stock + ($reservedByProduct[$productId] ?? 0);
            if ($requestedQty > $available) {
                $errors['products'] = "No hay suficiente stock de \"{$product->name}\" (disponible: {$available}, solicitado: {$requestedQty}).";
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function create(Request $request)
    {
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $invoiceType = $request->input('type', 'paid');
        return view('admin.sales.create', compact('products', 'customers', 'invoiceType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_type' => 'required|in:paid,credit,quotation',
            'customer_name' => 'required|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'due_date' => 'required_if:invoice_type,credit|nullable|date',
            'paid' => 'required_if:invoice_type,credit|nullable|numeric|min:0',
        ]);

        // Compute totals
        $invoiceType = $request->invoice_type;
        $subtotal = 0;
        $totalDiscount = 0;
        $productDetails = [];

        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            $qty = intval($item['quantity']);
            $price = floatval($item['price']);
            $discountPercent = isset($item['discount']) ? floatval($item['discount']) : 0;

            $rowSubtotal = $qty * $price;
            $rowDiscount = $rowSubtotal * ($discountPercent / 100);

            $subtotal += $rowSubtotal;
            $totalDiscount += $rowDiscount;

            $productDetails[] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $rowSubtotal - $rowDiscount,
                'product_model' => $product
            ];
        }

        $totalFinal = $subtotal - $totalDiscount;

        // Validar stock ANTES de crear el cliente, para no dejar clientes
        // huérfanos en la base de datos cuando la venta se bloquea.
        $this->checkStockAvailability($productDetails, $invoiceType);

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['name' => $request->customer_name],
            [
                'document_number' => $request->customer_cedula,
                'phone' => $request->customer_phone,
                'email' => $request->customer_email,
                'address' => $request->customer_address
            ]
        );

        // Set financial fields
        $initialPayment = 0;
        $pendingBalance = 0;
        $status = 'paid';

        if ($invoiceType === 'paid') {
            $initialPayment = $totalFinal;
            $pendingBalance = 0;
            $status = 'paid';
        } elseif ($invoiceType === 'credit') {
            $initialPayment = floatval($request->paid ?? 0);
            $pendingBalance = max(0, $totalFinal - $initialPayment);
            $status = $pendingBalance > 0 ? 'pending' : 'paid';
        } elseif ($invoiceType === 'quotation') {
            $initialPayment = 0;
            $pendingBalance = $totalFinal;
            $status = 'quotation';
        }

        // Calculate next document_number
        $lastDoc = Sale::where('invoice_type', $invoiceType)->max('document_number');
        $nextDocNumber = $lastDoc ? $lastDoc + 1 : 1;

        // Create the sale
        $sale = Sale::create([
            'customer_id' => $customer->id,
            'user_id' => auth()->id(),
            'total' => $totalFinal,
            'status' => $status,
            'invoice_type' => $invoiceType,
            'document_number' => $nextDocNumber,
            'due_date' => $invoiceType === 'credit' ? $request->due_date : null,
            'initial_payment' => $initialPayment,
            'pending_balance' => $pendingBalance,
            'discount' => $totalDiscount,
        ]);

        // Save details and update stock
        foreach ($productDetails as $details) {
            $sale->details()->create([
                'product_id' => $details['product_id'],
                'quantity' => $details['quantity'],
                'unit_price' => $details['unit_price'],
                'subtotal' => $details['subtotal'],
            ]);

            // Deduct stock if NOT a quote
            if ($invoiceType !== 'quotation') {
                $product = $details['product_model'];
                $product->stock = max(0, $product->stock - $details['quantity']);
                $product->save();
            }
        }

        // Registrar abono inicial SOLO para créditos (no para facturas de contado)
        // Las facturas de contado se contabilizan directamente en ventas, no en abonos
        if ($invoiceType === 'credit' && $initialPayment > 0) {
            Payment::create([
                'sale_id'        => $sale->id,
                'customer_id'    => $customer->id,
                'amount'         => $initialPayment,
                'payment_method' => 'cash',
                'date'           => now(),
            ]);
        }

        // Redirect based on type
        if ($invoiceType === 'quotation') {
            return redirect()->route('quotes.index')->with('success', 'Cotización guardada exitosamente.');
        } elseif ($invoiceType === 'credit') {
            return redirect()->route('credits.index')->with('success', 'Crédito guardado exitosamente.');
        } else {
            return redirect()->route('invoices.index')->with('success', 'Factura guardada exitosamente.');
        }
    }

    public function edit(Sale $sale)
    {
        $sale->load('customer', 'details.product');
        $products = Product::orderBy('name')->get();
        return view('admin.sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'invoice_type' => 'required|in:paid,credit,quotation',
            'customer_name' => 'required|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'due_date' => 'required_if:invoice_type,credit|nullable|date',
            'paid' => 'required_if:invoice_type,credit|nullable|numeric|min:0',
        ]);

        // Compute totals and prepare details
        $invoiceType = $request->invoice_type;
        $subtotal = 0;
        $totalDiscount = 0;
        $productDetails = [];

        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            $qty = intval($item['quantity']);
            $price = floatval($item['price']);
            $discountPercent = isset($item['discount']) ? floatval($item['discount']) : 0;

            $rowSubtotal = $qty * $price;
            $rowDiscount = $rowSubtotal * ($discountPercent / 100);

            $subtotal += $rowSubtotal;
            $totalDiscount += $rowDiscount;

            $productDetails[] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $rowSubtotal - $rowDiscount,
                'product_model' => $product
            ];
        }

        $totalFinal = $subtotal - $totalDiscount;

        // Validar stock ANTES de crear/tocar el cliente.
        $this->checkStockAvailability($productDetails, $invoiceType, $sale);

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['name' => $request->customer_name],
            [
                'document_number' => $request->customer_cedula,
                'phone' => $request->customer_phone,
                'email' => $request->customer_email,
                'address' => $request->customer_address
            ]
        );

        // Set financial fields
        $initialPayment = 0;
        $pendingBalance = 0;
        $status = 'paid';

        if ($invoiceType === 'paid') {
            $initialPayment = $totalFinal;
            $pendingBalance = 0;
            $status = 'paid';
        } elseif ($invoiceType === 'credit') {
            if ($sale->invoice_type === 'credit') {
                $totalPaymentsMade = Payment::where('sale_id', $sale->id)->sum('amount');
                $initialPayment = $sale->initial_payment;
                $pendingBalance = max(0, $totalFinal - $totalPaymentsMade);
            } else {
                $initialPayment = floatval($request->paid ?? 0);
                $pendingBalance = max(0, $totalFinal - $initialPayment);
            }
            $status = $pendingBalance > 0 ? 'pending' : 'paid';
        } elseif ($invoiceType === 'quotation') {
            $initialPayment = 0;
            $pendingBalance = $totalFinal;
            $status = 'quotation';
        }

        // Revert old stock if it was not a quotation
        if ($sale->invoice_type !== 'quotation') {
            foreach ($sale->details as $oldDetail) {
                $oldProduct = Product::find($oldDetail->product_id);
                if ($oldProduct) {
                    $oldProduct->stock += $oldDetail->quantity;
                    $oldProduct->save();
                }
            }
        }

        // Delete old details
        $sale->details()->delete();

        // Save new details and deduct stock
        foreach ($productDetails as $details) {
            $sale->details()->create([
                'product_id' => $details['product_id'],
                'quantity' => $details['quantity'],
                'unit_price' => $details['unit_price'],
                'subtotal' => $details['subtotal'],
            ]);

            // Deduct stock if NOT a quote
            if ($invoiceType !== 'quotation') {
                $product = $details['product_model'];
                $product->stock = max(0, $product->stock - $details['quantity']);
                $product->save();
            }
        }

        // Update the sale
        $sale->update([
            'customer_id' => $customer->id,
            'total' => $totalFinal,
            'status' => $status,
            'invoice_type' => $invoiceType,
            'due_date' => $invoiceType === 'credit' ? $request->due_date : null,
            'initial_payment' => $initialPayment,
            'pending_balance' => $pendingBalance,
            'discount' => $totalDiscount,
        ]);

        // Registrar abono inicial SOLO para créditos sin pagos previos, y sincronizar/limpiar según corresponda
        if ($invoiceType !== 'credit') {
            Payment::where('sale_id', $sale->id)->delete();
        } else {
            Payment::where('sale_id', $sale->id)->update(['customer_id' => $customer->id]);

            if ($initialPayment > 0 && Payment::where('sale_id', $sale->id)->count() === 0) {
                Payment::create([
                    'sale_id'        => $sale->id,
                    'customer_id'    => $customer->id,
                    'amount'         => $initialPayment,
                    'payment_method' => 'cash',
                    'date'           => now(),
                ]);
            }
        }

        // Redirect based on type
        if ($invoiceType === 'quotation') {
            return redirect()->route('quotes.index')->with('success', 'Cotización actualizada exitosamente.');
        } elseif ($invoiceType === 'credit') {
            return redirect()->route('credits.index')->with('success', 'Crédito actualizado exitosamente.');
        } else {
            return redirect()->route('invoices.index')->with('success', 'Factura actualizada exitosamente.');
        }
    }

    public function updateGeneral(Request $request, Sale $sale)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        $customer = Customer::firstOrCreate(
            ['name' => $request->customer_name],
            [
                'document_number' => $request->customer_cedula,
                'phone' => $request->customer_phone,
                'email' => $request->customer_email,
                'address' => $request->customer_address
            ]
        );

        $sale->update([
            'customer_id' => $customer->id,
            'due_date' => $request->due_date ?? $sale->due_date,
        ]);

        return back()->with('success', 'Información general actualizada exitosamente.');
    }

    public function destroy(Sale $sale)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Restaurar stock si no era cotización
            if ($sale->invoice_type !== 'quotation') {
                foreach ($sale->details as $detail) {
                    if ($detail->product) {
                        $detail->product->stock += $detail->quantity;
                        $detail->product->save();
                    }
                }
            }

            // Eliminar abonos
            Payment::where('sale_id', $sale->id)->delete();
            // Eliminar detalles
            \Illuminate\Support\Facades\DB::table('sale_details')->where('sale_id', $sale->id)->delete();
            
            // Eliminar venta
            $sale->delete();

            \Illuminate\Support\Facades\DB::commit();

            $message = $sale->invoice_type === 'quotation' 
                ? 'Cotización eliminada exitosamente.'
                : 'Registro eliminado exitosamente y stock restaurado.';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Ocurrió un error al intentar eliminar: ' . $e->getMessage());
        }
    }
}
