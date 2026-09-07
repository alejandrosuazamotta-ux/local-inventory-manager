<?php

namespace App\Exports;

use App\Models\CarteraAntigua;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CarteraAntiguaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    private $rowNumber = 1;
    protected $search;
    protected $estado;

    public function __construct($search = null, $estado = null)
    {
        $this->search = $search;
        $this->estado = $estado;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = CarteraAntigua::query();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('nombre_cliente', 'like', '%' . $this->search . '%')
                  ->orWhere('observaciones', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->estado)) {
            $query->where('estado', $this->estado);
        }

        return $query->latest('fecha_registro')->latest('id')->get();
    }

    /**
     * @param mixed $record
     * @return array
     */
    public function map($record): array
    {
        $saldoPendiente = max(0, $record->deuda_total - $record->monto_pagado);
        return [
            $this->rowNumber++,
            $record->nombre_cliente,
            $record->deuda_total,
            $record->monto_pagado,
            $saldoPendiente,
            ucfirst($record->estado),
            $record->fecha_registro->format('d/m/Y'),
            $record->observaciones ?? '',
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'Cliente',
            'Deuda Total',
            'Monto Pagado',
            'Saldo Pendiente',
            'Estado',
            'Fecha Registro',
            'Observaciones'
        ];
    }

    /**
     * @return array
     */
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
            'H' => NumberFormat::FORMAT_TEXT,
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
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
