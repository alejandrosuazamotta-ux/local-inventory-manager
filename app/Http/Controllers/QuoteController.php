<?php

namespace App\Http\Controllers;

use App\Reports\PDF\PdfReportService;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Exports\QuotesExport;
use Maatwebsite\Excel\Facades\Excel;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $quotes = Sale::with(['customer', 'details'])
            ->where('invoice_type', 'quotation')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('document_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%")
                             ->orWhere('document_number', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
            
        $products = \App\Models\Product::orderBy('name')->get();
        return view('admin.quotes.index', compact('quotes', 'products', 'search'));
    }

    public function export()
    {
        return Excel::download(new QuotesExport, 'cotizaciones.xlsx');
    }

    public function exportPdf(PdfReportService $pdfService)
    {
        return $pdfService->generateQuotesPdf();
    }

    public function downloadPdf(Sale $sale, PdfReportService $pdfService)
    {
        return $pdfService->generateSingleQuotePdf($sale);
    }

}
