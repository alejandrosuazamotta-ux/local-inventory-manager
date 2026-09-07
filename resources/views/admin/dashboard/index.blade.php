@extends('layouts.admin')

@section('content')
<style>
    /* Premium Dashboard Styles */
    .dashboard-header {
        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.08);
    }
    
    /* Gradient KPI Cards */
    .kpi-sales {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
    }
    
    .kpi-profit {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .kpi-credit {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .kpi-caja {
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        color: white;
    }
    
    .kpi-inventory {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    
    .kpi-discount {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        color: white;
    }
    
    .kpi-card {
        border-radius: 14px;
        padding: 16px 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px -3px rgba(0,0,0,0.1);
        border: none;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .kpi-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.15);
    }
    
    .kpi-icon-wrapper {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        border-radius: 10px;
        padding: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .kpi-card h2 {
        font-size: 1.5rem;
        letter-spacing: -0.5px;
    }
    
    .kpi-card .fs-7 {
        font-size: 0.75rem;
    }
    
    /* Decoration circles */
    .kpi-card::after {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    
    /* Table styles */
    .premium-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    
    .premium-table thead th {
        border: none;
        color: #64748b;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0 16px 8px 16px;
    }
    
    .premium-table tbody tr {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
        border-radius: 8px;
    }
    
    .premium-table tbody tr:hover {
        transform: scale(1.01);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .premium-table td {
        padding: 16px;
        vertical-align: middle;
        border: none;
    }
    
    .premium-table td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .premium-table td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
    
    /* List styles */
    .premium-list-item {
        border: none;
        border-bottom: 1px solid #f1f5f9;
        padding: 16px;
        transition: background-color 0.2s;
    }
    
    .premium-list-item:hover {
        background-color: #f8fafc;
    }
    
    .premium-list-item:last-child {
        border-bottom: none;
    }
    
    .icon-box-sm {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Period filter pills */
    .period-pills {
        display: flex;
        gap: 4px;
        background: #e2e8f0;
        padding: 4px;
        border-radius: 12px;
    }
    .period-pill {
        padding: 7px 16px;
        border-radius: 9px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.25s ease;
    }
    .period-pill:hover {
        color: #334155;
        background: rgba(255, 255, 255, 0.5);
    }
    .period-pill.active {
        color: #0f172a;
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .date-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 9px;
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 500;
    }
</style>

<div class="container-fluid px-0 mx-auto" style="max-width: 1400px; padding-bottom: 40px;">
    
    <!-- 1. HEADER -->
    <div class="dashboard-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-radius: 12px; box-shadow: 0 6px 12px rgba(37, 99, 235, 0.25);">
                    <i data-lucide="layout-dashboard" style="color: white; width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <h4 class="m-0 fw-bold" style="color: #0f172a; letter-spacing: -0.5px;">Dashboard Principal</h4>
                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">Resumen de tu negocio y métricas clave</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <form method="GET" action="{{ route('dashboard') }}" id="periodForm">
                    <div class="period-pills">
                        <button type="submit" name="period" value="today" class="period-pill {{ $period == 'today' ? 'active' : '' }}">Hoy</button>
                        <button type="submit" name="period" value="week" class="period-pill {{ $period == 'week' ? 'active' : '' }}">Semana</button>
                        <button type="submit" name="period" value="month" class="period-pill {{ $period == 'month' ? 'active' : '' }}">Mes</button>
                        <button type="submit" name="period" value="year" class="period-pill {{ $period == 'year' ? 'active' : '' }}">Año</button>
                    </div>
                </form>
                <div class="date-badge">
                    <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                    {{ $startDate->format('d/m/Y') }} — {{ $endDate->format('d/m/Y') }}
                </div>
                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <a href="{{ route('export.all') }}" class="btn text-white d-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem;" title="Descargar todos los Excel de los módulos comprimidos en .ZIP">
                        <i data-lucide="file-archive" style="width: 16px; height: 16px;"></i> Descargar Módulos (.ZIP)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary KPI Row (Gradients) -->
    <div class="row g-4 mb-4">
        <!-- Caja (Efectivo Recibido) -->
        <div class="col-12 col-md-6 col-xl">
            <div class="kpi-card kpi-caja h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-0 text-white fw-bold fs-6 text-uppercase" style="letter-spacing: 0.5px;">Efectivo en Caja</p>
                        <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Contado + Abonos - Gastos)</small>
                        <h2 class="fw-bold mb-0 text-white mt-2">${{ number_format($efectivo_hoy, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon-wrapper">
                        <i data-lucide="wallet" style="color: white; width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.7rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                    <i data-lucide="activity" style="width: 12px; height: 12px;"></i> ${{ number_format($contado_hoy, 0, ',', '.') }} + ${{ number_format($abonos_hoy, 0, ',', '.') }} - ${{ number_format($gastos_hoy, 0, ',', '.') }} = ${{ number_format($efectivo_hoy, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Venta Contado -->
        <div class="col-12 col-md-6 col-xl">
            <div class="kpi-card kpi-sales h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-0 text-white fw-bold fs-6 text-uppercase" style="letter-spacing: 0.5px;">Venta Contado</p>
                        <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Ingresos directos)</small>
                        <h2 class="fw-bold mb-0 text-white mt-2">${{ number_format($contado_hoy, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon-wrapper">
                        <i data-lucide="banknote" style="color: white; width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.7rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                    <i data-lucide="trending-up" style="width: 12px; height: 12px;"></i> Efectivo / Transferencia
                </div>
            </div>
        </div>

        <!-- Venta Crédito -->
        <div class="col-12 col-md-6 col-xl">
            <div class="kpi-card kpi-inventory h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-0 text-white fw-bold fs-6 text-uppercase" style="letter-spacing: 0.5px;">Venta Crédito</p>
                        <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Créditos generados)</small>
                        <h2 class="fw-bold mb-0 text-white mt-2">${{ number_format($credito_hoy, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon-wrapper">
                        <i data-lucide="credit-card" style="color: white; width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.7rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                    <i data-lucide="trending-up" style="width: 12px; height: 12px;"></i> Créditos creados en el periodo
                </div>
            </div>
        </div>

        <!-- Abonos Recibidos -->
        <div class="col-12 col-md-6 col-xl">
            <div class="kpi-card kpi-discount h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-0 text-white fw-bold fs-6 text-uppercase" style="letter-spacing: 0.5px;">Abonos Recibidos</p>
                        <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Abonos a deudas)</small>
                        <h2 class="fw-bold mb-0 text-white mt-2">${{ number_format($abonos_hoy, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon-wrapper">
                        <i data-lucide="coins" style="color: white; width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.7rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                    <i data-lucide="activity" style="width: 12px; height: 12px;"></i> Pagos de créditos en el periodo
                </div>
            </div>
        </div>

        <!-- Gastos Diarios -->
        <div class="col-12 col-md-6 col-xl">
            <div class="kpi-card kpi-credit h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-0 text-white fw-bold fs-6 text-uppercase" style="letter-spacing: 0.5px;">Gastos Diarios</p>
                        <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Egresos registrados)</small>
                        <h2 class="fw-bold mb-0 text-white mt-2">${{ number_format($gastos_hoy, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon-wrapper">
                        <i data-lucide="calculator" style="color: white; width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.7rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                    <i data-lucide="trending-down" style="width: 12px; height: 12px;"></i> Egresos del periodo
                </div>
            </div>
        </div>

        <!-- Gastos de Mercancía -->
        <div class="col-12 col-md-6 col-xl">
            <div class="kpi-card h-100" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: white;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-0 text-white fw-bold fs-6 text-uppercase" style="letter-spacing: 0.5px;">Gastos Mercancía</p>
                        <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Inversión compras)</small>
                        <h2 class="fw-bold mb-0 text-white mt-2">${{ number_format($gastos_mercancia, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon-wrapper">
                        <i data-lucide="package" style="color: white; width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.7rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                    <i data-lucide="shopping-cart" style="width: 12px; height: 12px;"></i> Inversión en inventario
                </div>
            </div>
        </div>

        <!-- Utilidad Generada -->
        <div class="col-12 col-md-6 col-xl">
            <div class="kpi-card kpi-profit h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-0 text-white fw-bold fs-6 text-uppercase" style="letter-spacing: 0.5px;">Utilidad Generada</p>
                        <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Ganancia neta estimada)</small>
                        <h2 class="fw-bold mb-0 text-white mt-2">${{ number_format($utilidad_hoy, 0, ',', '.') }}</h2>
                    </div>
                    <div class="kpi-icon-wrapper">
                        <i data-lucide="pie-chart" style="color: white; width: 20px; height: 20px;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.7rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                    <i data-lucide="trending-up" style="width: 12px; height: 12px;"></i> Ganancia neta periodo
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="glass-card card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-box-sm" style="background: #fee2e2;">
                    <i data-lucide="alert-circle" style="color: #ef4444; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fs-7 fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Por Cobrar</p>
                    <p class="fw-bold mb-0 fs-6" style="color: #0f172a;">${{ number_format($creditos_pendientes, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="glass-card card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-box-sm" style="background: #f3e8ff;">
                    <i data-lucide="tag" style="color: #a855f7; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fs-7 fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Descuentos</p>
                    <p class="fw-bold mb-0 fs-6" style="color: #0f172a;">${{ number_format($descuentos_hoy, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="glass-card card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-box-sm" style="background: #e0e7ff;">
                    <i data-lucide="users" style="color: #4338ca; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fs-7 fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Clientes</p>
                    <p class="fw-bold mb-0 fs-6" style="color: #0f172a;">{{ $total_clientes }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="glass-card card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-box-sm" style="background: #fce7f3;">
                    <i data-lucide="shopping-bag" style="color: #be185d; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fs-7 fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Catálogo</p>
                    <p class="fw-bold mb-0 fs-6" style="color: #0f172a;">{{ $total_productos }} prod.</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md">
            <div class="glass-card card-body p-3 d-flex align-items-center gap-3">
                <div class="icon-box-sm" style="background: #dcfce7;">
                    <i data-lucide="file-text" style="color: #15803d; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fs-7 fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Facturas</p>
                    <p class="fw-bold mb-0 fs-6" style="color: #0f172a;">{{ $facturas_hoy }} emitidas</p>
                </div>
            </div>
        </div>
    </div>

    
    <!-- ApexCharts Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold m-0" style="color: #0f172a;"><i data-lucide="activity" style="color: #2563eb; width: 18px; margin-right: 5px;"></i> Evolución Histórica</h6>
                    <small class="text-muted">Comparativa de Ventas, Utilidad y Efectivo Recibido.</small>
                </div>
                <div class="card-body p-4">
                    <div id="financialChart" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Últimas Ventas -->
        <div class="col-12 col-xl-8">
            <div class="glass-card h-100 d-flex flex-column">
                <div class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold m-0" style="color: #0f172a;">Últimas Ventas Realizadas</h6>
                        <small class="text-muted">Las transacciones más recientes procesadas hoy.</small>
                    </div>
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-light rounded-pill fw-semibold" style="color: #2563eb; font-size: 0.8rem;">Ver todas</a>
                </div>
                <div class="card-body p-4 pt-2 flex-grow-1">
                    <div class="table-responsive">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Factura</th>
                                    <th>Cliente</th>
                                    <th>Hora</th>
                                    <th class="text-end">Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latest_sales as $sale)
                                <tr>
                                    <td>
                                        @if($sale->invoice_type == 'credit')
                                            <span class="badge" style="background: #fef3c7; color: #d97706; padding: 6px 10px; font-weight: 600; border-radius: 6px;">
                                                #CRD-{{ $sale->document_number }}
                                            </span>
                                        @else
                                            <span class="badge" style="background: #e0e7ff; color: #4338ca; padding: 6px 10px; font-weight: 600; border-radius: 6px;">
                                                #FAC-{{ $sale->document_number }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #f1f5f9; color: #64748b; font-weight: bold; font-size: 0.75rem;">
                                                {{ substr($sale->customer->name ?? 'C', 0, 1) }}
                                            </div>
                                            <span class="fw-semibold" style="color: #334155;">{{ $sale->customer->name ?? 'Consumidor Final' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted" style="font-size: 0.85rem;">
                                            <i data-lucide="clock" style="width: 12px; height: 12px; display: inline-block; margin-bottom: 2px;"></i>
                                            {{ $sale->created_at->format('H:i') }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold" style="color: #0f172a;">
                                        ${{ number_format($sale->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px dashed #cbd5e1;">
                                            <i data-lucide="inbox" style="color: #94a3b8; width: 32px; height: 32px; margin-bottom: 10px;"></i>
                                            <p class="text-muted mb-0 fw-semibold">No hay ventas registradas el día de hoy.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4 d-flex flex-column gap-4">
            <!-- Bajo Stock -->
            <div class="glass-card">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold m-0" style="color: #0f172a;">Bajo Stock</h6>
                        <small class="text-muted">Productos que requieren reabastecimiento.</small>
                    </div>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-light rounded-pill fw-semibold" style="color: #2563eb; font-size: 0.8rem;">Ver todos</a>
                </div>
                <div class="card-body p-0 mt-2">
                    <ul class="list-group list-group-flush">
                        @forelse($low_stock as $prod)
                        <li class="premium-list-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box-sm" style="background: #fee2e2;">
                                    <i data-lucide="alert-triangle" style="color: #dc2626; width: 16px; height: 16px;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-truncate" style="color: #334155; font-size: 0.9rem; max-width: 150px;">{{ $prod->name }}</h6>
                                    <small class="text-muted" style="font-size: 0.75rem;">Mínimo: {{ $prod->min_stock }} uds.</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge" style="background: #fee2e2; color: #dc2626; padding: 6px 10px; font-weight: 600; border-radius: 6px;">
                                    {{ $prod->stock }} uds.
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="premium-list-item text-center py-4">
                            <p class="text-muted mb-0 fs-7">Todo el inventario está en niveles óptimos.</p>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ApexCharts Script -->
<script type="application/json" id="financialChartData">
{!! json_encode($chart_data ?? ['labels'=>[], 'sales'=>[], 'profits'=>[], 'payments'=>[]]) !!}
</script>
<script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var chartDataEl = document.getElementById('financialChartData');
        var chartData = chartDataEl ? JSON.parse(chartDataEl.textContent) : null;
        if (!chartData || !chartData.labels || chartData.labels.length === 0) return;
        var options = {
            series: [{
                name: 'Ventas',
                data: chartData['sales']
            }, {
                name: 'Utilidad',
                data: chartData['profits']
            }, {
                name: 'Efectivo Recibido',
                data: chartData['payments']
            }],
            chart: {
                height: 380,
                type: 'area',
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            colors: ['#2563eb', '#10b981', '#f59e0b'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                type: 'category',
                categories: chartData['labels'],
                labels: { 
                    style: { colors: '#64748b', fontWeight: 500 },
                    formatter: function(val) {
                        var groupBy = "{{ $groupBy ?? 'DATE' }}";
                        if (!val) return val;
                        var rawVal = val.toString();
                        if (groupBy === 'MONTH') {
                            const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                            return months[parseInt(rawVal) - 1] || rawVal;
                        } else if (groupBy === 'HOUR') {
                            var parts = rawVal.split(':');
                            if(parts.length === 2) {
                                var h = parseInt(parts[0]);
                                var ampm = h >= 12 ? 'PM' : 'AM';
                                h = h % 12;
                                if(h === 0) h = 12;
                                return h + ':' + parts[1] + ' ' + ampm;
                            }
                        } else {
                            var parts = rawVal.split('-');
                            if(parts.length === 3) {
                                const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                                return parseInt(parts[2]) + ' ' + months[parseInt(parts[1]) - 1];
                            }
                        }
                        return val;
                    }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#64748b', fontWeight: 500 },
                    formatter: function (value) { return "$" + value.toLocaleString("es-CO"); }
                }
            },
            tooltip: {
                theme: 'light',
                x: {
                    formatter: function(val, opts) {
                        var rawVal = val;
                        // For category type charts, val might be the index (1-based or 0-based).
                        // It's safer to read directly from our chartData.
                        if (opts && typeof opts.dataPointIndex !== 'undefined') {
                            var cat = chartData['labels'][opts.dataPointIndex];
                            if (cat) rawVal = cat;
                        }
                        
                        var groupBy = "{{ $groupBy ?? 'DATE' }}";
                        if (!rawVal) return rawVal;
                        rawVal = rawVal.toString();
                        
                        if (groupBy === 'MONTH') {
                            const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                            return months[parseInt(rawVal) - 1] || rawVal;
                        } else if (groupBy === 'HOUR') {
                            var parts = rawVal.split(':');
                            if(parts.length === 2) {
                                var h = parseInt(parts[0]);
                                var ampm = h >= 12 ? 'PM' : 'AM';
                                h = h % 12;
                                if(h === 0) h = 12;
                                return h + ':' + parts[1] + ' ' + ampm;
                            }
                        } else {
                            var parts = rawVal.split('-');
                            if(parts.length === 3) {
                                const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                                return parseInt(parts[2]) + ' de ' + months[parseInt(parts[1]) - 1] + ' de ' + parts[0];
                            }
                        }
                        return rawVal;
                    }
                },
                y: { formatter: function (val) { return "$" + val.toLocaleString("es-CO"); } }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontWeight: 600,
                markers: { radius: 12 }
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
        };

        var chart = new ApexCharts(document.querySelector("#financialChart"), options);
        chart.render();
    });
</script>

@endsection
