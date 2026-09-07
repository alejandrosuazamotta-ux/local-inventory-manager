<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    private $rowNumber = 1;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Customer::orderBy('id', 'asc')->get();
    }

    public function map($customer): array
    {
        return [
            $this->rowNumber++,
            $customer->name,
            $customer->phone ? ' ' . $customer->phone : 'N/A',
            $customer->document_number ? ' ' . $customer->document_number : 'N/A',
            $customer->email ?? 'N/A',
            $customer->address ?? 'N/A',
            $customer->created_at->format('d/m/Y H:i')
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Cliente',
            'Celular',
            'Cédula',
            'Correo',
            'Dirección',
            'Fecha de Registro'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Align all cells to the left
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [
            // Style the first row as bold text.
            1    => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF3B82F6']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }
}
