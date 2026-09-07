<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CreditsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function query()
    {
        return Sale::with(['customer', 'payments'])
            ->where('invoice_type', 'credit')
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'NO. FACTURA',
            'CLIENTE',
            'TELÉFONO',
            'FECHA EMISIÓN',
            'FECHA VENCIMIENTO',
            'ESTADO',
            'TOTAL',
            'ABONOS',
            'SALDO PENDIENTE'
        ];
    }

    public function map($sale): array
    {
        $abonos = $sale->payments->sum('amount');
        $saldo = $sale->total - $abonos;
        $estado = $sale->status == 'paid' ? 'Pagado' : 'Pendiente';

        return [
            'FAC-' . $sale->document_number,
            $sale->customer->name ?? 'Consumidor Final',
            $sale->customer?->phone ? ' ' . $sale->customer->phone : 'N/A',
            $sale->created_at->format('d/m/Y'),
            $sale->due_date ? \Carbon\Carbon::parse($sale->due_date)->format('d/m/Y') : 'N/A',
            $estado,
            $sale->total,
            $abonos,
            $saldo,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0',
            'H' => '#,##0',
            'I' => '#,##0',
        ];
    }
}
