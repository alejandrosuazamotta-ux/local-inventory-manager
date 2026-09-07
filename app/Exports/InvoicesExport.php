<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InvoicesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function query()
    {
        return Sale::with('customer')
            ->where('invoice_type', 'paid')
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'NO. FACTURA',
            'CLIENTE',
            'DOCUMENTO/NIT',
            'TELÉFONO',
            'FECHA',
            'TOTAL'
        ];
    }

    public function map($sale): array
    {
        return [
            'FAC-' . $sale->document_number,
            $sale->customer->name ?? 'Consumidor Final',
            $sale->customer->document_number ?? 'N/A',
            $sale->customer?->phone ? ' ' . $sale->customer->phone : 'N/A',
            $sale->created_at->format('d/m/Y'),
            $sale->total,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '#,##0',
        ];
    }
}
