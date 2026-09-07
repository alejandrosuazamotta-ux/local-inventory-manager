@extends('layouts.admin')

@section('content')
<style>
    .portfolio-hero {
        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 50%, #f0f9ff 100%);
        border-radius: 20px;
        padding: 28px 32px;
        margin-bottom: 28px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        position: relative;
    }
    
    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.08);
    }
    
    /* Gradient KPI Cards */
    .kpi-sales { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; }
    .kpi-profit { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
    .kpi-credit { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }
    .kpi-inventory { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
    .kpi-discount { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; }
    .kpi-caja { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); color: white; }

    .kpi-main-card {
        border-radius: 14px;
        padding: 16px 20px;
        border: none;
        box-shadow: 0 4px 10px -2px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .kpi-main-card:hover {
        transform: translateY(-5px) scale(1.02);
    }
    
    .kpi-main-card::after {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }

    .icon-wrapper-light {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        border-radius: 10px;
        padding: 10px;
        display: inline-flex;
    }

    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #475569;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
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
    <div class="portfolio-hero">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-radius: 12px; box-shadow: 0 6px 12px rgba(37, 99, 235, 0.25);">
                    <i data-lucide="bar-chart-3" style="color: white; width: 22px; height: 22px;"></i>
                </div>
                <div>
                    <h4 class="m-0 fw-bold" style="color: #0f172a; letter-spacing: -0.5px;">Contabilidad</h4>
                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">Rendimiento comercial y estado de tus cuentas</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <form method="GET" action="{{ route('portfolio.index') }}" id="periodForm">
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
            </div>
        </div>
    </div>

    <!-- 2. MÉTRICAS DEL PERIODO -->
    <div class="mb-4">
        <!-- Fila Superior (4 Columnas) -->
        <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-xl-4 mb-3">
            <!-- Efectivo en Caja -->
            <div class="col">
                <div class="kpi-main-card kpi-caja h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <p class="mb-0 text-white-50 fw-semibold fs-7 text-uppercase" style="letter-spacing: 0.5px;">Efectivo en Caja</p>
                            <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Contado + Abonos - Gastos)</small>
                            <h2 class="fw-bold mb-0 text-white" style="font-size: 1.6rem;">${{ number_format($efectivo_recibido, 0, ',', '.') }}</h2>
                        </div>
                        <div class="icon-wrapper-light">
                            <i data-lucide="wallet" style="color: white; width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                        <i data-lucide="activity" style="width: 14px; height: 14px;"></i> ${{ number_format($ventas_contado, 0, ',', '.') }} + ${{ number_format($abonos_reales, 0, ',', '.') }} - ${{ number_format($gastos_periodo, 0, ',', '.') }} = ${{ number_format($efectivo_recibido, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <!-- Venta Contado -->
            <div class="col">
                <div class="kpi-main-card kpi-sales h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <p class="mb-0 text-white-50 fw-semibold fs-7 text-uppercase" style="letter-spacing: 0.5px;">Venta Contado</p>
                            <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Ingresos directos)</small>
                            <h2 class="fw-bold mb-0 text-white" style="font-size: 1.6rem;">${{ number_format($ventas_contado, 0, ',', '.') }}</h2>
                        </div>
                        <div class="icon-wrapper-light">
                            <i data-lucide="banknote" style="color: white; width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                        <i data-lucide="trending-up" style="width: 14px; height: 14px;"></i> Efectivo / Transferencia
                    </div>
                </div>
            </div>

            <!-- Venta Crédito -->
            <div class="col">
                <div class="kpi-main-card kpi-inventory h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <p class="mb-0 text-white-50 fw-semibold fs-7 text-uppercase" style="letter-spacing: 0.5px;">Venta Crédito</p>
                            <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Créditos generados)</small>
                            <h2 class="fw-bold mb-0 text-white" style="font-size: 1.6rem;">${{ number_format($ventas_periodo - $ventas_contado, 0, ',', '.') }}</h2>
                        </div>
                        <div class="icon-wrapper-light">
                            <i data-lucide="credit-card" style="color: white; width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                        <i data-lucide="trending-up" style="width: 14px; height: 14px;"></i> Créditos creados en el periodo
                    </div>
                </div>
            </div>

            <!-- Abonos Recibidos -->
            <div class="col">
                <div class="kpi-main-card kpi-discount h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <p class="mb-0 text-white-50 fw-semibold fs-7 text-uppercase" style="letter-spacing: 0.5px;">Abonos Recibidos</p>
                            <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Abonos a deudas)</small>
                            <h2 class="fw-bold mb-0 text-white" style="font-size: 1.6rem;">${{ number_format($abonos_reales, 0, ',', '.') }}</h2>
                        </div>
                        <div class="icon-wrapper-light">
                            <i data-lucide="coins" style="color: white; width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                        <i data-lucide="activity" style="width: 14px; height: 14px;"></i> Pagos de créditos en el periodo
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila Inferior (2 Columnas) -->
        <div class="row g-3 row-cols-1 row-cols-md-2">
            <!-- Gastos Registrados -->
            <div class="col">
                <div class="kpi-main-card kpi-credit h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <p class="mb-0 text-white-50 fw-semibold fs-7 text-uppercase" style="letter-spacing: 0.5px;">Gastos Diarios</p>
                            <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Egresos registrados)</small>
                            <h2 class="fw-bold mb-0 text-white" style="font-size: 1.6rem;">${{ number_format($gastos_periodo, 0, ',', '.') }}</h2>
                        </div>
                        <div class="icon-wrapper-light">
                            <i data-lucide="calculator" style="color: white; width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                        <i data-lucide="trending-down" style="width: 14px; height: 14px;"></i> Egresos del periodo
                    </div>
                </div>
            </div>

            <!-- Utilidad Generada -->
            <div class="col">
                <div class="kpi-main-card kpi-profit h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <p class="mb-0 text-white-50 fw-semibold fs-7 text-uppercase" style="letter-spacing: 0.5px;">Utilidad Generada</p>
                            <small class="text-white-50 d-block mb-1" style="font-size: 0.65rem; font-weight: 500;">(Ganancia neta estimada)</small>
                            <h2 class="fw-bold mb-0 text-white" style="font-size: 1.6rem;">${{ number_format($utilidad_periodo, 0, ',', '.') }}</h2>
                        </div>
                        <div class="icon-wrapper-light">
                            <i data-lucide="pie-chart" style="color: white; width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 text-white-50 mt-1 pt-1" style="font-size: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.15);">
                        <i data-lucide="trending-up" style="width: 14px; height: 14px;"></i> Ganancia neta periodo
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 3. TRAZABILIDAD VISUAL -->
    <div class="mb-4">
        <h5 class="section-title">
            <i data-lucide="calculator" style="color: #6366f1;"></i> Facturación del Periodo
        </h5>
        <div class="glass-card p-3">
            <div class="row justify-content-center text-center">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="py-3 px-3 rounded-3" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.15); border: none;">
                        <p class="text-white-50 fw-bold mb-0 fs-7 text-uppercase" style="letter-spacing: 0.5px;">Total Facturado</p>
                        <p class="fw-bold mb-0 fs-4 text-white">${{ number_format($ventas_periodo, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <span class="d-inline-flex align-items-center rounded-pill px-4 py-2" style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; font-size: 0.85rem;">
                    <i data-lucide="info" style="width: 16px; height: 16px; margin-right: 6px;"></i>
                    Este es el valor total facturado (contado y crédito) durante el periodo seleccionado.
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- 4. ESTADO DE CARTERA -->
        <div class="col-12 col-xl-6">
            <h5 class="section-title">
                <i data-lucide="users" style="color: #f59e0b;"></i> Estado de la Cartera
            </h5>
            <div class="glass-card p-4 h-100">
                <!-- Mini cards grid -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="p-3 rounded-3 text-center" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid #fecaca;">
                            <div class="d-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px; background: white; border-radius: 12px; margin: 0 auto; box-shadow: 0 2px 8px rgba(220,38,38,0.15);">
                                <i data-lucide="alert-triangle" style="color: #dc2626; width: 22px;"></i>
                            </div>
                            <h3 class="fw-bold mb-1" style="color: #dc2626;">{{ $estado_creditos['vencidas'] }}</h3>
                            <p class="mb-0 fw-semibold" style="color: #991b1b; font-size: 0.8rem;">Facturas Vencidas</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 text-center" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a;">
                            <div class="d-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px; background: white; border-radius: 12px; margin: 0 auto; box-shadow: 0 2px 8px rgba(217,119,6,0.15);">
                                <i data-lucide="clock" style="color: #d97706; width: 22px;"></i>
                            </div>
                            <h3 class="fw-bold mb-1" style="color: #d97706;">{{ $estado_creditos['pendientes'] }}</h3>
                            <p class="mb-0 fw-semibold" style="color: #92400e; font-size: 0.8rem;">Pendientes sin Abono</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 text-center" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe;">
                            <div class="d-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px; background: white; border-radius: 12px; margin: 0 auto; box-shadow: 0 2px 8px rgba(37,99,235,0.15);">
                                <i data-lucide="pie-chart" style="color: #2563eb; width: 22px;"></i>
                            </div>
                            <h3 class="fw-bold mb-1" style="color: #2563eb;">{{ $estado_creditos['parciales'] }}</h3>
                            <p class="mb-0 fw-semibold" style="color: #1e40af; font-size: 0.8rem;">Con Abonos Parciales</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 text-center" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0;">
                            <div class="d-flex align-items-center justify-content-center mb-2" style="width: 44px; height: 44px; background: white; border-radius: 12px; margin: 0 auto; box-shadow: 0 2px 8px rgba(22,163,74,0.15);">
                                <i data-lucide="check-circle" style="color: #16a34a; width: 22px;"></i>
                            </div>
                            <h3 class="fw-bold mb-1" style="color: #16a34a;">{{ $estado_creditos['pagadas'] }}</h3>
                            <p class="mb-0 fw-semibold" style="color: #166534; font-size: 0.8rem;">Créditos Pagados</p>
                        </div>
                    </div>
                </div>
                
                <!-- Resumen financiero de cartera -->
                <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-semibold fs-7">Total en créditos vendidos</span>
                        <span class="fw-bold" style="color: #334155;">${{ number_format($cartera['total_creditos'], 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fw-semibold fs-7">Total abonado históricamente</span>
                        <span class="fw-bold" style="color: #16a34a;">${{ number_format($cartera['total_abonos'], 0, ',', '.') }}</span>
                    </div>
                    <hr class="my-2" style="border-color: #cbd5e1;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="color: #0f172a;">Saldo pendiente total</span>
                        <span class="fw-bold fs-5" style="color: #d97706;">${{ number_format($cartera['total_pendiente'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. CAPITAL ESTÁTICO (INVENTARIO) -->
        <div class="col-12 col-xl-6">
            <h5 class="section-title">
                <i data-lucide="package" style="color: #6366f1;"></i> Tu Bodega (Inventario Actual)
            </h5>
            <div class="glass-card p-4 h-100">
                <!-- Inventory mini-cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border: 1px solid #cbd5e1;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                    <i data-lucide="box" style="color: #475569; width: 18px;"></i>
                                </div>
                                <span class="fw-semibold" style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Productos</span>
                            </div>
                            <h3 class="fw-bold mb-0" style="color: #0f172a;">{{ number_format($inventario->total_productos, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border: 1px solid #cbd5e1;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                    <i data-lucide="layers" style="color: #475569; width: 18px;"></i>
                                </div>
                                <span class="fw-semibold" style="color: #64748b; font-size: 0.75rem; text-transform: uppercase;">Unidades</span>
                            </div>
                            <h3 class="fw-bold mb-0" style="color: #0f172a;">{{ number_format($inventario->total_unidades, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Capital breakdown -->
                <div class="p-4 rounded-3 mb-3" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i data-lucide="wallet" style="color: #0284c7; width: 18px;"></i>
                        <span class="fw-bold text-uppercase" style="color: #0369a1; font-size: 0.8rem;">Inversión en Mercancía</span>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #0c4a6e;">${{ number_format($inventario->valor_total, 0, ',', '.') }}</h3>
                    <p class="mb-0" style="color: #0369a1; font-size: 0.8rem;">Lo que costó comprar todo lo que tienes en bodega.</p>
                </div>

                <div class="p-4 rounded-3 mb-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i data-lucide="trending-up" style="color: #16a34a; width: 18px;"></i>
                        <span class="fw-bold text-uppercase" style="color: #15803d; font-size: 0.8rem;">Ganancia Potencial</span>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #14532d;">${{ number_format($inventario->utilidad_potencial, 0, ',', '.') }}</h3>
                    <p class="mb-0" style="color: #15803d; font-size: 0.8rem;">Lo que ganarás de utilidad pura al venderlo todo.</p>
                </div>

                <div class="p-4 rounded-3" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border: 1px solid #a5b4fc;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i data-lucide="landmark" style="color: #4f46e5; width: 18px;"></i>
                        <span class="fw-bold text-uppercase" style="color: #4338ca; font-size: 0.8rem;">Precio de Venta Total</span>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #312e81;">${{ number_format($inventario->valor_total + $inventario->utilidad_potencial, 0, ',', '.') }}</h3>
                    <p class="mb-0" style="color: #4338ca; font-size: 0.8rem;">Inversión + Ganancia = Capital total de tu bodega.</p>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
