<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    private $rowNumber = 0;
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::when($this->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nº',
            'Nombre del Producto',
            'Cantidad Actual',
            'Costo de Compra ($)',
            'Precio de Venta ($)',
            'Utilidad Neta ($)',
            'Margen de Ganancia (%)',
            'Fecha de Registro',
        ];
    }

    public function map($product): array
    {
        $this->rowNumber++;
        $ganancia = $product->price - $product->cost;
        $margen = $product->price > 0 ? round(($ganancia / $product->price) * 100, 2) : 0;

        return [
            $this->rowNumber,
            $product->name,
            $product->stock,
            $product->cost,
            $product->price,
            $ganancia,
            $margen . '%',
            $product->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila 1 (Cabeceras)
            1    => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['argb' => 'FF059669']]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => '#,##0',
            'D' => '#,##0',
            'E' => '#,##0',
            'F' => '#,##0',
        ];
    }
}
