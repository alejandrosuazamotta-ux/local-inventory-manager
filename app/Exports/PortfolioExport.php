<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PortfolioExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $period;
    protected $startDate;
    protected $endDate;

    public function __construct($period, $startDate, $endDate)
    {
        $this->period = $period;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        // Calcular datos
        $inventario = DB::table('products')->where('status', 'active')
            ->select(
                DB::raw('SUM(cost * stock) as valor_total'),
                DB::raw('SUM((price - cost) * stock) as utilidad_potencial')
            )->first();
            
        $ventas_periodo = Sale::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->whereIn('invoice_type', ['paid', 'credit'])->sum('total');
            
        $utilidad_periodo = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$this->startDate, $this->endDate])
            ->whereIn('sales.invoice_type', ['paid', 'credit'])
            ->sum(DB::raw('(sale_details.unit_price - products.cost) * sale_details.quantity'));
            
        $margen = $ventas_periodo > 0 ? round(($utilidad_periodo / $ventas_periodo) * 100, 1) : 0;

        $cartera_pendiente = Sale::where('invoice_type', 'credit')->sum('pending_balance');
        // Solo abonos de créditos (no pagos de facturas de contado)
        $abonos_periodo = Payment::join('sales', 'payments.sale_id', '=', 'sales.id')
            ->where('sales.invoice_type', 'credit')
            ->whereBetween('payments.created_at', [$this->startDate, $this->endDate])
            ->sum('payments.amount');

        $periodLabel = '';
        if ($this->period == 'today') {
            $periodLabel = 'Hoy, ' . $this->startDate->format('d/m/Y');
        } elseif ($this->period == 'week') {
            $periodLabel = 'Últimos 7 días';
        } elseif ($this->period == 'month') {
            $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            $mesIndex = (int) $this->startDate->format('n') - 1;
            $periodLabel = $meses[$mesIndex] . ' de ' . $this->startDate->format('Y');
        } elseif ($this->period == 'year') {
            $periodLabel = 'Año ' . $this->startDate->format('Y');
        } else {
            $periodLabel = $this->startDate->format('d/m/Y') . ' al ' . $this->endDate->format('d/m/Y');
        }

        return [
            [
                $periodLabel,
                $inventario->valor_total,
                $inventario->utilidad_potencial,
                $ventas_periodo,
                $utilidad_periodo,
                $margen . '%',
                $cartera_pendiente,
                $abonos_periodo
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Periodo',
            'Valor Inventario',
            'Utilidad Potencial',
            'Ventas Periodo',
            'Utilidad Neta Periodo',
            'Margen',
            'Cartera Pendiente Total',
            'Abonos Recaudados Periodo'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '#,##0',
            'C' => '#,##0',
            'D' => '#,##0',
            'E' => '#,##0',
            'G' => '#,##0',
            'H' => '#,##0',
        ];
    }
}
