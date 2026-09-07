@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0 mx-auto" style="max-width: 1300px;">
    
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0 fw-bold" style="color: #111827;">Abonos &bull; Historial</h5>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
        <div class="card-body p-3">
            <form action="{{ route('payments.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-12">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; background: #fff; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-transparent border-0" style="color: #94a3b8; padding-right: 8px;">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 ps-0 shadow-none auto-submit" placeholder="Buscar por número de crédito o nombre de cliente..." value="{{ request('search') }}" style="font-size: 0.95rem; background: transparent;">
                        @if(request('search'))
                            <a href="{{ route('payments.index') }}" class="input-group-text bg-transparent border-0 text-danger text-decoration-none" title="Limpiar Búsqueda">
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
                    <i data-lucide="hand-coins" style="color: #2563eb; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color: #111827 !important; letter-spacing: -0.3px; font-size: 0.95rem;">Historial de Abonos</h6>
                    <p class="mb-0 fs-7 d-flex align-items-center gap-2" style="color: #6b7280 !important;">
                        <span style="width: 5px; height: 5px; background: #10B981; border-radius: 50%;"></span>
                        Registro detallado de los pagos y abonos aplicados a los créditos
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                <a href="{{ route('payments.export') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #10b981; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(16, 185, 129, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-spreadsheet" style="width: 14px; height: 14px;"></i> Exportar Excel
                </a>
                <a href="{{ route('payments.export.pdf') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #ef4444; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(220, 38, 38, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Exportar PDF
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#createPaymentModal" class="btn btn-sm btn-primary d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #2563eb; border: none; box-shadow: 0 2px 4px -1px rgba(37, 99, 235, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Registrar Abono Libre
                </button>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless mb-0 align-middle" style="color: #374151;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <th class="fw-semibold text-uppercase ps-3 py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 5%;">#</th>
                            <th class="fw-semibold text-uppercase py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 15%;">FECHA</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 30%;">CLIENTE</th>
                            <th class="fw-semibold text-uppercase py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 15%;">NO. CRÉDITO</th>
                            <th class="fw-semibold text-uppercase py-2 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 15%;">VALOR ABONADO</th>
                            <th class="fw-semibold text-uppercase pe-3 py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 20%;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr class="table-row-hover" style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.2s;">
                                <td class="ps-3 py-2 text-center text-secondary fw-semibold" style="font-size: 0.8rem;">
                                    {{ $payments->firstItem() ? $payments->firstItem() + $loop->index : $loop->iteration }}
                                </td>
                                <td class="py-2 text-center text-secondary" style="font-size: 0.8rem;">
                                    {{ $payment->date ? $payment->date->format('d/m/Y') : $payment->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-2">
                                    <div class="d-flex flex-column justify-content-center">
                                        <span class="fw-bold" style="color: #111827; font-size: 0.8rem;">{{ $payment->customer->name ?? 'Consumidor Final' }}</span>
                                    </div>
                                </td>
                                <td class="text-center py-2 text-primary fw-semibold" style="font-size: 0.8rem;">
                                    {{ $payment->sale->invoice_type === 'credit' ? '#CRD-' : '#FAC-' }}{{ $payment->sale->document_number }}
                                </td>
                                <td class="fw-bold py-2 text-end text-success" style="font-size: 0.8rem;">
                                    ${{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center pe-3 py-1">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- View Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #f3f4f6; border: 1px solid #e5e7eb;" title="Ver" data-bs-toggle="modal" data-bs-target="#modalPayment{{ $payment->id }}">
                                            <i data-lucide="eye" style="color: #4b5563; width: 12px; height: 12px;"></i>
                                        </button>
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fef3c7; border: 1px solid #fde68a;" title="Editar" data-bs-toggle="modal" data-bs-target="#editPaymentModal{{ $payment->id }}">
                                            <i data-lucide="edit-2" style="color: #d97706; width: 12px; height: 12px;"></i>
                                        </button>
                                        <!-- Delete Button -->
                                        <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="confirmDelete(event, '¿Estás seguro de que deseas eliminar este abono?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fee2e2; border: 1px solid #fecaca;" title="Eliminar">
                                                <i data-lucide="trash-2" style="color: #ef4444; width: 12px; height: 12px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            
                        @empty
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td colspan="6" class="text-center py-5 text-secondary" style="color: #6b7280; font-size: 0.85rem;">
                                    <i data-lucide="hand-coins" class="mb-3 d-block mx-auto" style="width: 48px; height: 48px; opacity: 0.5; color: #9ca3af;"></i>
                                    <p class="mb-0">Aún no hay abonos registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payments->hasPages())
        <div class="card-footer bg-transparent py-3 d-flex align-items-center justify-content-between custom-pagination-container" style="border-top: 1px solid #f3f4f6;">
            {{ $payments->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

@foreach($payments as $payment)
<!-- Payment Modal -->
                            <div class="modal fade" id="modalPayment{{ $payment->id }}" tabindex="-1" aria-labelledby="modalPaymentLabel{{ $payment->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                                            <h5 class="modal-title fw-bold" id="modalPaymentLabel{{ $payment->id }}" style="color: #0f172a;">Detalles del Abono</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            
                                            <!-- Card 1: Información General -->
                                            <div class="card mb-3 border-0" style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center gap-2 mb-3">
                                                        <div style="background: #ffffff; padding: 6px; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                                            <i data-lucide="user" style="color: #3b82f6; width: 16px; height: 16px;"></i>
                                                        </div>
                                                        <h6 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">Información del Abono</h6>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: #e2e8f0 !important;">
                                                        <span class="fw-semibold" style="color: #64748b; font-size: 0.85rem;">Cliente</span>
                                                        <span class="fw-bold" style="color: #0f172a; font-size: 0.85rem;">{{ $payment->customer->name ?? 'Consumidor Final' }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="border-color: #e2e8f0 !important;">
                                                        <span class="fw-semibold" style="color: #64748b; font-size: 0.85rem;">Abonado a Documento</span>
                                                        <span class="fw-bold" style="color: #3b82f6; font-size: 0.85rem;">{{ $payment->sale->invoice_type === 'credit' ? '#CRD-' : '#FAC-' }}{{ $payment->sale->document_number }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="fw-semibold" style="color: #64748b; font-size: 0.85rem;">Fecha del Abono</span>
                                                        <span class="fw-bold" style="color: #0f172a; font-size: 0.85rem;">{{ $payment->date ? $payment->date->format('d M Y') : $payment->created_at->format('d M Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Card 2: Análisis Financiero -->
                                            <div class="card border-0" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center gap-2 mb-3">
                                                        <div style="background: #ffffff; padding: 6px; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                                            <i data-lucide="tag" style="color: #10b981; width: 16px; height: 16px;"></i>
                                                        </div>
                                                        <h6 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.95rem;">Resumen Financiero</h6>
                                                    </div>
                                                    
                                                    <div class="p-3 mb-0" style="background: #d1fae5; border: 1px solid #a7f3d0; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                                                        <span class="fw-bold text-success" style="font-size: 0.85rem; letter-spacing: 0.5px;">VALOR ABONADO</span>
                                                        <span class="fw-bold text-success" style="font-size: 1.1rem;">${{ number_format($payment->amount, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                            <button type="button" class="btn btn-primary w-100 fw-bold" data-bs-dismiss="modal" style="border-radius: 8px; padding: 12px; background: #3b82f6; border: none;">Entendido</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
@endforeach

@foreach($payments as $payment)
<!-- Edit Payment Modal -->
                            <div class="modal fade" id="editPaymentModal{{ $payment->id }}" tabindex="-1" aria-labelledby="editPaymentModalLabel{{ $payment->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('payments.update', $payment) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom-0 pb-0 pt-4 px-4" style="background-color: #ffffff;">
                                            <h5 class="modal-title fw-bold" id="editPaymentModalLabel{{ $payment->id }}" style="color: #0f172a; font-size: 1.15rem;">Editar Abono</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4" style="background-color: #ffffff;">
                                            <div class="d-flex flex-column gap-3">
                                                <!-- Sección Información -->
                                                <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                                                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                                                        <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #3b82f6; border: 1px solid #e2e8f0;">
                                                            <i data-lucide="receipt" style="width: 14px; height: 14px;"></i>
                                                        </div>
                                                        <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Información del Crédito</h6>
                                                    </div>
                                                    
                                                    <div class="mb-2 d-flex justify-content-between align-items-center">
                                                        <span class="fw-semibold fs-7" style="color: #64748b;">Documento</span>
                                                        <span class="fw-bold" style="color: #1e293b; font-size: 0.95rem;">{{ $payment->sale->invoice_type === 'credit' ? '#CRD-' : '#FAC-' }}{{ $payment->sale->document_number }}</span>
                                                    </div>
                                                    <div class="mb-1 d-flex justify-content-between align-items-center">
                                                        <span class="fw-semibold fs-7" style="color: #64748b;">Deuda Máxima</span>
                                                        <span class="fw-bold text-primary" style="font-size: 0.95rem;">${{ number_format($payment->sale->pending_balance + $payment->amount, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>

                                                <!-- Sección Valor -->
                                                <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                                                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                                                        <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #10b981; border: 1px solid #e2e8f0;">
                                                            <i data-lucide="coins" style="width: 14px; height: 14px;"></i>
                                                        </div>
                                                        <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Valor del Abono</h6>
                                                    </div>
                                                    
                                                    <div class="mb-1">
                                                        <div class="input-group shadow-sm" style="border-radius: 8px;">
                                                            <span class="input-group-text border-end-0 py-2" style="background: #ffffff; border-color: #cbd5e1; color: #10b981; font-weight: bold; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                                                <i data-lucide="dollar-sign" style="width: 18px; height: 18px;"></i>
                                                            </span>
                                                            <input type="text" name="amount" class="form-control format-number border-start-0 py-2 ps-0 shadow-none edit-payment-amount" data-max-amount="{{ $payment->sale->pending_balance + $payment->amount }}" value="{{ number_format($payment->amount, 0, '', '.') }}" required placeholder="Ej. 50.000" style="border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; font-size: 1.1rem; font-weight: 600; color: #0f172a;">
                                                        </div>
                                                        <div class="invalid-feedback d-block mt-2 fs-8 editAmountFeedback" style="display: none !important;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 1rem 1.5rem;">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; color: #374151;">Cancelar</button>
                                            <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-2" style="background: #f59e0b; border-radius: 8px;">
                                                <i data-lucide="save" style="width: 16px; height: 16px;"></i> Guardar Cambios
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
@endforeach

<!-- Modal Crear Abono Libre -->
<div class="modal fade" id="createPaymentModal" tabindex="-1" aria-labelledby="createPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('payments.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            @csrf
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4" style="background-color: #ffffff;">
                <h5 class="modal-title fw-bold" id="createPaymentModalLabel" style="color: #0f172a; font-size: 1.15rem;">Registrar Nuevo Abono</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #ffffff;">
                <div class="d-flex flex-column gap-3">
                    <!-- Sección Información -->
                    <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #3b82f6; border: 1px solid #e2e8f0;">
                                <i data-lucide="receipt" style="width: 14px; height: 14px;"></i>
                            </div>
                            <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Selección de Crédito</h6>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155;">Buscar Cliente o Nro. <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text border-end-0 py-2" style="background: #ffffff; border-color: #cbd5e1; color: #94a3b8; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                    <i data-lucide="file-text" style="width: 16px; height: 16px;"></i>
                                </span>
                                <select name="sale_id" id="sale_id" class="form-select border-start-0 py-2 ps-0 shadow-none" style="border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; font-size: 0.9rem;" required>
                                    <option value="" selected disabled>Seleccionar crédito...</option>
                                    @foreach($pendingCredits as $credit)
                                        <option value="{{ $credit->id }}" data-max-amount="{{ $credit->pending_balance }}">
                                            #CRD-{{ $credit->document_number }} - {{ $credit->customer->name ?? 'Consumidor Final' }} (Deuda: ${{ number_format($credit->pending_balance, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="creditInfoBox" class="mt-2 p-2 rounded-3 d-none align-items-center" style="background: #eff6ff; border: 1px solid #bfdbfe; font-size: 0.8rem; color: #1e3a8a;">
                            <i data-lucide="info" style="width: 14px; height: 14px; margin-right: 6px;"></i>
                            <span>Deuda máxima a pagar: <strong id="maxDebtText">$0</strong></span>
                        </div>
                    </div>

                    <!-- Sección Valor -->
                    <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #10b981; border: 1px solid #e2e8f0;">
                                <i data-lucide="coins" style="width: 14px; height: 14px;"></i>
                            </div>
                            <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Valor del Abono</h6>
                        </div>
                        
                        <div class="mb-1">
                            <div class="input-group shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text border-end-0 py-2" style="background: #ffffff; border-color: #cbd5e1; color: #10b981; font-weight: bold; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                    <i data-lucide="dollar-sign" style="width: 18px; height: 18px;"></i>
                                </span>
                                <input type="text" name="amount" id="paymentAmountInput" class="form-control format-number border-start-0 py-2 ps-0 shadow-none" required placeholder="Ej. 50.000" style="border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; font-size: 1.1rem; font-weight: 600; color: #0f172a;">
                            </div>
                            <div class="invalid-feedback d-block mt-2 fs-8" id="amountFeedback" style="display: none !important;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 1rem 1.5rem;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; color: #374151;">Cancelar</button>
                <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-2" style="background: #3b82f6; border-radius: 8px;">
                    <i data-lucide="save" style="width: 16px; height: 16px;"></i> Registrar Abono
                </button>
            </div>
        </form>
    </div>
</div>
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

        // Validar que el abono no supere la deuda
        const saleSelect = document.getElementById('sale_id');
        const amountInput = document.getElementById('paymentAmountInput');
        const feedback = document.getElementById('amountFeedback');

        function validateAmount() {
            if (!saleSelect || !amountInput) return;
            const selectedOption = saleSelect.options[saleSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) return;
            
            const maxAmount = parseFloat(selectedOption.getAttribute('data-max-amount'));
            const currentAmount = parseInt(amountInput.value.replace(/\D/g, '') || 0, 10);
            
            // Mostrar la deuda máxima permitida
            const infoBox = document.getElementById('creditInfoBox');
            const maxDebtText = document.getElementById('maxDebtText');
            if (infoBox && maxDebtText) {
                maxDebtText.textContent = '$' + maxAmount.toLocaleString('es-CO');
                infoBox.classList.remove('d-none');
                infoBox.classList.add('d-flex');
            }
            
            if (currentAmount > maxAmount) {
                amountInput.setCustomValidity('Inválido');
                feedback.textContent = 'El monto no puede superar la deuda ($' + maxAmount.toLocaleString('es-CO') + ')';
                feedback.style.setProperty('display', 'block', 'important');
                amountInput.parentElement.style.border = '1px solid #ef4444';
            } else {
                amountInput.setCustomValidity('');
                feedback.style.setProperty('display', 'none', 'important');
                amountInput.parentElement.style.border = '1px solid #cbd5e1';
            }
        }

        if (saleSelect && amountInput) {
            saleSelect.addEventListener('change', validateAmount);
            amountInput.addEventListener('input', validateAmount);
        }

        // Validación para los modales de edición
        document.querySelectorAll('.edit-payment-amount').forEach(function(input) {
            input.addEventListener('input', function() {
                const maxAmount = parseFloat(this.getAttribute('data-max-amount'));
                const currentAmount = parseInt(this.value.replace(/\D/g, '') || 0, 10);
                const feedback = this.closest('.modal-body').querySelector('.editAmountFeedback');
                
                if (currentAmount > maxAmount) {
                    this.setCustomValidity('Inválido');
                    if (feedback) {
                        feedback.textContent = 'El monto no puede superar la deuda total permitida ($' + maxAmount.toLocaleString('es-CO') + ')';
                        feedback.style.setProperty('display', 'block', 'important');
                    }
                    this.parentElement.style.border = '1px solid #ef4444';
                } else {
                    this.setCustomValidity('');
                    if (feedback) {
                        feedback.style.setProperty('display', 'none', 'important');
                    }
                    this.parentElement.style.border = '1px solid #e2e8f0';
                }
            });
        });
    });
</script>
@endpush
@endsection
