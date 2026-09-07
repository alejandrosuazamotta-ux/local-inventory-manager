<?php

namespace App\Http\Controllers;

use App\Reports\PDF\PdfReportService;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'month');
        
        $startDate = Carbon::today()->startOfDay();
        $endDate = Carbon::today()->endOfDay(); // 23:59:59

        if ($period == 'week') {
            $startDate = Carbon::today()->subDays(6)->startOfDay();
        } elseif ($period == 'month') {
            $startDate = Carbon::now()->startOfMonth();
        } elseif ($period == 'year') {
            $startDate = Carbon::now()->startOfYear();
        } elseif ($period == 'custom' && $request->has('start') && $request->has('end')) {
            $startDate = Carbon::parse($request->input('start'))->startOfDay();
            $endDate = Carbon::parse($request->input('end'))->endOfDay();
        }

        // Las tarjetas de totales usan el acumulado del año cuando el periodo es "Hoy",
        // para no mostrar $0 apenas empieza el día. La gráfica de abajo sigue usando
        // $startDate/$endDate (hoy, agrupado por hora) sin verse afectada por esto.
        $cardStartDate = $startDate;
        $cardEndDate = $endDate;
        if ($period == 'today') {
            $cardStartDate = Carbon::now()->startOfYear();
            $cardEndDate = Carbon::today()->endOfDay();
        }

        // --- INFO DEL INVENTARIO ---
        $inventario = DB::table('products')->where('status', 'active')
            ->select(
                DB::raw('SUM(cost * stock) as valor_total'),
                DB::raw('SUM((price - cost) * stock) as utilidad_potencial'),
                DB::raw('COUNT(id) as total_productos'),
                DB::raw('SUM(stock) as total_unidades')
            )->first();
            
        // --- INFO DE VENTAS Y UTILIDAD (FILTRADAS POR PERIODO) ---
        $ventas_periodo = Sale::whereBetween('created_at', [$cardStartDate, $cardEndDate])
            ->whereIn('invoice_type', ['paid', 'credit'])->sum('total');

        $utilidad_periodo = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$cardStartDate, $cardEndDate])
            ->whereIn('sales.invoice_type', ['paid', 'credit'])
            ->sum(DB::raw('sale_details.subtotal - (products.cost * sale_details.quantity)'));
            
        
        $ventas_historicas = Sale::whereIn('invoice_type', ['paid', 'credit'])->sum('total');
        
        // --- GESTIÓN DE CARTERA ---
        $cartera = [
            'total_pendiente' => Sale::where('invoice_type', 'credit')->where('status', 'pending')->sum('pending_balance'),
            'total_creditos' => Sale::where('invoice_type', 'credit')->sum('total'),
            'total_abonos' => Payment::sum('amount'), // Histórico
            'clientes_activos' => Sale::where('invoice_type', 'credit')->where('status', 'pending')->distinct('customer_id')->count('customer_id'),
            'facturas_pendientes' => Sale::where('invoice_type', 'credit')->where('status', 'pending')->count(),
        ];
        
        // Ventas de contado del periodo (Ventas de contado completo)
        $ventas_contado = Sale::whereBetween('created_at', [$cardStartDate, $cardEndDate])
            ->where('invoice_type', 'paid')
            ->sum('total');

        // Abonos reales (pagos de créditos en el periodo)
        $abonos_reales = Payment::whereBetween('created_at', [$cardStartDate, $cardEndDate])->sum('amount');

        $gastos_periodo = \App\Models\Expense::where(function($q) use ($cardStartDate, $cardEndDate) {
            $q->whereBetween('created_at', [$cardStartDate, $cardEndDate])
              ->orWhereBetween('date', [$cardStartDate->format('Y-m-d'), $cardEndDate->format('Y-m-d')]);
        })->sum('amount');

        // Efectivo total (Contado + Abonos - Gastos)
        $efectivo_recibido = ($ventas_contado + $abonos_reales) - $gastos_periodo;

        // Crédito del periodo (Pendiente + Abonos)
        $credito_periodo_pendiente = Sale::where('invoice_type', 'credit')
            ->whereBetween('created_at', [$cardStartDate, $cardEndDate])
            ->sum('pending_balance');

        $credito_periodo = $credito_periodo_pendiente + $abonos_reales;
        
        // Facturas por estado
        $estado_creditos = [
            'pendientes' => Sale::where('invoice_type', 'credit')->where('status', 'pending')->where('pending_balance', '=', DB::raw('total'))->count(),
            'parciales' => Sale::where('invoice_type', 'credit')->where('status', 'pending')->where('pending_balance', '<', DB::raw('total'))->count(),
            'pagadas' => Sale::where('invoice_type', 'credit')->where('status', 'paid')->count(),
            'vencidas' => Sale::where('invoice_type', 'credit')->where('status', 'pending')->whereDate('due_date', '<', Carbon::today())->count(),
        ];

        // --- PRODUCTOS MÁS VENDIDOS (PERIODO) ---
        $top_producto = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$cardStartDate, $cardEndDate])
            ->whereIn('sales.invoice_type', ['paid', 'credit'])
            ->select(
                'products.name', 
                DB::raw('SUM(sale_details.quantity) as qty'), 
                DB::raw('SUM(sale_details.subtotal) as total_sales'),
                DB::raw('SUM(sale_details.subtotal - (products.cost * sale_details.quantity)) as profit')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->first();

        // --- DATOS PARA GRÁFICOS (Evolución diaria o mensual) ---
        // Decidir agrupación según el periodo
        if ($period == 'year' || ($period == 'custom' && $startDate->diffInDays($endDate) > 31)) {
            $groupBy = 'MONTH';
            $labelExpr = "strftime('%m', created_at)";
            $labelExprSales = "strftime('%m', sales.created_at)";
        } elseif ($period == 'today' || ($period == 'custom' && $startDate->diffInDays($endDate) == 0)) {
            $groupBy = 'HOUR';
            $labelExpr = "strftime('%H:00', created_at)";
            $labelExprSales = "strftime('%H:00', sales.created_at)";
        } else {
            $groupBy = 'DATE';
            $labelExpr = "DATE(created_at)";
            $labelExprSales = "DATE(sales.created_at)";
        }

        $chart_sales = DB::table('sales')
            ->whereIn('invoice_type', ['paid', 'credit'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("$labelExpr as label"),
                DB::raw('SUM(total) as y_sales'),
                DB::raw('SUM(discount) as y_discounts')
            )
            ->groupBy('label')->orderBy('label')->get();
            
        $chart_profits = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereIn('sales.invoice_type', ['paid', 'credit'])
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                DB::raw("$labelExprSales as label"),
                DB::raw('SUM(sale_details.subtotal - (products.cost * sale_details.quantity)) as y_profits')
            )
            ->groupBy('label')->orderBy('label')->get();
            
        $chart_payments = DB::table('payments')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("$labelExpr as label"),
                DB::raw('SUM(amount) as y_payments')
            )
            ->groupBy('label')->orderBy('label')->get();

        // Armar estructura para el gráfico
        $labels_map = [];
        
        // Pre-fill labels to ensure continuous chart
        if ($groupBy == 'HOUR') {
            for ($i = 0; $i < 24; $i++) {
                $h = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $labels_map[$h] = ['sales' => 0, 'profits' => 0, 'payments' => 0, 'discounts' => 0];
            }
        } elseif ($groupBy == 'DATE') {
            $current = clone $startDate;
            while ($current <= $endDate) {
                $labels_map[$current->format('Y-m-d')] = ['sales' => 0, 'profits' => 0, 'payments' => 0, 'discounts' => 0];
                $current->addDay();
            }
        } elseif ($groupBy == 'MONTH') {
            for ($i = 1; $i <= 12; $i++) {
                $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                $labels_map[$m] = ['sales' => 0, 'profits' => 0, 'payments' => 0, 'discounts' => 0];
            }
        }

        foreach($chart_sales as $row) {
            if(!isset($labels_map[$row->label])) $labels_map[$row->label] = ['sales' => 0, 'profits' => 0, 'payments' => 0, 'discounts' => 0];
            $labels_map[$row->label]['sales'] = $row->y_sales;
            $labels_map[$row->label]['discounts'] = $row->y_discounts;
        }
        foreach($chart_profits as $row) {
            if(!isset($labels_map[$row->label])) $labels_map[$row->label] = ['sales' => 0, 'profits' => 0, 'payments' => 0, 'discounts' => 0];
            $labels_map[$row->label]['profits'] = $row->y_profits;
        }
        foreach($chart_payments as $row) {
            if(!isset($labels_map[$row->label])) $labels_map[$row->label] = ['sales' => 0, 'profits' => 0, 'payments' => 0, 'discounts' => 0];
            $labels_map[$row->label]['payments'] = $row->y_payments;
        }
        
        // Discounts are already applied at the item level in details.subtotal, so we don't subtract them again from profits.
        
        ksort($labels_map);
        
        $chart_data = [
            'labels' => array_keys($labels_map),
            'sales' => array_column($labels_map, 'sales'),
            'profits' => array_column($labels_map, 'profits'),
            'payments' => array_column($labels_map, 'payments'),
        ];

        return view('admin.portfolio.index', compact(
            'period', 'inventario', 'ventas_periodo', 'utilidad_periodo', 'ventas_historicas',
            'cartera', 'efectivo_recibido', 'abonos_reales', 'ventas_contado', 'estado_creditos', 'top_producto', 'chart_data',
            'startDate', 'endDate', 'groupBy', 'credito_periodo', 'credito_periodo_pendiente', 'gastos_periodo'
        ));
    }

    public function exportExcel(Request $request)
    {
        $period = $request->input('period', 'month');
        $dates = $this->getDatesFromPeriod($period, $request);
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PortfolioExport($period, $dates['start'], $dates['end']),
            'reporte_cartera_'.$period.'.xlsx'
        );
    }

    public function exportPdf(Request $request, PdfReportService $pdfService)
    {
        $period = $request->input('period', 'month');
        $dates = $this->getDatesFromPeriod($period, $request);
        return $pdfService->generatePortfolioPdf($period, $dates['start'], $dates['end']);
    }

    private function getDatesFromPeriod($period, $request)
    {
        $startDate = Carbon::today()->startOfDay();
        $endDate = Carbon::today()->endOfDay(); // 23:59:59

        if ($period == 'week') {
            $startDate = Carbon::today()->subDays(6)->startOfDay();
        } elseif ($period == 'month') {
            $startDate = Carbon::now()->startOfMonth();
        } elseif ($period == 'year') {
            $startDate = Carbon::now()->startOfYear();
        } elseif ($period == 'custom' && $request->has('start') && $request->has('end')) {
            $startDate = Carbon::parse($request->input('start'))->startOfDay();
            $endDate = Carbon::parse($request->input('end'))->endOfDay();
        }
        return ['start' => $startDate, 'end' => $endDate];
    }

}
