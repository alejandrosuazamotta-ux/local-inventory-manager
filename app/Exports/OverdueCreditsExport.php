<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OverdueCreditsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        return Sale::with(['customer', 'payments'])
            ->where('invoice_type', 'credit')
            ->where('status', 'pending')
            ->whereDate('due_date', '<', \Carbon\Carbon::today())
            ->orderBy('due_date', 'asc');
    }

    public function headings(): array
    {
        return [
            'NO. FACTURA',
            'CLIENTE',
            'TELÉFONO',
            'FECHA EMISIÓN',
            'FECHA VENCIMIENTO',
            'DÍAS ATRASO',
            'TOTAL',
            'ABONOS',
            'SALDO PENDIENTE'
        ];
    }

    public function map($sale): array
    {
        $abonos = $sale->payments->sum('amount');
        $saldo = $sale->total - $abonos;
        
        $dias_atraso = 0;
        if($sale->due_date) {
            $due = \Carbon\Carbon::parse($sale->due_date);
            if($due->isPast()) {
                $dias_atraso = $due->diffInDays(\Carbon\Carbon::now());
            }
        }

        return [
            'FAC-' . $sale->document_number,
            $sale->customer->name ?? 'Consumidor Final',
            $sale->customer?->phone ? ' ' . $sale->customer->phone : 'N/A',
            $sale->created_at->format('d/m/Y'),
            $sale->due_date ? \Carbon\Carbon::parse($sale->due_date)->format('d/m/Y') : 'N/A',
            $dias_atraso,
            $sale->total,
            $abonos,
            $saldo,
        ];
    }
}
