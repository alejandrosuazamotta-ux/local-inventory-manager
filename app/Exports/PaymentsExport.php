<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function query()
    {
        return Payment::with(['sale', 'customer'])
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'NO. ABONO',
            'DOCUMENTO ORIGEN',
            'CLIENTE',
            'FECHA',
            'MÉTODO PAGO',
            'MONTO'
        ];
    }

    public function map($payment): array
    {
        return [
            'ABN-' . $payment->id,
            'CRE-' . ($payment->sale->document_number ?? 'N/A'),
            $payment->customer->name ?? 'Consumidor Final',
            $payment->created_at->format('d/m/Y'),
            $payment->payment_method == 'cash' ? 'Efectivo' : ucfirst($payment->payment_method),
            $payment->amount,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '#,##0',
        ];
    }
}
