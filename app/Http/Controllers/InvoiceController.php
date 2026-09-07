<?php

namespace App\Http\Controllers;

use App\Reports\PDF\PdfReportService;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $invoices = Sale::with(['customer', 'details.product', 'payments'])
            ->where('invoice_type', 'paid')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('document_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%")
                             ->orWhere('document_number', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
            
        $products = Product::orderBy('name')->get();

        return view('admin.invoices.index', compact('invoices', 'products', 'search'));
    }

    public function export()
    {
        return Excel::download(new InvoicesExport, 'facturas.xlsx');
    }

    public function exportPdf(PdfReportService $pdfService)
    {
        return $pdfService->generateInvoicesPdf();
    }

    public function downloadPdf(Sale $sale, PdfReportService $pdfService)
    {
        return $pdfService->generateSingleInvoicePdf($sale);
    }

}
