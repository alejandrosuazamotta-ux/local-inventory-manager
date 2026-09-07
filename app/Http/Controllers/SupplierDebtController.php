<?php

namespace App\Http\Controllers;

use App\Models\SupplierDebt;
use Illuminate\Http\Request;
use App\Reports\PDF\PdfReportService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SupplierDebtsExport;

class SupplierDebtController extends Controller
{
    public function export()
    {
        return Excel::download(new SupplierDebtsExport, 'deudas_proveedores.xlsx');
    }

    public function exportPdf(PdfReportService $pdfService)
    {
        return $pdfService->generateSupplierDebtsPdf();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SupplierDebt::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $debts = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();
        return view('admin.supplier-debts.index', compact('debts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('debt_amount')) {
            $request->merge([
                'debt_amount' => str_replace('.', '', $request->debt_amount)
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'debt_amount' => 'required|numeric|min:0',
        ]);

        SupplierDebt::create([
            'name' => $request->name,
            'debt_amount' => $request->debt_amount,
            'paid_amount' => 0,
            'status' => 'pendiente',
        ]);

        return redirect()->route('supplier-debts.index')->with('success', 'Deuda agregada exitosamente.');
    }

    /**
     * Store a payment for the debt.
     */
    public function pay(Request $request, SupplierDebt $supplierDebt)
    {
        if ($request->has('amount')) {
            $request->merge([
                'amount' => str_replace('.', '', $request->amount)
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $newPaidAmount = $supplierDebt->paid_amount + $request->amount;
        $status = $supplierDebt->status;

        if ($newPaidAmount >= $supplierDebt->debt_amount) {
            $status = 'pagado';
            // Optionally cap the paid amount to the debt amount
            $newPaidAmount = $supplierDebt->debt_amount;
        }

        $supplierDebt->update([
            'paid_amount' => $newPaidAmount,
            'status' => $status,
        ]);

        return redirect()->route('supplier-debts.index')->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierDebt $supplierDebt)
    {
        if ($request->has('debt_amount')) {
            $request->merge([
                'debt_amount' => str_replace('.', '', $request->debt_amount)
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'debt_amount' => 'required|numeric|min:0',
        ]);

        $status = $supplierDebt->status;
        if ($supplierDebt->paid_amount >= $request->debt_amount && $request->debt_amount > 0) {
            $status = 'pagado';
        } elseif ($supplierDebt->paid_amount < $request->debt_amount) {
            $status = 'pendiente';
        }

        $supplierDebt->update([
            'name' => $request->name,
            'debt_amount' => $request->debt_amount,
            'status' => $status,
        ]);

        return redirect()->route('supplier-debts.index')->with('success', 'Deuda actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierDebt $supplierDebt)
    {
        $supplierDebt->delete();
        return redirect()->route('supplier-debts.index')->with('success', 'Deuda eliminada exitosamente.');
    }
}
