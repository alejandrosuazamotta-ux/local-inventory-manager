<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $file->move(public_path('img'), 'logo.png');
        }

        return redirect()->back()->with('success', 'Logo actualizado correctamente.');
    }

    public function index(Request $request)
    {
        $period = $request->input('period', 'month');
        
        $startDate = Carbon::today()->startOfDay();
        $endDate = Carbon::today()->endOfDay();

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
        
        // Tarjetas informativas
        $ventas_hoy = Sale::whereBetween('created_at', [$startDate, $endDate])->whereIn('invoice_type', ['paid', 'credit'])->sum('total');
        
        $utilidad_hoy = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->whereIn('sales.invoice_type', ['paid', 'credit'])
            ->sum(DB::raw('sale_details.subtotal - (products.cost * sale_details.quantity)'));
            
        $descuentos_hoy = Sale::whereBetween('created_at', [$startDate, $endDate])->whereIn('invoice_type', ['paid', 'credit'])->sum('discount');
        
        // Sumar abonos de créditos + facturas de contado (efectivo real ingresado)
        $abonos_hoy = \App\Models\Payment::join('sales', 'payments.sale_id', '=', 'sales.id')
            ->where('sales.invoice_type', 'credit')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->sum('payments.amount');

        $contado_hoy = Sale::where('invoice_type', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $credito_hoy_pendiente = Sale::where('invoice_type', 'credit')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('pending_balance');

        // Crédito total generado en el periodo
        $credito_hoy = Sale::where('invoice_type', 'credit')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $expensesQuery = \App\Models\Expense::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate])
              ->orWhereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        });

        $gastos_personales = (clone $expensesQuery)->where(function($q) {
            $q->where('type', 'personal')->orWhereNull('type');
        })->sum('amount');

        $gastos_mercancia = (clone $expensesQuery)->where('type', 'merchandise')->sum('amount');

        $gastos_hoy = $gastos_personales + $gastos_mercancia;

        $efectivo_hoy = ($abonos_hoy + $contado_hoy) - $gastos_hoy;
        
        $creditos_pendientes = Sale::where('invoice_type', 'credit')->where('status', 'pending')->sum('pending_balance');
        
        $productos_vendidos_hoy = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->whereIn('sales.invoice_type', ['paid', 'credit'])
            ->sum('sale_details.quantity');
        
        $total_clientes = Customer::count();
        
        $total_productos = Product::where('status', 'active')->count();
        
        $facturas_hoy = Sale::whereBetween('created_at', [$startDate, $endDate])->whereIn('invoice_type', ['paid', 'credit'])->count();

        // Secciones adicionales
        $low_stock = Product::where('status', 'active')->whereColumn('stock', '<=', 'min_stock')->take(5)->get();
        
        $latest_sales = Sale::with('customer')->whereIn('invoice_type', ['paid', 'credit'])->latest()->take(5)->get();

        // Chart Data (Uses the same period)
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
            ->select(DB::raw("$labelExpr as label"), DB::raw('SUM(total) as y_sales'), DB::raw('SUM(discount) as y_discounts'))
            ->groupBy('label')->orderBy('label')->get();
            
        $chart_profits = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->whereIn('sales.invoice_type', ['paid', 'credit'])
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(DB::raw("$labelExprSales as label"), DB::raw('SUM(sale_details.subtotal - (products.cost * sale_details.quantity)) as y_profits'))
            ->groupBy('label')->orderBy('label')->get();
            
        // Solo abonos de créditos para la gráfica
        // Usamos $labelExprPayments para evitar columna ambigua tras el JOIN con sales
        $labelExprPayments = str_replace('created_at', 'payments.created_at', $labelExpr);
        $chart_payments = DB::table('payments')
            ->join('sales', 'payments.sale_id', '=', 'sales.id')
            ->where('sales.invoice_type', 'credit')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->select(DB::raw("{$labelExprPayments} as label"), DB::raw('SUM(payments.amount) as y_payments'))
            ->groupBy('label')->orderBy('label')->get();

        // Facturas de contado para la gráfica
        $chart_paid_sales = DB::table('sales')
            ->where('invoice_type', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw("{$labelExpr} as label"), DB::raw('SUM(total) as y_paid'))
            ->groupBy('label')->orderBy('label')->get();

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
            // Subtract discounts from profits
            $labels_map[$row->label]['profits'] = $row->y_profits;
        }
        foreach($chart_payments as $row) {
            if(!isset($labels_map[$row->label])) $labels_map[$row->label] = ['sales' => 0, 'profits' => 0, 'payments' => 0, 'discounts' => 0];
            $labels_map[$row->label]['payments'] += $row->y_payments;
        }
        foreach($chart_paid_sales as $row) {
            if(!isset($labels_map[$row->label])) $labels_map[$row->label] = ['sales' => 0, 'profits' => 0, 'payments' => 0, 'discounts' => 0];
            $labels_map[$row->label]['payments'] += $row->y_paid;
        }

        // Discounts are already applied at the item level in details.subtotal, so we don't subtract them again from profits.
        ksort($labels_map);
        $chart_data = [
            'labels' => array_keys($labels_map),
            'sales' => array_column($labels_map, 'sales'),
            'profits' => array_column($labels_map, 'profits'),
            'payments' => array_column($labels_map, 'payments'),
        ];

        return view('admin.dashboard.index', compact(
            'chart_data', 'period', 'startDate', 'endDate', 'groupBy',
            'ventas_hoy', 'utilidad_hoy', 'efectivo_hoy', 'creditos_pendientes',
            'descuentos_hoy', 'productos_vendidos_hoy', 'total_clientes', 'total_productos', 'facturas_hoy', 'low_stock',
            'latest_sales', 'contado_hoy', 'credito_hoy', 'abonos_hoy', 'credito_hoy_pendiente', 'gastos_hoy', 'gastos_personales', 'gastos_mercancia'
        ));
    }

    public function exportExcel(Request $request)
    {
        $period = $request->input('period', 'month');
        $dates = $this->getDatesFromPeriod($period, $request);
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PortfolioExport($period, $dates['start'], $dates['end']),
            'reporte_finanzas_'.$period.'.xlsx'
        );
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
