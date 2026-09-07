<?php

namespace App\Http\Controllers;

use App\Reports\PDF\PdfReportService;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Exports\CreditsExport;
use App\Exports\OverdueCreditsExport;
use Maatwebsite\Excel\Facades\Excel;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Sale::with(['customer', 'details', 'payments'])
            ->where('invoice_type', 'credit')
            ->where('status', 'pending')
            ->when($search, function ($q, $search) {
                return $q->where(function ($subQ) use ($search) {
                    $subQ->where('document_number', 'like', "%{$search}%")
                         ->orWhereHas('customer', function ($cq) use ($search) {
                             $cq->where('name', 'like', "%{$search}%")
                                ->orWhere('document_number', 'like', "%{$search}%");
                         });
                });
            });

        $totalPendingBalance = (clone $query)->sum('pending_balance');
        $totalCreditAmount = (clone $query)->sum('total');
        $totalPendingCount = (clone $query)->count();

        $credits = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $products = \App\Models\Product::orderBy('name')->get();

        return view('admin.credits.index', compact(
            'credits', 'products', 'search', 'totalPendingBalance', 'totalCreditAmount', 'totalPendingCount'
        ));
    }

    public function paid(Request $request)
    {
        $search = $request->input('search');

        $credits = Sale::with(['customer', 'details', 'payments'])
            ->where('invoice_type', 'credit')
            ->where('status', 'paid')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('document_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%")
                             ->orWhere('document_number', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        $products = \App\Models\Product::orderBy('name')->get();
        return view('admin.credits.paid', compact('credits', 'products', 'search'));
    }

    public function overdue(Request $request)
    {
        $search = $request->input('search');

        $credits = Sale::with(['customer', 'details', 'payments'])
            ->where('invoice_type', 'credit')
            ->where('status', 'pending')
            ->whereDate('due_date', '<', \Carbon\Carbon::today())
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('document_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%")
                             ->orWhere('document_number', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('due_date', 'asc')
            ->paginate(10)
            ->withQueryString();
        $products = \App\Models\Product::orderBy('name')->get();
        return view('admin.credits.overdue', compact('credits', 'products', 'search'));
    }

    public function export()
    {
        return Excel::download(new CreditsExport, 'creditos.xlsx');
    }

    public function exportOverdue()
    {
        return Excel::download(new OverdueCreditsExport, 'creditos_vencidos.xlsx');
    }

    public function exportPdf(PdfReportService $pdfService)
    {
        return $pdfService->generateCreditsPdf();
    }

    public function exportOverduePdf(PdfReportService $pdfService)
    {
        return $pdfService->generateOverdueCreditsPdf();
    }

    public function downloadPdf(Sale $sale, PdfReportService $pdfService)
    {
        return $pdfService->generateSingleInvoicePdf($sale);
    }

}
