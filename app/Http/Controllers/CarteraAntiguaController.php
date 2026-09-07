<?php

namespace App\Http\Controllers;

use App\Models\CarteraAntigua;
use Illuminate\Http\Request;
use App\Exports\CarteraAntiguaExport;
use App\Reports\PDF\PdfReportService;
use Maatwebsite\Excel\Facades\Excel;

class CarteraAntiguaController extends Controller
{
    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');
        return Excel::download(new CarteraAntiguaExport($search, $estado), 'cartera_antigua.xlsx');
    }

    public function exportPdf(Request $request, PdfReportService $pdfService)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');
        return $pdfService->generateCarteraAntiguaPdf($search, $estado);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CarteraAntigua::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre_cliente', 'like', '%' . $request->search . '%')
                  ->orWhere('observaciones', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $records = $query->orderBy('nombre_cliente', 'asc')->paginate(10)->withQueryString();
        return view('admin.cartera-antigua.index', compact('records'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('deuda_total')) {
            $request->merge([
                'deuda_total' => str_replace('.', '', $request->deuda_total)
            ]);
        }

        $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'deuda_total' => 'required|numeric|min:0',
            'fecha_registro' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        CarteraAntigua::create([
            'nombre_cliente' => $request->nombre_cliente,
            'deuda_total' => $request->deuda_total,
            'monto_pagado' => 0,
            'estado' => 'pendiente',
            'fecha_registro' => $request->fecha_registro,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('cartera-antigua.index')->with('success', 'Registro de cartera antigua creado exitosamente.');
    }

    /**
     * Store a payment for the debt.
     */
    public function pay(Request $request, CarteraAntigua $carteraAntigua)
    {
        if ($request->has('amount')) {
            $request->merge([
                'amount' => str_replace('.', '', $request->amount)
            ]);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $newPaidAmount = $carteraAntigua->monto_pagado + $request->amount;
        $estado = $carteraAntigua->estado;

        if ($newPaidAmount >= $carteraAntigua->deuda_total) {
            $estado = 'pagado';
            $newPaidAmount = $carteraAntigua->deuda_total;
        }

        $carteraAntigua->update([
            'monto_pagado' => $newPaidAmount,
            'estado' => $estado,
        ]);

        return redirect()->route('cartera-antigua.index')->with('success', 'Abono registrado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CarteraAntigua $carteraAntigua)
    {
        if ($request->has('deuda_total')) {
            $request->merge([
                'deuda_total' => str_replace('.', '', $request->deuda_total)
            ]);
        }

        $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'deuda_total' => 'required|numeric|min:0',
            'fecha_registro' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $estado = $carteraAntigua->estado;
        if ($carteraAntigua->monto_pagado >= $request->deuda_total && $request->deuda_total > 0) {
            $estado = 'pagado';
        } elseif ($carteraAntigua->monto_pagado < $request->deuda_total) {
            $estado = 'pendiente';
        }

        $carteraAntigua->update([
            'nombre_cliente' => $request->nombre_cliente,
            'deuda_total' => $request->deuda_total,
            'estado' => $estado,
            'fecha_registro' => $request->fecha_registro,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('cartera-antigua.index')->with('success', 'Registro de cartera antigua actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CarteraAntigua $carteraAntigua)
    {
        $carteraAntigua->delete();
        return redirect()->route('cartera-antigua.index')->with('success', 'Registro de cartera antigua eliminado exitosamente.');
    }
}
