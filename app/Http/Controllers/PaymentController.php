<?php

namespace App\Http\Controllers;

use App\Reports\PDF\PdfReportService;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $payments = Payment::with(['sale.details.product', 'customer'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('sale', function ($sq) use ($search) {
                          $sq->where('document_number', 'like', "%{$search}%");
                      })
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%")
                             ->orWhere('document_number', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $pendingCredits = \App\Models\Sale::where('invoice_type', 'credit')
            ->where('status', 'pending')
            ->with('customer')
            ->get();

        return view('admin.payments.index', compact('payments', 'search', 'pendingCredits'));
    }

    public function store(Request $request)
    {
        if ($request->has('amount')) {
            $request->merge([
                'amount' => str_replace('.', '', $request->amount)
            ]);
        }

        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'amount'  => 'required|numeric|min:0.01'
        ]);

        $sale = \App\Models\Sale::findOrFail($request->sale_id);

        if ($sale->pending_balance <= 0) {
            return redirect()->back()->with('error', 'Este crédito ya está pagado en su totalidad.');
        }

        if ($request->amount > $sale->pending_balance) {
            return redirect()->back()->with('error', 'El monto del abono no puede superar el saldo pendiente ($' . number_format($sale->pending_balance, 0, ',', '.') . ').');
        }

        // Crear el abono
        Payment::create([
            'sale_id'        => $sale->id,
            'customer_id'    => $sale->customer_id,
            'amount'         => $request->amount,
            'payment_method' => 'cash',
            'date'           => now(),
        ]);

        // Actualizar saldo del crédito
        $sale->pending_balance -= $request->amount;

        // Si se pagó todo: cambiar status pero CONSERVAR invoice_type='credit'
        // Un crédito pagado sigue siendo un crédito — no se convierte en factura.
        // Esto garantiza que los reportes de cartera y el historial sean correctos.
        if ($sale->pending_balance <= 0) {
            $sale->pending_balance = 0;
            $sale->status = 'paid';
            $sale->save();
            return redirect()->route('credits.index')
                ->with('success', 'El crédito #CRD-' . $sale->document_number . ' fue pagado en su totalidad. ✓');
        }

        $sale->save();

        return redirect()->back()
            ->with('success', 'Abono registrado exitosamente. Saldo pendiente: $' . number_format($sale->pending_balance, 0, ',', '.'));
    }

    public function update(Request $request, Payment $payment)
    {
        if ($request->has('amount')) {
            $request->merge([
                'amount' => str_replace('.', '', $request->amount)
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $sale        = $payment->sale;
            $oldAmount   = $payment->amount;
            $newAmount   = $request->amount;

            // Balance disponible incluyendo lo que ya se había abonado
            $availableBalance = $sale->pending_balance + $oldAmount;

            if ($newAmount > $availableBalance) {
                return redirect()->back()->with('error', 'El monto no puede superar la deuda total ($' . number_format($availableBalance, 0, ',', '.') . ').');
            }

            // Actualizar abono
            $payment->amount = $newAmount;
            $payment->save();

            // Actualizar saldo del crédito
            $sale->pending_balance = $availableBalance - $newAmount;

            if ($sale->pending_balance <= 0) {
                $sale->pending_balance = 0;
                $sale->status = 'paid';
                // NO cambiar invoice_type: el crédito pagado sigue siendo un crédito
            } else {
                $sale->status = 'pending';
            }
            $sale->save();

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Abono actualizado exitosamente.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el abono: ' . $e->getMessage());
        }
    }

    public function destroy(Payment $payment)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $sale = $payment->sale;
            $sale->pending_balance += $payment->amount;

            if ($sale->pending_balance > 0) {
                $sale->status = 'pending';
                // NO cambiar invoice_type, ya es 'credit'
            }
            $sale->save();

            $payment->delete();

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->back()->with('success', 'Abono eliminado exitosamente y saldo restaurado.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al intentar eliminar el abono: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new PaymentsExport, 'historial_abonos.xlsx');
    }

    public function exportPdf(PdfReportService $pdfService)
    {
        return $pdfService->generatePaymentsPdf();
    }
}
