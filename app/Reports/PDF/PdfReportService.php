<?php

namespace App\Reports\PDF;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PdfReportService
{
    public function generateProductsPdf($search = null)
    {
        $data = Product::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->latest()
            ->get();
        $pdf = Pdf::loadView('pdf.products', compact('data'))->setPaper('a4', 'portrait');
        return $pdf->download('reporte_productos.pdf');
    }

    public function generateCustomersPdf()
    {
        $data = Customer::oldest('id')->get();
        $pdf = Pdf::loadView('pdf.customers', compact('data'))->setPaper('a4', 'landscape');
        return $pdf->download('reporte_clientes.pdf');
    }

    public function generateInvoicesPdf()
    {
        $data = Sale::with('customer')->where([['invoice_type', '=', 'paid']])->latest()->get();
        $pdf = Pdf::loadView('pdf.invoices', compact('data'))->setPaper('a4', 'landscape');
        return $pdf->download('historial_facturas.pdf');
    }

    public function generateSingleInvoicePdf(Sale $invoice)
    {
        $invoice->load(['customer', 'details.product', 'payments']);
        $pdf = Pdf::loadView('pdf.invoice_single', compact('invoice'))->setPaper('a4', 'portrait');
        $prefix = $invoice->invoice_type == 'credit' ? 'credito_' : 'factura_';
        return $pdf->download($prefix . $invoice->document_number . '.pdf');
    }

    public function generateQuotesPdf()
    {
        $data = Sale::with('customer')->where([['invoice_type', '=', 'quotation']])->latest()->get();
        $pdf = Pdf::loadView('pdf.quotes', compact('data'))->setPaper('a4', 'landscape');
        return $pdf->download('historial_cotizaciones.pdf');
    }

    public function generateSingleQuotePdf(Sale $quote)
    {
        $quote->load(['customer', 'details.product']);
        $pdf = Pdf::loadView('pdf.quote_single', compact('quote'))->setPaper('a4', 'portrait');
        return $pdf->download('cotizacion_' . $quote->document_number . '.pdf');
    }

    public function generateCreditsPdf()
    {
        $data = Sale::with(['customer', 'payments'])->where([['invoice_type', '=', 'credit']])->latest()->get();
        $pdf = Pdf::loadView('pdf.credits', compact('data'))->setPaper('a4', 'landscape');
        return $pdf->download('historial_creditos.pdf');
    }

    public function generateOverdueCreditsPdf()
    {
        $data = Sale::with(['customer', 'payments'])->where([['invoice_type', '=', 'credit'], ['status', '=', 'pending']])->whereDate('due_date', '<', Carbon::today())->oldest('due_date')->get();
        $pdf = Pdf::loadView('pdf.overdue', compact('data'))->setPaper('a4', 'landscape');
        return $pdf->download('creditos_vencidos.pdf');
    }

    public function generatePaymentsPdf()
    {
        $data = Payment::with(['sale', 'customer'])->latest()->get();
        $pdf = Pdf::loadView('pdf.payments', compact('data'))->setPaper('a4', 'portrait');
        return $pdf->download('historial_abonos.pdf');
    }

    public function generateExpensesPdf($search = null)
    {
        $data = \App\Models\Expense::when($search, function ($query, $search) {
                return $query->where('description', 'like', "%{$search}%");
            })
            ->latest()
            ->get();
        $pdf = Pdf::loadView('pdf.expenses', compact('data'))->setPaper('a4', 'portrait');
        return $pdf->download('historial_gastos.pdf');
    }

    public function generatePortfolioPdf($period, $startDate, $endDate)
    {
        $inventario = DB::table('products')->where([['status', '=', 'active']])
            ->select(
                DB::raw('SUM(cost * stock) as valor_total'),
                DB::raw('SUM((price - cost) * stock) as utilidad_potencial'),
                DB::raw('COUNT(id) as total_productos'),
                DB::raw('SUM(stock) as total_unidades')
            )->first();
            
        $ventas_periodo = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('invoice_type', ['paid', 'credit'])->sum('total');
            
        $utilidad_periodo = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->whereIn('sales.invoice_type', ['paid', 'credit'])
            ->sum(DB::raw('(sale_details.unit_price - products.cost) * sale_details.quantity'));
            
        $cartera = [
            'total_pendiente' => Sale::where([['invoice_type', '=', 'credit']])->sum('pending_balance'),
            'total_creditos' => Sale::where([['invoice_type', '=', 'credit']])->sum('total'),
            'facturas_pendientes' => Sale::where([['invoice_type', '=', 'credit'], ['status', '=', 'pending']])->count(),
        ];
        
        $abonos_periodo = Payment::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        
        $estado_creditos = [
            'pendientes' => Sale::where([['invoice_type', '=', 'credit'], ['status', '=', 'pending']])->where([['pending_balance', '=', DB::raw('total')]])->count(),
            'parciales' => Sale::where([['invoice_type', '=', 'credit'], ['status', '=', 'pending']])->where('pending_balance', '<', DB::raw('total'))->count(),
            'pagadas' => Sale::where([['invoice_type', '=', 'credit'], ['status', '=', 'paid']])->count(),
            'vencidas' => Sale::where([['invoice_type', '=', 'credit'], ['status', '=', 'pending']])->whereDate('due_date', '<', Carbon::today())->count(),
        ];

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'inventario' => $inventario,
            'ventas_periodo' => $ventas_periodo,
            'utilidad_periodo' => $utilidad_periodo,
            'cartera' => $cartera,
            'abonos_periodo' => $abonos_periodo,
            'estado_creditos' => $estado_creditos
        ];

        $pdf = Pdf::loadView('pdf.portfolio', compact('data'))->setPaper('a4', 'portrait');
        return $pdf->download('reporte_cartera_'.$period.'.pdf');
    }
    public function generateSupplierDebtsPdf()
    {
        $data = \App\Models\SupplierDebt::orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('pdf.supplier-debts', compact('data'))->setPaper('a4', 'portrait');
        return $pdf->download('reporte_deudas_proveedores.pdf');
    }

    public function generateCarteraAntiguaPdf($search = null, $estado = null)
    {
        $query = \App\Models\CarteraAntigua::query();

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_cliente', 'like', '%' . $search . '%')
                  ->orWhere('observaciones', 'like', '%' . $search . '%');
            });
        }

        if (!empty($estado)) {
            $query->where('estado', $estado);
        }

        $data = $query->latest('fecha_registro')->latest('id')->get();
        $pdf = Pdf::loadView('pdf.cartera-antigua', compact('data'))->setPaper('a4', 'landscape');
        return $pdf->download('reporte_cartera_antigua.pdf');
    }
}
