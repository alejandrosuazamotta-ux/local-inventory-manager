@extends('layouts.admin')

@section('content')
@push('styles')
    <style>
        .custom-pagination-container { width: 100%; }
        .custom-pagination-container nav { width: 100%; }
        .custom-pagination-container nav > div:not(.d-sm-none) { display: flex !important; justify-content: space-between !important; align-items: center !important; width: 100% !important; }
        .custom-pagination-container .pagination { margin-bottom: 0; gap: 6px; }
        .custom-pagination-container .page-link { padding: 0.4rem 0.8rem; font-size: 0.85rem; font-weight: 500; border-radius: 8px !important; color: #64748b; border: 1px solid transparent; background-color: transparent; transition: all 0.2s ease; }
        .custom-pagination-container .page-item:not(.active) .page-link:hover { background-color: #f1f5f9; color: #0f172a; }
        .custom-pagination-container .page-item.active .page-link { background-color: #3b82f6; border-color: #3b82f6; color: white; box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3); }
        .custom-pagination-container .page-item.disabled .page-link { color: #cbd5e1; background-color: transparent; }
        .custom-pagination-container p.small { font-size: 0.85rem !important; color: #64748b !important; margin-bottom: 0; padding-top: 0; }
    </style>
@endpush

<div class="container-fluid px-0 p-4">
    
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0 fw-bold" style="color: #111827;">Deudas a Proveedores &bull; Administración</h5>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
        <div class="card-body p-3">
            <form action="{{ route('supplier-debts.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-7">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; background: #fff; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-transparent border-0" style="color: #94a3b8; padding-right: 8px;">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 ps-0 shadow-none auto-submit" placeholder="Buscar por proveedor..." value="{{ request('search') }}" style="font-size: 0.95rem; background: transparent;">
                        @if(request('search'))
                            <a href="{{ route('supplier-debts.index', ['status' => request('status')]) }}" class="input-group-text bg-transparent border-0 text-danger text-decoration-none" title="Limpiar Búsqueda">
                                <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <div class="d-flex gap-2">
                        <select name="status" class="form-select shadow-sm border-0" style="font-size: 0.95rem; border-radius: 8px; border: 1px solid #e2e8f0 !important;" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="pagado" {{ request('status') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        </select>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('supplier-debts.index') }}" class="btn btn-light shadow-sm d-flex align-items-center justify-content-center gap-2" style="border-radius: 8px; border: 1px solid #e2e8f0; color: #ef4444; font-weight: 500; font-size: 0.9rem; white-space: nowrap; transition: all 0.2s;" title="Limpiar Filtros" onmouseover="this.style.backgroundColor='#fee2e2'" onmouseout="this.style.backgroundColor='#f8fafc'">
                                <i data-lucide="x-circle" style="width: 18px; height: 18px;"></i> Limpiar
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
        <!-- Card Header -->
        <div class="card-header bg-transparent py-3 px-4 d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: 1px solid #f3f4f6 !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: #eff6ff; border-radius: 10px; border: 1px solid #bfdbfe;">
                    <i data-lucide="truck" style="color: #2563eb; width: 22px; height: 22px;"></i>
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <h6 class="fw-bold mb-0" style="color: #1e293b; font-size: 1rem; letter-spacing: -0.2px;">Proveedores</h6>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span style="width: 6px; height: 6px; background: #10B981; border-radius: 50%; display: inline-block;"></span>
                        <span style="color: #64748b; font-size: 0.8rem; font-weight: 500;">Gestione deudas y abonos</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                <a href="{{ route('supplier-debts.export') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold shadow-sm" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 6px; padding: 0 12px; height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-spreadsheet" style="width: 14px; height: 14px;"></i> Excel
                </a>
                <a href="{{ route('supplier-debts.export.pdf') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #ef4444; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(220, 38, 38, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Exportar PDF
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#addDebtModal" class="btn btn-sm btn-primary d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #2563eb; border: none; box-shadow: 0 2px 4px -1px rgba(37, 99, 235, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Nueva Deuda
                </button>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless mb-0 align-middle" style="color: #374151;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <th class="fw-semibold text-uppercase ps-3 py-2" style="color: #6b7280; letter-spacing: 0.5px; width: 40px; font-size: 0.7rem;">#</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">PROVEEDOR</th>
                            <th class="fw-semibold text-uppercase py-2 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">DEUDA TOTAL</th>
                            <th class="fw-semibold text-uppercase py-2 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">PAGADO</th>
                            <th class="fw-semibold text-uppercase py-2 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">SALDO PENDIENTE</th>
                            <th class="fw-semibold text-uppercase py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">ESTADO</th>
                            <th class="fw-semibold text-uppercase pe-3 py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; width: 120px; font-size: 0.7rem;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debts as $index => $debt)
                            @php
                                $saldoPendiente = max(0, $debt->debt_amount - $debt->paid_amount);
                            @endphp
                            <tr class="table-row-hover" style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.2s;">
                                <td class="ps-3 py-1 text-secondary fw-semibold" style="font-size: 0.8rem;">{{ $index + 1 + ($debts->currentPage() - 1) * $debts->perPage() }}</td>
                                <td class="py-1">
                                    <span class="fw-bold" style="color: #111827; font-size: 0.85rem;">{{ $debt->name }}</span>
                                </td>
                                <td class="text-end fw-medium py-1" style="color: #4b5563; font-size: 0.85rem;">${{ number_format($debt->debt_amount, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold py-1" style="color: #059669; font-size: 0.85rem;">${{ number_format($debt->paid_amount, 0, ',', '.') }}</td>
                                <td class="text-end py-1">
                                    <span class="badge" style="background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; border-radius: 4px; padding: 4px 6px; font-weight: 500; font-size: 0.75rem;">
                                        ${{ number_format($saldoPendiente, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center py-1">
                                    @if($debt->status == 'pagado')
                                        <span class="badge" style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 9999px;">Pagado</span>
                                    @else
                                        <span class="badge" style="background: #fef9c3; color: #854d0e; padding: 4px 8px; border-radius: 9999px;">Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-center pe-3 py-1">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#showDebtModal{{ $debt->id }}" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #f3f4f6; border: 1px solid #e5e7eb;" title="Ver Detalles">
                                            <i data-lucide="eye" style="color: #4b5563; width: 12px; height: 12px;"></i>
                                        </button>

                                        @if($debt->status != 'pagado')
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#payDebtModal{{ $debt->id }}" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #eff6ff; border: 1px solid #bfdbfe;" title="Registrar Pago">
                                                <i data-lucide="dollar-sign" style="color: #2563eb; width: 12px; height: 12px;"></i>
                                            </button>
                                        @endif

                                        <button type="button" data-bs-toggle="modal" data-bs-target="#editDebtModal{{ $debt->id }}" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fef3c7; border: 1px solid #fde68a;" title="Editar Deuda">
                                            <i data-lucide="edit-2" style="color: #d97706; width: 12px; height: 12px;"></i>
                                        </button>

                                        <button type="button" data-bs-toggle="modal" data-bs-target="#deleteDebtModal{{ $debt->id }}" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fee2e2; border: 1px solid #fecaca;" title="Eliminar Deuda">
                                            <i data-lucide="trash-2" style="color: #ef4444; width: 12px; height: 12px;"></i>
                                        </button>
                                    </div>

                                    @if($debt->status != 'pagado')
                                    <!-- Modal Pago -->
                                    <div class="modal fade" id="payDebtModal{{ $debt->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form action="{{ route('supplier-debts.pay', $debt->id) }}" method="POST" class="modal-content text-start" style="border-radius: 12px; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                                                @csrf
                                                <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e5e7eb;">
                                                    <h5 class="modal-title fw-bold text-dark">Abonar Pago</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                                        <span class="text-muted fw-semibold">Proveedor:</span>
                                                        <span class="fw-bold">{{ $debt->name }}</span>
                                                    </div>
                                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                                        <span class="text-muted fw-semibold">Saldo Pendiente:</span>
                                                        <span class="text-danger fw-bold">${{ number_format($saldoPendiente, 0, ',', '.') }}</span>
                                                    </div>
                                                    <hr>
                                                    <div class="mb-3">
                                                        <label for="amount{{ $debt->id }}" class="form-label fw-semibold">Monto a Pagar <span class="text-danger">*</span></label>
                                                        <div class="input-group shadow-sm">
                                                            <span class="input-group-text bg-white"><i data-lucide="dollar-sign" style="width:14px;height:14px;"></i></span>
                                                            <input type="text" name="amount" id="amount{{ $debt->id }}" class="form-control format-number border-start-0 ps-0" required placeholder="Ej. 500.000">
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="modal-footer border-0 pb-4 pe-4">
                                                        <button type="button" class="btn btn-light text-muted fw-medium" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary fw-bold px-4">Guardar Pago</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center w-100" style="color: #6b7280; min-height: 150px;">
                                        <i data-lucide="truck" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 1rem;"></i>
                                        <p class="mb-0 fw-medium" style="font-size: 0.95rem;">No se encontraron deudas a proveedores.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($debts->hasPages())
        <div class="card-footer bg-transparent py-3 d-flex align-items-center justify-content-between custom-pagination-container" style="border-top: 1px solid #f3f4f6;">
            {{ $debts->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Nueva Deuda -->
<div class="modal fade" id="addDebtModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('supplier-debts.store') }}" method="POST" class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
            @csrf
            <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e5e7eb;">
                <h5 class="modal-title fw-bold text-dark">Nueva Deuda a Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold text-muted">Nombre del Proveedor <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" required placeholder="Ej. Distribuidora S.A.">
                </div>
                <div class="mb-3">
                    <label for="debt_amount" class="form-label fw-semibold text-muted">Monto de la Deuda Inicial <span class="text-danger">*</span></label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white"><i data-lucide="dollar-sign" style="width:14px;height:14px;"></i></span>
                        <input type="text" name="debt_amount" id="debt_amount" class="form-control format-number border-start-0 ps-0" required placeholder="Ej. 1.000.000">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 pe-4">
                <button type="button" class="btn btn-light text-muted fw-medium" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-bold px-4">Guardar Deuda</button>
            </div>
        </form>
    </div>
</div>

<!-- Modales adicionales (Ver, Editar, Eliminar) -->
@foreach($debts as $debt)
    <!-- Modal Ver -->
    <div class="modal fade" id="showDebtModal{{ $debt->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header border-bottom-0 pb-0" style="background-color: #ffffff; padding: 1.25rem 1.5rem;">
                    <h5 class="modal-title fw-bold" style="color: #0f172a; font-size: 1.15rem;">Detalles de la Deuda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3" style="background-color: #ffffff;">
                    <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold fs-7" style="color: #64748b;">Proveedor</span>
                            <span class="fw-bold" style="color: #1e293b; font-size: 0.95rem;">{{ $debt->name }}</span>
                        </div>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold fs-7" style="color: #64748b;">Deuda Total</span>
                            <span class="fw-bold" style="color: #4b5563; font-size: 0.95rem;">${{ number_format($debt->debt_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold fs-7" style="color: #64748b;">Pagado</span>
                            <span class="fw-bold text-success" style="font-size: 0.95rem;">${{ number_format($debt->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold fs-7" style="color: #64748b;">Saldo Pendiente</span>
                            <span class="fw-bold text-danger" style="font-size: 0.95rem;">${{ number_format(max(0, $debt->debt_amount - $debt->paid_amount), 0, ',', '.') }}</span>
                        </div>
                        <div class="mb-1 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold fs-7" style="color: #64748b;">Estado</span>
                            <span class="fw-bold" style="color: #1e293b; font-size: 0.95rem;">{{ ucfirst($debt->status) }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 1rem 1.5rem;">
                    <button type="button" class="btn w-100 fw-bold text-white d-inline-flex justify-content-center align-items-center gap-2" data-bs-dismiss="modal" style="background: #3b82f6; border-radius: 8px;">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar -->
    <div class="modal fade" id="editDebtModal{{ $debt->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('supplier-debts.update', $debt->id) }}" method="POST" class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                @csrf
                @method('PUT')
                <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e5e7eb;">
                    <h5 class="modal-title fw-bold text-dark">Editar Deuda a Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="edit_name_{{ $debt->id }}" class="form-label fw-semibold text-muted">Nombre del Proveedor <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name_{{ $debt->id }}" value="{{ $debt->name }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_debt_amount_{{ $debt->id }}" class="form-label fw-semibold text-muted">Monto de la Deuda Total <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white"><i data-lucide="dollar-sign" style="width:14px;height:14px;"></i></span>
                            <input type="text" name="debt_amount" id="edit_debt_amount_{{ $debt->id }}" value="{{ number_format($debt->debt_amount, 0, ',', '.') }}" class="form-control format-number border-start-0 ps-0" required>
                        </div>
                        <small class="text-muted d-block mt-2">El monto pagado actualmente es <strong>${{ number_format($debt->paid_amount, 0, ',', '.') }}</strong>. Editar la deuda recalculará el estado.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light text-muted fw-medium" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Actualizar Deuda</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div class="modal fade" id="deleteDebtModal{{ $debt->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-body p-4 text-center" style="background-color: #ffffff;">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 64px; height: 64px; background: #fee2e2; border: 4px solid #fef2f2;">
                        <i data-lucide="alert-triangle" style="width: 32px; height: 32px; color: #ef4444;"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: #0f172a;">¿Eliminar Deuda?</h5>
                    <p class="text-muted mb-4" style="font-size: 0.9rem;">
                        Estás a punto de eliminar permanentemente la deuda con <strong>{{ $debt->name }}</strong>. <br>Esta acción no se puede deshacer.
                    </p>
                    <form action="{{ route('supplier-debts.destroy', $debt->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex gap-2 w-100">
                            <button type="button" class="btn btn-light w-50 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px; color: #475569;">Cancelar</button>
                            <button type="submit" class="btn text-white w-50 fw-bold d-inline-flex justify-content-center align-items-center gap-2" style="background: #ef4444; border-radius: 8px;">
                                Eliminar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

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
    });
</script>
@endpush
@endsection
