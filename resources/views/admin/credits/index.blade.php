@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0 mx-auto" style="max-width: 1300px;">
    
    @if ($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.85rem; border-radius: 8px;">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0 fw-bold" style="color: #111827;">Créditos &bull; Cartera</h5>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row g-2 mb-3">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 10px; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid #fca5a5 !important;">
                <div class="card-body p-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase fw-bold d-block" style="color: #991b1b; font-size: 0.65rem; letter-spacing: 0.4px;">
                            @if(request('search'))
                                Total Saldo Pendiente (Cliente: "{{ request('search') }}")
                            @else
                                Total Saldo Pendiente (Créditos Activos)
                            @endif
                        </span>
                        <h5 class="fw-bolder mb-0 mt-1" style="color: #7f1d1d; font-size: 1.15rem;">${{ number_format($totalPendingBalance ?? 0, 0, ',', '.') }}</h5>
                    </div>
                    <div style="background: #ffffff; padding: 6px 8px; border-radius: 8px; color: #dc2626;" class="shadow-sm">
                        <i data-lucide="circle-dollar-sign" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 10px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe !important;">
                <div class="card-body p-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase fw-bold d-block" style="color: #1e40af; font-size: 0.65rem; letter-spacing: 0.4px;">Facturas Pendientes de Cobro</span>
                        <h5 class="fw-bolder mb-0 mt-1" style="color: #1e3a8a; font-size: 1.15rem;">{{ $totalPendingCount ?? 0 }} <small style="font-size: 0.75rem; font-weight: 500;">factura(s)</small></h5>
                    </div>
                    <div style="background: #ffffff; padding: 6px 8px; border-radius: 8px; color: #2563eb;" class="shadow-sm">
                        <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
        <div class="card-body p-3">
            <form action="{{ route('credits.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-12">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; background: #fff; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-transparent border-0" style="color: #94a3b8; padding-right: 8px;">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 ps-0 shadow-none auto-submit" placeholder="Buscar por número de crédito o nombre de cliente..." value="{{ request('search') }}" style="font-size: 0.95rem; background: transparent;">
                        @if(request('search'))
                            <a href="{{ route('credits.index') }}" class="input-group-text bg-transparent border-0 text-danger text-decoration-none" title="Limpiar Búsqueda">
                                <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
        <!-- Card Header -->
        <div class="card-header bg-transparent py-2 px-3 d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: 1px solid #f3f4f6 !important;">
            <div class="d-flex align-items-center gap-2">
                <div style="background: #eff6ff; padding: 8px; border-radius: 8px; border: 1px solid #bfdbfe;">
                    <i data-lucide="credit-card" style="color: #2563eb; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color: #111827 !important; letter-spacing: -0.3px; font-size: 0.95rem;">Créditos y Cuentas por Cobrar</h6>
                    <p class="mb-0 fs-7 d-flex align-items-center gap-2" style="color: #6b7280 !important;">
                        <span style="width: 5px; height: 5px; background: #f59e0b; border-radius: 50%;"></span>
                        Gestión y control de facturas pendientes de cobro
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                <a href="{{ route('credits.export') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #10b981; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(16, 185, 129, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-spreadsheet" style="width: 14px; height: 14px;"></i> Exportar Excel
                </a>
                <a href="{{ route('credits.export.pdf') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #ef4444; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(220, 38, 38, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Exportar PDF
                </a>
                <a href="{{ route('credits.overdue') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; height: 32px; font-size: 0.8rem; transition: all 0.2s;">
                    <i data-lucide="alert-triangle" style="width: 14px; height: 14px;"></i> Créditos Vencidos
                </a>
                <a href="{{ route('credits.paid') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #e0f2fe; border: 1px solid #bae6fd; color: #0369a1; height: 32px; font-size: 0.8rem; transition: all 0.2s;">
                    <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Créditos Pagados
                </a>
                <a href="{{ route('sales.create', ['type' => 'credit']) }}" class="btn btn-sm btn-primary d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #2563eb; border: none; box-shadow: 0 2px 4px -1px rgba(37, 99, 235, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Nuevo crédito
                </a>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless mb-0 align-middle" style="color: #374151;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <th class="fw-semibold text-uppercase ps-3 py-3 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 40px;">#</th>
                            <th class="fw-semibold text-uppercase py-3" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">NO. CRÉDITO</th>
                            <th class="fw-semibold text-uppercase py-3" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">CLIENTE</th>
                            <th class="fw-semibold text-uppercase py-3 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">FECHA VENC.</th>
                            <th class="fw-semibold text-uppercase py-3 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">TIEMPO REST.</th>
                            <th class="fw-semibold text-uppercase py-3 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">DESCUENTOS</th>
                            <th class="fw-semibold text-uppercase py-3 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">TOTAL</th>
                            <th class="fw-semibold text-uppercase py-3 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">ABONOS</th>
                            <th class="fw-semibold text-uppercase py-3 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">SALDO</th>
                            <th class="fw-semibold text-uppercase pe-3 py-3 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($credits as $credit)
                            <tr class="table-row-hover" style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.2s;">
                                <td class="ps-4 py-2 text-center text-secondary fw-semibold" style="font-size: 0.8rem;">
                                    {{ $credits->firstItem() ? $credits->firstItem() + $loop->index : $loop->iteration }}
                                </td>
                                <td class="py-2 fw-semibold text-primary" style="font-size: 0.8rem;">
                                    #CRD-{{ $credit->document_number }}
                                </td>
                                <td class="py-2">
                                    <div class="d-flex flex-column justify-content-center">
                                        <span class="fw-bold" style="color: #111827; font-size: 0.8rem;">{{ $credit->customer->name ?? 'Consumidor Final' }}</span>
                                    </div>
                                </td>
                                @php
                                    $dueDate = $credit->due_date ? \Carbon\Carbon::parse($credit->due_date)->startOfDay() : null;
                                    $today = \Carbon\Carbon::today();
                                    if ($dueDate) {
                                        $isOverdue = $dueDate->lessThan($today);
                                        $isToday = $dueDate->equalTo($today);
                                        
                                        if ($isOverdue) {
                                            $badgeBg = '#fee2e2'; $badgeColor = '#b91c1c'; $badgeBorder = '#fca5a5';
                                        } elseif ($isToday) {
                                            $badgeBg = '#fef3c7'; $badgeColor = '#d97706'; $badgeBorder = '#fde68a';
                                        } else {
                                            $badgeBg = '#d1fae5'; $badgeColor = '#047857'; $badgeBorder = '#6ee7b7';
                                        }
                                        $timeText = $dueDate->locale('es')->diffForHumans(['options' => \Carbon\Constants\DiffOptions::JUST_NOW | \Carbon\Constants\DiffOptions::ONE_DAY_WORDS]);
                                    }
                                @endphp
                                <td class="text-center py-2">
                                    <span class="fw-bold" style="font-size: 0.75rem; color: #4b5563;">{{ $dueDate ? $dueDate->format('d/m/Y') : 'N/A' }}</span>
                                </td>
                                <td class="text-center py-2">
                                    @if($dueDate)
                                        <span class="badge shadow-sm" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }}; padding: 3px 6px; font-weight: 600; font-size: 0.65rem; border-radius: 4px;">
                                            {{ ucfirst($timeText) }}
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.75rem;">N/A</span>
                                    @endif
                                </td>
                                @php
                                    $totalDiscount = $credit->details->sum(function($detail) {
                                        return ($detail->unit_price * $detail->quantity) - $detail->subtotal;
                                    });
                                @endphp
                                <td class="fw-bold py-2 text-end text-danger" style="font-size: 0.8rem;">
                                    ${{ number_format($totalDiscount, 0, ',', '.') }}
                                </td>
                                <td class="fw-bold py-2 text-end" style="font-size: 0.8rem; color: #1f2937;">
                                    ${{ number_format($credit->total, 0, ',', '.') }}
                                </td>
                                <td class="fw-bold py-2 text-end" style="font-size: 0.8rem; color: #10b981;">
                                    ${{ number_format($credit->total - $credit->pending_balance, 0, ',', '.') }}
                                </td>
                                <td class="fw-bold py-2 text-end text-danger" style="font-size: 0.8rem;">
                                    ${{ number_format($credit->pending_balance, 0, ',', '.') }}
                                </td>
                                <td class="text-center pe-3 py-1">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <!-- Abonar Button -->
                                        <button type="button" class="btn btn-sm d-inline-flex align-items-center justify-content-center gap-1 px-3 shadow-sm" style="background: #d1fae5; border: 1px solid #6ee7b7; color: #047857; border-radius: 20px; height: 26px; font-size: 0.75rem; font-weight: 600; transition: all 0.2s;" title="Registrar Abono" data-bs-toggle="modal" data-bs-target="#modalPaymentCreate{{ $credit->id }}">
                                            <i data-lucide="hand-coins" style="width: 14px; height: 14px;"></i> Abonar
                                        </button>
                                        
                                        <!-- Divider -->
                                        <div style="width: 1px; height: 18px; background-color: #cbd5e1; border-radius: 1px;"></div>
                                        
                                        <!-- Standard Actions Group -->
                                        <div class="d-flex align-items-center gap-1">
                                            <!-- View Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #f3f4f6; border: 1px solid #e5e7eb;" title="Ver" data-bs-toggle="modal" data-bs-target="#modalCredit{{ $credit->id }}">
                                            <i data-lucide="eye" style="color: #4b5563; width: 12px; height: 12px;"></i>
                                        </button>
                                            <!-- Download PDF Button -->
                                            <a href="{{ route('credits.download.pdf', $credit) }}" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #dbeafe; border: 1px solid #bfdbfe;" title="Descargar PDF" target="_blank">
                                                <i data-lucide="file-text" style="color: #2563eb; width: 12px; height: 12px;"></i>
                                            </a>
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fef3c7; border: 1px solid #fde68a;" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditSale{{ $credit->id }}">
                                                <i data-lucide="edit-2" style="color: #d97706; width: 12px; height: 12px;"></i>
                                            </button>
                                            <!-- Delete Button -->
                                            <form action="{{ route('sales.destroy', $credit) }}" method="POST" class="d-inline" onsubmit="confirmDelete(event, '¿Estás seguro de que deseas eliminar este crédito?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fee2e2; border: 1px solid #fecaca;" title="Eliminar">
                                                    <i data-lucide="trash-2" style="color: #ef4444; width: 12px; height: 12px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            @empty
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td colspan="7" class="text-center py-5 text-secondary" style="color: #6b7280; font-size: 0.85rem;">
                                    <i data-lucide="credit-card" class="mb-3 d-block mx-auto" style="width: 48px; height: 48px; opacity: 0.5; color: #9ca3af;"></i>
                                    <p class="mb-0">Aún no hay créditos registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($credits->hasPages())
        <div class="card-footer bg-transparent py-3 d-flex align-items-center justify-content-between custom-pagination-container" style="border-top: 1px solid #f3f4f6;">
            {{ $credits->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

@foreach($credits as $credit)
<!-- Credit Modal -->
                            <div class="modal fade" id="modalCredit{{ $credit->id }}" tabindex="-1" aria-labelledby="modalCreditLabel{{ $credit->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                                        <div class="modal-header border-0 pb-0 pt-3 px-3">
                                            <h5 class="modal-title fw-bold" id="modalCreditLabel{{ $credit->id }}" style="color: #0f172a; font-size: 1.1rem;">Detalles del Crédito</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-3">
                                            <div class="row g-2">
                                                <!-- Left Column -->
                                                <div class="col-sm-6 d-flex flex-column gap-2">
                                                    <!-- Card 1: Información General -->
                                                    <div class="card border-0" style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0 !important;">
                                                        <div class="card-body p-2">
                                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                                <div style="background: #ffffff; padding: 4px; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                                                    <i data-lucide="box" style="color: #3b82f6; width: 14px; height: 14px;"></i>
                                                                </div>
                                                                <h6 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.9rem;">Información General</h6>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1 pb-1 border-bottom" style="border-color: #e2e8f0 !important;">
                                                                <span class="fw-semibold" style="color: #64748b; font-size: 0.8rem;">Cliente</span>
                                                                <span class="fw-bold text-end" style="color: #0f172a; font-size: 0.8rem;">{{ $credit->customer->name ?? 'Consumidor Final' }}</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1 pb-1 border-bottom" style="border-color: #e2e8f0 !important;">
                                                                <span class="fw-semibold" style="color: #64748b; font-size: 0.8rem;">No. Crédito</span>
                                                                <span class="fw-bold text-end" style="color: #eab308; font-size: 0.8rem;">#CRD-{{ $credit->document_number }}</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between">
                                                                <span class="fw-semibold" style="color: #64748b; font-size: 0.8rem;">Vencimiento</span>
                                                                <span class="fw-bold text-end" style="color: #dc2626; font-size: 0.8rem;">{{ $credit->due_date ? \Carbon\Carbon::parse($credit->due_date)->format('d M Y') : 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Card 3: Análisis Financiero -->
                                                    <div class="card border-0" style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0 !important;">
                                                        <div class="card-body p-2">
                                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                                <div style="background: #ffffff; padding: 4px; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                                                    <i data-lucide="tag" style="color: #10b981; width: 14px; height: 14px;"></i>
                                                                </div>
                                                                <h6 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.9rem;">Resumen Financiero</h6>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between mb-1 pb-1 border-bottom" style="border-color: #e2e8f0 !important;">
                                                                <span class="fw-semibold" style="color: #64748b; font-size: 0.8rem;">Total Facturado</span>
                                                                <span class="fw-bold text-end" style="color: #0f172a; font-size: 0.8rem;">${{ number_format($credit->total, 0, ',', '.') }}</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span class="fw-semibold" style="color: #64748b; font-size: 0.8rem;">Abono Inicial</span>
                                                                <span class="fw-bold text-end" style="color: #10b981; font-size: 0.8rem;">${{ number_format($credit->initial_payment, 0, ',', '.') }}</span>
                                                            </div>
                                                            
                                                            <div class="p-2 mb-0" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                                                                <span class="fw-bold text-danger" style="font-size: 0.75rem; letter-spacing: 0.5px;">SALDO PENDIENTE</span>
                                                                <span class="fw-bold text-danger" style="font-size: 1rem;">${{ number_format($credit->pending_balance, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Right Column -->
                                                <div class="col-sm-6 mt-0">
                                                    <!-- Card 2: Productos -->
                                                    <div class="card border-0 h-100" style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0 !important;">
                                                        <div class="card-body p-2 d-flex flex-column h-100">
                                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                                <div style="background: #ffffff; padding: 4px; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                                                    <i data-lucide="shopping-bag" style="color: #f59e0b; width: 14px; height: 14px;"></i>
                                                                </div>
                                                                <h6 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.9rem;">Productos Adquiridos</h6>
                                                            </div>
                                                            <div style="max-height: 180px; overflow-y: auto; padding-right: 5px; flex-grow: 1;">
                                                                @forelse($credit->details as $detail)
                                                                <div class="d-flex justify-content-between align-items-center mb-1 pb-1 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color: #e2e8f0 !important;">
                                                                    <div>
                                                                        <div class="fw-bold" style="color: #0f172a; font-size: 0.8rem;">{{ $detail->product->name ?? 'Producto Eliminado' }}</div>
                                                                        <div style="color: #64748b; font-size: 0.7rem;">{{ $detail->quantity }} x ${{ number_format($detail->unit_price, 0, ',', '.') }}</div>
                                                                    </div>
                                                                    <div class="fw-bold" style="color: #0f172a; font-size: 0.8rem;">${{ number_format($detail->subtotal, 0, ',', '.') }}</div>
                                                                </div>
                                                                @empty
                                                                <div class="text-center py-2" style="color: #94a3b8; font-size: 0.75rem; font-style: italic;">Detalle de productos no disponible para este registro antiguo.</div>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 pb-3 px-3 d-flex gap-2">
                                            <a href="{{ route('credits.download.pdf', $credit) }}" class="btn btn-danger fw-bold py-2 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-1" target="_blank" style="border-radius: 8px; font-size: 0.9rem; border: none; box-shadow: 0 2px 4px -1px rgba(220, 38, 38, 0.2); background: #ef4444; color: white;">
                                                <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Descargar PDF
                                            </a>
                                            <button type="button" class="btn btn-primary fw-bold py-2 flex-grow-1" data-bs-dismiss="modal" style="border-radius: 8px; background: #3b82f6; border: none; font-size: 0.9rem;">Entendido</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Modal -->
                            <div class="modal fade" id="modalPaymentCreate{{ $credit->id }}" tabindex="-1" aria-labelledby="modalPaymentCreateLabel{{ $credit->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                                            <h5 class="modal-title fw-bold" id="modalPaymentCreateLabel{{ $credit->id }}" style="color: #0f172a;">Registrar Abono</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('payments.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="sale_id" value="{{ $credit->id }}">
                                            <div class="modal-body p-4">
                                                <div class="mb-3 p-3 text-center" style="background: #fef2f2; border: 1px dashed #fecaca; border-radius: 8px;">
                                                    <span style="color: #ef4444; font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 4px;">SALDO PENDIENTE ACTUAL</span>
                                                    <span style="color: #dc2626; font-size: 1.5rem; font-weight: 800;">${{ number_format($credit->pending_balance, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="amount{{ $credit->id }}" class="form-label fw-bold" style="color: #475569; font-size: 0.9rem;">Monto a Abonar ($)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border-end-0"><i data-lucide="dollar-sign" style="width: 16px; height: 16px;"></i></span>
                                                        <input type="text" class="form-control format-number border-start-0 ps-0" id="amount{{ $credit->id }}" name="amount" required placeholder="Ej. 10.000" style="box-shadow: none;">
                                                    </div>
                                                    <div class="form-text mt-2 text-muted" style="font-size: 0.8rem;">
                                                        <i data-lucide="info" style="width: 12px; height: 12px; display: inline-block; margin-bottom: 2px;"></i> Si abonas el total, este crédito se convertirá automáticamente en una Factura Pagada.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex gap-2">
                                                <button type="button" class="btn btn-light fw-bold flex-grow-1" data-bs-dismiss="modal" style="border-radius: 8px;">Cancelar</button>
                                                <button type="submit" class="btn btn-success fw-bold flex-grow-1" style="border-radius: 8px; background: #10b981; border: none;">Guardar Abono</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="modalEditSale{{ $credit->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                                            <h5 class="modal-title fw-bold" style="color: #0f172a;">Editar Documento #{{ $credit->document_number }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sales.update', $credit->id) }}" method="POST" class="edit-credit-form">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="invoice_type" value="{{ $credit->invoice_type }}">
                                            <input type="hidden" id="totalPaymentsMade-{{ $credit->id }}" value="{{ \App\Models\Payment::where('sale_id', $credit->id)->sum('amount') }}">
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <!-- Info General -->
                                                    <div class="col-lg-4">
                                                        <div class="p-2 rounded-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                                                            <h6 class="text-uppercase fw-bold mb-2" style="font-size: 0.65rem; color: #6b7280;">👤 Datos del Cliente</h6>
                                                            <div class="mb-2 position-relative">
                                                                <input type="text" class="form-control form-control-sm client-autocomplete-edit-field" name="customer_name" value="{{ $credit->customer->name ?? '' }}" placeholder="Nombre" autocomplete="off" required>
                                                            </div>
                                                            <div class="mb-2 position-relative">
                                                                <input type="text" class="form-control form-control-sm client-autocomplete-edit-field" name="customer_cedula" value="{{ $credit->customer->document_number ?? '' }}" placeholder="Cédula/NIT" autocomplete="off">
                                                            </div>
                                                            <div class="mb-2 position-relative">
                                                                <input type="text" class="form-control form-control-sm client-autocomplete-edit-field" name="customer_phone" value="{{ $credit->customer->phone ?? '' }}" placeholder="Teléfono" autocomplete="off">
                                                            </div>
                                                            <div class="mb-2 position-relative">
                                                                <input type="text" class="form-control form-control-sm client-autocomplete-edit-field" name="customer_address" value="{{ $credit->customer->address ?? '' }}" placeholder="Dirección" autocomplete="off">
                                                            </div>
                                                            <div class="mb-2 mt-3"><label class="form-label fw-bold" style="font-size: 0.75rem;">Fecha Vencimiento</label><input type="date" class="form-control form-control-sm" name="due_date" value="{{ $credit->due_date ? \Carbon\Carbon::parse($credit->due_date)->format('Y-m-d') : '' }}" required></div>
                                                        </div>
                                                        <div class="p-2 rounded-3 mt-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                                                            <h6 class="text-uppercase fw-bold mb-2" style="font-size: 0.65rem; color: #6b7280;">💰 Resumen</h6>
                                                            <div class="d-flex justify-content-between mb-1" style="font-size: 0.75rem;">
                                                                <span>Subtotal:</span>
                                                                <span class="font-monospace">$<span id="subtotalPreview-{{ $credit->id }}">0</span></span>
                                                            </div>
                                                            <div class="d-flex justify-content-between text-danger mb-1" style="font-size: 0.75rem;">
                                                                <span>Descuento:</span>
                                                                <span class="font-monospace">-$<span id="discountPreview-{{ $credit->id }}">0</span></span>
                                                            </div>
                                                            <div class="d-flex justify-content-between fw-bold pt-1 border-top" style="font-size: 0.9rem;">
                                                                <span>TOTAL:</span>
                                                                <span class="font-monospace">$<span id="totalPreview-{{ $credit->id }}">0</span></span>
                                                            </div>
                                                            <div class="d-flex justify-content-between text-warning fw-bold pt-1 border-top mt-1" style="font-size: 0.9rem;"><span>PENDIENTE:</span><span class="font-monospace">$<span id="balancePreview-{{ $credit->id }}">0</span></span></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Productos -->
                                                    <div class="col-lg-8">
                                                        <div class="p-2 rounded-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <h6 class="text-uppercase fw-bold mb-0" style="font-size: 0.65rem; color: #6b7280;">🛒 Detalle de Venta</h6>
                                                                <button type="button" onclick="addRowEdit({{ $credit->id }})" class="btn btn-sm btn-primary py-1" style="font-size: 0.7rem;">+ Añadir</button>
                                                            </div>
                                                            <div class="row g-1 d-none d-lg-flex mb-1 px-1 fw-semibold text-uppercase" style="font-size: 0.6rem; color: #6b7280;">
                                                                <div class="col-4">Producto</div>
                                                                <div class="col-2 text-center">Cant.</div>
                                                                <div class="col-3 text-center">Precio U.</div>
                                                                <div class="col-2 text-center">Desc %</div>
                                                                <div class="col-1"></div>
                                                            </div>
                                                            <div id="items-{{ $credit->id }}" class="d-flex flex-column gap-1"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex gap-2">
                                                <button type="button" class="btn btn-light fw-bold flex-grow-1" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary fw-bold flex-grow-1">Guardar Factura Completa</button>
                                            </div>
                                        </form>
                                        <script>
                                            document.getElementById('modalEditSale{{ $credit->id }}').addEventListener('show.bs.modal', function () {
                                                const container = document.getElementById('items-{{ $credit->id }}');
                                                if(container.children.length === 0) {
                                                    const details = @json($credit->details);
                                                    if(details.length > 0) {
                                                        details.forEach(d => addRowEdit({{ $credit->id }}, d));
                                                    } else {
                                                        addRowEdit({{ $credit->id }});
                                                    }
                                                    setTimeout(() => calcEdit({{ $credit->id }}), 100);
                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
@endforeach

<script>
const appProducts = @json($products);

function addRowEdit(saleId, detail = null) {
    const itemsContainer = document.getElementById(`items-${saleId}`);
    const index = itemsContainer.children.length;
    const rowId = `row-${saleId}-${index}`;
    
    let options = `<option value="" data-price="0">-- Producto --</option>`;
    appProducts.forEach(p => {
        let selected = (detail && detail.product_id == p.id) ? 'selected' : '';
        options += `<option value="${p.id}" data-price="${p.price || 0}" data-stock="${p.stock || 0}" ${selected}>${p.name} (Stock: ${p.stock})</option>`;
    });

    let qty = 1;
    let price = '';
    let disc = 0;

    if (detail) {
        qty = detail.quantity;
        price = detail.unit_price;
        const originalTotal = qty * price;
        if (originalTotal > 0 && detail.subtotal < originalTotal) {
            disc = ((originalTotal - detail.subtotal) / originalTotal) * 100;
        }
    }

    const html = `
        <div id="${rowId}" class="row g-1 align-items-center bg-white p-1 rounded shadow-sm mx-0 mb-1" style="border: 1px solid #e5e7eb;">
            <div class="col-12 col-lg-4">
                <select name="products[${index}][product_id]" onchange="updateRowPriceEdit(this, ${saleId})" class="form-select form-select-sm" style="font-size: 0.75rem; border-color: #d1d5db; min-height: 28px;" required>
                    ${options}
                </select>
            </div>
            <div class="col-4 col-lg-2">
                <input type="number" name="products[${index}][quantity]" class="quantity form-control form-control-sm text-center fw-bold" style="font-size: 0.75rem; border-color: #d1d5db; min-height: 28px;" value="${qty}" min="1" oninput="calcEdit(${saleId})">
            </div>
            <div class="col-4 col-lg-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-success fw-bold py-0" style="border-color: #d1d5db; border-right: none;">$</span>
                    <input type="number" name="products[${index}][price]" class="price-input form-control form-control-sm text-success fw-bold font-monospace ps-0" style="font-size: 0.75rem; border-color: #d1d5db; border-left: none; min-height: 28px;" step="0.01" value="${price}" oninput="calcEdit(${saleId})" required>
                </div>
            </div>
            <div class="col-3 col-lg-2">
                <div class="input-group input-group-sm">
                    <input type="number" name="products[${index}][discount]" class="discount-item form-control form-control-sm text-danger text-center fw-bold" style="font-size: 0.75rem; border-color: #d1d5db; min-height: 28px;" value="${disc.toFixed(2)}" min="0" max="100" oninput="calcEdit(${saleId})">
                    <span class="input-group-text bg-white px-1 text-muted py-0" style="border-color: #d1d5db;">%</span>
                </div>
            </div>
            <div class="col-1 d-flex justify-content-end justify-content-lg-center">
                <button type="button" onclick="document.getElementById('${rowId}').remove(); calcEdit(${saleId})" class="btn btn-sm p-0 d-flex align-items-center justify-content-center" style="background: #fee2e2; color: #ef4444; border: none; width: 22px; height: 22px; border-radius: 4px;">
                    <i data-lucide="x" style="width: 12px; height: 12px;"></i>
                </button>
            </div>
        </div>`;
    
    itemsContainer.insertAdjacentHTML('beforeend', html);
    if(typeof lucide !== 'undefined') lucide.createIcons();
}

function updateRowPriceEdit(select, saleId) {
    const selectedOption = select.options[select.selectedIndex];
    const precioVenta = selectedOption.getAttribute('data-price');
    const stock = selectedOption.getAttribute('data-stock');
    const row = select.closest('.row');
    const priceInput = row.querySelector('.price-input');
    const qtyInput = row.querySelector('.quantity');
    
    if (priceInput) priceInput.value = precioVenta;
    if (qtyInput && stock !== null) {
        qtyInput.max = stock;
        const stockInt = parseInt(stock, 10);
        if (stockInt === 0) {
            qtyInput.value = 0;
        } else {
            const currentQty = parseInt(qtyInput.value || 0, 10);
            if (currentQty <= 0 || currentQty > stockInt) {
                qtyInput.value = 1;
            }
        }
    }
    calcEdit(saleId); 
}

function calcEdit(saleId){
    let subtotal = 0;
    let totalDiscount = 0;
    
    const itemsContainer = document.getElementById(`items-${saleId}`);
    if(!itemsContainer) return;
    const rows = itemsContainer.querySelectorAll('div[id^="row-"]');
    
    rows.forEach(row => {
        const qtyInput = row.querySelector('.quantity');
        let qty = parseFloat(qtyInput.value || 0);
        const price = parseFloat(row.querySelector('.price-input').value || 0);
        const discPercent = parseFloat(row.querySelector('.discount-item').value || 0);

        const select = row.querySelector('select');
        const selectedOption = select.options[select.selectedIndex];
        const stock = parseInt(selectedOption.getAttribute('data-stock') || 999999, 10);
        
        if (selectedOption.value && qty > stock) {
            qty = stock;
            qtyInput.value = stock;
        }

        const rowSubtotal = qty * price;
        const rowDiscount = rowSubtotal * (discPercent / 100);

        subtotal += rowSubtotal;
        totalDiscount += rowDiscount;
    });

    const totalFinal = subtotal - totalDiscount;

    const subP = document.getElementById(`subtotalPreview-${saleId}`);
    const discP = document.getElementById(`discountPreview-${saleId}`);
    const totP = document.getElementById(`totalPreview-${saleId}`);
    
    if(subP) subP.textContent = subtotal.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2});
    if(discP) discP.textContent = totalDiscount.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2});
    if(totP) totP.textContent = totalFinal.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2});
    
    const balancePreview = document.getElementById(`balancePreview-${saleId}`);
    if (balancePreview) {
        const abonoInput = document.getElementById(`paidInput-${saleId}`);
        const totalPayInput = document.getElementById(`totalPaymentsMade-${saleId}`);
        let abono = 0;
        if(abonoInput) abono = parseFloat(abonoInput.value || 0);
        else if(totalPayInput) abono = parseFloat(totalPayInput.value || 0);
        
        const pendiente = totalFinal - abono;
        balancePreview.textContent = pendiente.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2});
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Formato de miles con puntos al escribir
    document.querySelectorAll('.format-number').forEach(function(input) {
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value !== '') {
                this.value = parseInt(value, 10).toLocaleString('es-CO').replace(/,/g, '.');
            } else {
                this.value = '';
            }
        });
    });

    // Autocomplete para campos de cliente en modales de edicion
    let resultsBoxEdit = document.getElementById('clientResultsEdit');
    if (!resultsBoxEdit) {
        resultsBoxEdit = document.createElement('div');
        resultsBoxEdit.id = 'clientResultsEdit';
        resultsBoxEdit.className = 'd-none position-absolute w-100 mt-1 bg-white border rounded shadow z-3';
        resultsBoxEdit.style.maxHeight = '200px';
        resultsBoxEdit.style.overflowY = 'auto';
        document.body.appendChild(resultsBoxEdit);
    }

    let activeEditForm = null;

    document.querySelectorAll('.client-autocomplete-edit-field').forEach(input => {
        input.addEventListener('input', async () => {
            const q = input.value.trim();
            if (q.length < 2) { 
                resultsBoxEdit.classList.add('d-none'); 
                return; 
            }
            
            activeEditForm = input.closest('form');
            input.parentNode.appendChild(resultsBoxEdit);
            
            try {
                const res = await fetch(`{{ route('clients.autocomplete') }}?q=${q}`);
                const data = await res.json();
                resultsBoxEdit.innerHTML = '';
                
                if (data.length === 0) {
                    resultsBoxEdit.classList.add('d-none');
                    return;
                }
                
                data.forEach(c => {
                    const item = document.createElement('div');
                    item.className = 'px-3 py-2 border-bottom';
                    item.style.cursor = 'pointer';
                    item.style.transition = 'background-color 0.2s';
                    item.innerHTML = `<strong class="text-dark" style="font-size: 0.75rem;">${c.name}</strong> <span class="text-muted ms-2" style="font-size: 0.7rem;">· ${c.cedula || 'Sin CC'}</span>`;
                    item.onmouseover = () => { item.style.backgroundColor = '#f9fafb'; };
                    item.onmouseout = () => { item.style.backgroundColor = 'transparent'; };
                    item.onclick = () => {
                        if (activeEditForm) {
                            const nameField = activeEditForm.querySelector('input[name="customer_name"]');
                            const cedulaField = activeEditForm.querySelector('input[name="customer_cedula"]');
                            const phoneField = activeEditForm.querySelector('input[name="customer_phone"]');
                            const addressField = activeEditForm.querySelector('input[name="customer_address"]');
                            
                            if (nameField) nameField.value = c.name;
                            if (cedulaField) cedulaField.value = c.cedula || '';
                            if (phoneField) phoneField.value = c.cellphone || '';
                            if (addressField) addressField.value = c.address || '';
                        }
                        resultsBoxEdit.classList.add('d-none');
                    };
                    resultsBoxEdit.appendChild(item);
                });
                resultsBoxEdit.classList.remove('d-none');
            } catch (e) { 
                console.error("Error clientes", e); 
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('client-autocomplete-edit-field') && e.target !== resultsBoxEdit && !resultsBoxEdit.contains(e.target)) {
            resultsBoxEdit.classList.add('d-none');
        }
    });

    // Validacion de formularios de credito (edicion)
    document.querySelectorAll('.edit-credit-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const client = this.querySelector('input[name="customer_name"]').value.trim();
            const dueDate = this.querySelector('input[name="due_date"]').value;
            const productSelects = this.querySelectorAll('select[name^="products"]');
            
            let hasEmptyProduct = false;
            productSelects.forEach(select => {
                if (!select.value) {
                    hasEmptyProduct = true;
                }
            });

            let missing = [];
            if (!client) missing.push("<strong>Falta el Cliente:</strong> Debes escribir y seleccionar el cliente para saber a quién se le genera el documento.");
            if (!dueDate) missing.push("<strong>Falta la Fecha de Vencimiento:</strong> Al ser una venta a crédito, es obligatorio indicar la fecha límite de pago para el control de cartera.");
            if (productSelects.length === 0) {
                missing.push("<strong>Falta agregar productos:</strong> Debes añadir al menos un artículo o servicio para poder calcular el total de la venta.");
            } else {
                productSelects.forEach(select => {
                    if (select.value) {
                        const selectedOption = select.options[select.selectedIndex];
                        const stock = parseInt(selectedOption.getAttribute('data-stock') || 0, 10);
                        const row = select.closest('.row');
                        const qtyInput = row.querySelector('.quantity');
                        const qty = parseInt(qtyInput.value || 0, 10);
                        const productName = selectedOption.text.split(' (Stock:')[0];
                        if (stock === 0) {
                            missing.push(`<strong>Producto agotado (${productName}):</strong> No hay unidades disponibles en inventario. No puedes facturar un producto sin existencias.`);
                        } else if (qty > stock) {
                            missing.push(`<strong>Stock insuficiente para ${productName}:</strong> Solo quedan ${stock} unidades disponibles en inventario e indicaste ${qty}.`);
                        }
                    }
                });
                if (hasEmptyProduct) {
                    missing.push("<strong>Falta seleccionar producto:</strong> Tienes filas agregadas vacías, selecciona un producto válido en la lista o elimina la fila si no la necesitas.");
                }
            }

            if (missing.length > 0) {
                e.preventDefault();
                e.stopPropagation();
                
                this.classList.remove('is-submitting');
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalHtml || 'Guardar Factura Completa';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR DE FORMULARIO',
                    html: `<div class="text-center mt-2" style="font-size: 0.95rem;">
                        <p class="mb-3"><strong>¡Ups! Te falta lo siguiente:</strong></p>
                        <div class="d-flex flex-column gap-2 text-danger fw-semibold">
                            ${missing.map(m => `<div>⚠️ ${m}</div>`).join('')}
                        </div>
                    </div>`,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
        });
    });
});
</script>
@endsection
