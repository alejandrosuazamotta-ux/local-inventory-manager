<?php

namespace App\Exports;

use App\Models\SupplierDebt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SupplierDebtsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    private $rowNumber = 1;

    public function collection()
    {
        return SupplierDebt::orderBy('created_at', 'desc')->get();
    }

    public function map($debt): array
    {
        return [
            $this->rowNumber++,
            $debt->name,
            $debt->debt_amount,
            $debt->paid_amount,
            $debt->debt_amount - $debt->paid_amount,
            $debt->status == 'pagado' ? 'Pagado' : 'Pendiente',
            $debt->created_at->format('d/m/Y')
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Proveedor',
            'Total Deuda',
            'Total Pagado',
            'Saldo Pendiente',
            'Estado',
            'Fecha Registro'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => '"$"#,##0',
            'D' => '"$"#,##0',
            'E' => '"$"#,##0',
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF3B82F6']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }
}
