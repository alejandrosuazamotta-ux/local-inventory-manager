<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Exports\ExpensesExport;
use App\Reports\PDF\PdfReportService;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $baseQuery = Expense::query()
            ->when($search, function ($q, $search) {
                return $q->where('description', 'like', "%{$search}%");
            });

        $totalPersonal = (clone $baseQuery)->where('type', 'personal')->sum('amount');
        $totalMerchandise = (clone $baseQuery)->where('type', 'merchandise')->sum('amount');

        $query = (clone $baseQuery)
            ->when($type, function ($q, $type) {
                return $q->where('type', $type);
            });

        $totalExpenses = (clone $query)->sum('amount');

        $expenses = $query->orderBy('description', 'asc')->paginate(10)->withQueryString();

        return view('admin.expenses.index', compact('expenses', 'search', 'type', 'totalPersonal', 'totalMerchandise', 'totalExpenses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('amount')) {
            $request->merge([
                'amount' => str_replace('.', '', $request->amount)
            ]);
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'type' => 'required|in:personal,merchandise',
        ]);

        Expense::create([
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'type' => $request->type ?? 'personal',
        ]);

        return redirect()->route('expenses.index')->with('success', 'Gasto registrado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        if ($request->has('amount')) {
            $request->merge([
                'amount' => str_replace('.', '', $request->amount)
            ]);
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'type' => 'required|in:personal,merchandise',
        ]);

        $expense->update([
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'type' => $request->type ?? 'personal',
        ]);

        return redirect()->route('expenses.index')->with('success', 'Gasto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Gasto eliminado exitosamente.');
    }

    /**
     * Export expenses to Excel.
     */
    public function export()
    {
        return Excel::download(new ExpensesExport, 'historial_gastos.xlsx');
    }

    /**
     * Export expenses to PDF.
     */
    public function exportPdf(Request $request, PdfReportService $pdfService)
    {
        $search = $request->input('search');
        return $pdfService->generateExpensesPdf($search);
    }
}
