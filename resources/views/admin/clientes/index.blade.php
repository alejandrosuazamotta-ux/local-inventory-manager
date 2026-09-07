@extends('layouts.admin')

@section('content')
@push('styles')
    @vite(['resources/css/admin/clientes.css'])
@endpush
@push('styles')
    <style>
        .custom-pagination-container {
            width: 100%;
        }
        .custom-pagination-container nav {
            width: 100%;
        }
        /* Target the desktop pagination wrapper to force separation */
        .custom-pagination-container nav > div:not(.d-sm-none) {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
        }
        .custom-pagination-container .pagination {
            margin-bottom: 0;
            gap: 6px;
        }
        .custom-pagination-container .page-link {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 8px !important;
            color: #64748b;
            border: 1px solid transparent;
            background-color: transparent;
            transition: all 0.2s ease;
        }
        .custom-pagination-container .page-item:not(.active) .page-link:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .custom-pagination-container .page-item.active .page-link {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: white;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
        }
        .custom-pagination-container .page-item.disabled .page-link {
            color: #cbd5e1;
            background-color: transparent;
        }
        .custom-pagination-container p.small {
            font-size: 0.85rem !important;
            color: #64748b !important;
            margin-bottom: 0;
            padding-top: 0;
        }
    </style>
@endpush

<div class="container-fluid px-0">
    
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0 fw-bold" style="color: #111827;">Clientes &bull; Directorio</h5>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
        <div class="card-body p-3">
            <form action="{{ route('customers.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-12">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; background: #fff; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-transparent border-0" style="color: #94a3b8; padding-right: 8px;">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 ps-0 shadow-none auto-submit" placeholder="Buscar por nombre o número de cédula..." value="{{ request('search') }}" style="font-size: 0.95rem; background: transparent;">
                        @if(request('search'))
                            <a href="{{ route('customers.index') }}" class="input-group-text bg-transparent border-0 text-danger text-decoration-none" title="Limpiar Búsqueda">
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
        <div class="card-header border-0 py-2 px-3 d-flex flex-wrap justify-content-between align-items-center" style="background-color: #ffffff !important; border-radius: 12px 12px 0 0; border-bottom: 1px solid #f3f4f6 !important;">
            <div class="d-flex align-items-center gap-2">
                <div style="background: #eff6ff; padding: 8px; border-radius: 8px; border: 1px solid #bfdbfe;">
                    <i data-lucide="users" style="color: #3b82f6; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem; color: #1e293b !important; letter-spacing: -0.3px;">Directorio de Clientes</h6>
                    <p class="text-secondary mb-0 fs-7 d-flex align-items-center gap-2" style="color: #64748b !important;">
                        <span style="width: 5px; height: 5px; background: #10B981; border-radius: 50%;"></span> 
                        Gestión de clientes e información de contacto
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                <a href="{{ route('customers.export') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold shadow-sm" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 6px; padding: 0 12px; height: 32px; font-size: 0.8rem; text-decoration: none;">
                    <i data-lucide="file-spreadsheet" style="width: 14px; height: 14px;"></i> Excel
                </a>
                <a href="{{ route('customers.export.pdf') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #ef4444; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(220, 38, 38, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Exportar PDF
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#createModal" class="btn btn-sm btn-primary d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #2563eb; border: none; box-shadow: 0 2px 4px -1px rgba(37, 99, 235, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Nuevo cliente
                </button>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0" style="background: #ffffff !important;">
            <div class="table-responsive" style="background: #ffffff !important;">
                <table class="table table-borderless mb-0 align-middle" style="color: #374151;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <th class="fw-semibold text-uppercase ps-3 py-2" style="color: #6b7280; letter-spacing: 0.5px; width: 40px; font-size: 0.7rem;">#</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">CLIENTE</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">CELULAR</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">CÉDULA</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">CORREO</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">DIRECCIÓN</th>
                            <th class="fw-semibold text-uppercase pe-3 py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $customer)
                            <tr class="table-row-hover" style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.2s;">
                                <td class="ps-3 py-1" style="color: #6b7280; font-size: 0.75rem;">{{ $index + 1 + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                                <td class="fw-bold py-1" style="color: #111827; font-size: 0.8rem;">{{ $customer->name }}</td>
                                <td class="py-1 fw-semibold" style="color: #111827; font-size: 0.8rem;">{{ $customer->phone ?? 'N/A' }}</td>
                                <td class="py-1" style="color: #4b5563; font-size: 0.8rem;">{{ $customer->document_number ?? 'N/A' }}</td>
                                <td class="py-1" style="color: #4b5563; font-size: 0.8rem;">{{ $customer->email ?? 'N/A' }}</td>
                                <td class="py-1" style="color: #4b5563; font-size: 0.8rem;">
                                    <span class="d-inline-block text-truncate" style="max-width: 150px;" title="{{ $customer->address }}">
                                        {{ $customer->address ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-center pe-3 py-1">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- View Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" data-bs-toggle="modal" data-bs-target="#viewModal{{ $customer->id }}" style="width: 26px; height: 26px; background: #f3f4f6; border: 1px solid #e5e7eb;" title="Ver">
                                            <i data-lucide="eye" style="color: #4b5563; width: 12px; height: 12px;"></i>
                                        </button>
                                        <!-- History Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" data-bs-toggle="modal" data-bs-target="#historyModal{{ $customer->id }}" style="width: 26px; height: 26px; background: #e0f2fe; border: 1px solid #bae6fd;" title="Historial">
                                            <i data-lucide="clock" style="color: #0284c7; width: 12px; height: 12px;"></i>
                                        </button>
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $customer->id }}" style="width: 26px; height: 26px; background: #fef3c7; border: 1px solid #fde68a;" title="Editar">
                                            <i data-lucide="edit-2" style="color: #d97706; width: 12px; height: 12px;"></i>
                                        </button>
                                        <!-- Delete Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $customer->id }}" style="width: 26px; height: 26px; background: #fee2e2; border: 1px solid #fecaca;" title="Eliminar">
                                            <i data-lucide="trash-2" style="color: #ef4444; width: 12px; height: 12px;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-secondary">
                                    <i data-lucide="users" class="mb-3 text-secondary" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                                    <p class="mb-0">No se encontraron clientes registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($customers->hasPages())
        <div class="card-footer bg-transparent pt-3 pb-3 custom-pagination-container" style="border-top: 1px solid #f1f5f9;">
            {{ $customers->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>


@foreach($customers as $customer)
<!-- View Modal -->
                            <div class="modal fade" id="viewModal{{ $customer->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $customer->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden; background-color: #ffffff;">
                                        <div class="modal-header border-bottom-0 pb-0" style="background-color: #ffffff; padding: 1.25rem 1.5rem;">
                                            <h5 class="modal-title fw-bold" id="viewModalLabel{{ $customer->id }}" style="color: #0f172a; font-size: 1.15rem;">
                                                Detalles del Cliente
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-3" style="background-color: #ffffff;">
                                            <div class="d-flex flex-column gap-3">
                                                <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                                                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                                                        <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #3b82f6; border: 1px solid #e2e8f0;">
                                                            <i data-lucide="user" style="width: 14px; height: 14px;"></i>
                                                        </div>
                                                        <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Información General</h6>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background-color: #ffffff; border: 1px solid #e2e8f0;">
                                                                <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 36px; height: 36px; background-color: #f8fafc; color: #3b82f6; border: 1px solid #e2e8f0;">
                                                                    <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                                                                </div>
                                                                <div>
                                                                    <span class="d-block fw-semibold fs-7" style="color: #64748b;">Cliente</span>
                                                                    <span class="d-block fw-bold" style="color: #0f172a; font-size: 0.95rem;">{{ $customer->name }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background-color: #ffffff; border: 1px solid #e2e8f0;">
                                                                <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 36px; height: 36px; background-color: #f8fafc; color: #6366f1; border: 1px solid #e2e8f0;">
                                                                    <i data-lucide="credit-card" style="width: 16px; height: 16px;"></i>
                                                                </div>
                                                                <div>
                                                                    <span class="d-block fw-semibold fs-7" style="color: #64748b;">Cédula</span>
                                                                    <span class="d-block fw-bold" style="color: #0f172a; font-size: 0.95rem;">{{ $customer->document_number }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background-color: #ffffff; border: 1px solid #e2e8f0;">
                                                                <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 36px; height: 36px; background-color: #f8fafc; color: #10b981; border: 1px solid #e2e8f0;">
                                                                    <i data-lucide="smartphone" style="width: 16px; height: 16px;"></i>
                                                                </div>
                                                                <div>
                                                                    <span class="d-block fw-semibold fs-7" style="color: #64748b;">Celular</span>
                                                                    <span class="d-block fw-bold" style="color: #0f172a; font-size: 0.95rem;">{{ $customer->phone ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background-color: #ffffff; border: 1px solid #e2e8f0;">
                                                                <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 36px; height: 36px; background-color: #f8fafc; color: #f59e0b; border: 1px solid #e2e8f0;">
                                                                    <i data-lucide="mail" style="width: 16px; height: 16px;"></i>
                                                                </div>
                                                                <div style="overflow: hidden;">
                                                                    <span class="d-block fw-semibold fs-7" style="color: #64748b;">Correo</span>
                                                                    <span class="d-block fw-bold text-truncate" style="color: #0f172a; font-size: 0.95rem;">{{ $customer->email ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background-color: #ffffff; border: 1px solid #e2e8f0;">
                                                                <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 36px; height: 36px; background-color: #f8fafc; color: #ef4444; border: 1px solid #e2e8f0;">
                                                                    <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                                                                </div>
                                                                <div style="overflow: hidden;">
                                                                    <span class="d-block fw-semibold fs-7" style="color: #64748b;">Dirección</span>
                                                                    <span class="d-block fw-bold text-truncate" style="color: #0f172a; font-size: 0.95rem;">{{ $customer->address ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="background-color: #ffffff !important; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 1rem 1.5rem;">
                                            <button type="button" class="btn w-100 fw-bold text-white d-inline-flex justify-content-center align-items-center gap-2" data-bs-dismiss="modal" style="background: #3b82f6 !important; border-radius: 8px;">
                                                Entendido
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $customer->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $customer->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden; background-color: #ffffff;">
                                        <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e5e7eb; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 1.25rem 1.5rem;">
                                            <h5 class="modal-title fw-bold" id="editModalLabel{{ $customer->id }}" style="color: #111827; font-size: 1.15rem;">
                                                Editar Cliente
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('customers.update', $customer) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-3" style="background-color: #ffffff;">
                                                <div class="d-flex flex-column gap-3">
                                                    <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                                                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                                                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #f59e0b; border: 1px solid #fcd34d;">
                                                                <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                                            </div>
                                                            <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Información General</h6>
                                                        </div>
                                                        <div class="row g-3">
                                                            <input type="hidden" name="document_type" value="{{ $customer->document_type ?? 'CC' }}">
                                                            <div class="col-12">
                                                                <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155 !important;">Cliente <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" name="name" value="{{ $customer->name }}" required placeholder="Nombre del cliente" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155 !important;">Cédula <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control" name="document_number" value="{{ $customer->document_number }}" required placeholder="Ej. 10203040" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155 !important;">Celular</label>
                                                                <input type="number" class="form-control" name="phone" value="{{ $customer->phone }}" placeholder="Ej. 3001234567" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155 !important;">Correo</label>
                                                                <input type="email" class="form-control" name="email" value="{{ $customer->email }}" placeholder="ejemplo@correo.com" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);">
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155 !important;">Dirección</label>
                                                                <input type="text" class="form-control" name="address" value="{{ $customer->address }}" placeholder="Ej. Calle 123 # 45-67" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 1rem 1.5rem;">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; color: #374151; background-color: #ffffff;">Cancelar</button>
                                                <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-2" style="background: #f59e0b; border-radius: 8px; border: none;">
                                                    <i data-lucide="save" style="width: 16px; height: 16px;"></i> Guardar Cambios
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $customer->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $customer->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden; background-color: #ffffff !important;">
                                        <div class="modal-body p-4 text-center" style="background-color: #ffffff !important;">
                                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm" style="width: 64px; height: 64px; background: #fee2e2 !important; border: 4px solid #fef2f2 !important;">
                                                <i data-lucide="alert-triangle" style="width: 32px; height: 32px; color: #ef4444;"></i>
                                            </div>
                                            <h5 class="fw-bold mb-2" style="color: #0f172a !important;">¿Eliminar Cliente?</h5>
                                            <p class="text-muted mb-4" style="font-size: 0.9rem; color: #64748b !important;">
                                                Estás a punto de eliminar permanentemente a <strong>{{ $customer->name }}</strong>. <br>Esta acción no se puede deshacer.
                                            </p>
                                            
                                            <form action="{{ route('customers.destroy', $customer) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="d-flex gap-2 w-100">
                                                    <button type="button" class="btn w-50 fw-semibold" data-bs-dismiss="modal" style="border: 1px solid #d1d5db; border-radius: 8px; color: #475569 !important; background-color: #ffffff !important;">Cancelar</button>
                                                    <button type="submit" class="btn text-white w-50 fw-bold d-inline-flex justify-content-center align-items-center gap-2" style="background: #ef4444 !important; border-radius: 8px; border: none;">
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- History Modal -->
                            <div class="modal fade" id="historyModal{{ $customer->id }}" tabindex="-1" aria-labelledby="historyModalLabel{{ $customer->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden; background-color: #ffffff;">
                                        <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e5e7eb; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 1.25rem 1.5rem;">
                                            <h5 class="modal-title fw-bold" id="historyModalLabel{{ $customer->id }}" style="color: #111827; font-size: 1.15rem;">
                                                Historial de Facturas: {{ $customer->name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4" style="background-color: #ffffff; max-height: 400px; overflow-y: auto;">
                                            @if($customer->sales->isEmpty())
                                                <div class="text-center text-muted py-4">
                                                    <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.3;" class="mb-2"></i>
                                                    <p>Este cliente no tiene facturas o compras registradas aún.</p>
                                                </div>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle mb-0">
                                                        <thead style="background-color: #f8fafc;">
                                                            <tr>
                                                                <th class="py-2" style="color: #475569; font-size: 0.85rem; font-weight: 600;">Factura</th>
                                                                <th class="py-2" style="color: #475569; font-size: 0.85rem; font-weight: 600;">Fecha</th>
                                                                <th class="py-2 text-end" style="color: #475569; font-size: 0.85rem; font-weight: 600;">Total</th>
                                                                <th class="py-2 text-center" style="color: #475569; font-size: 0.85rem; font-weight: 600;">Estado</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($customer->sales as $sale)
                                                                <tr>
                                                                    <td class="py-2 fw-medium" style="color: #0f172a; font-size: 0.85rem;">{{ $sale->invoice_type === 'credit' ? '#CRD-' : ($sale->invoice_type === 'quotation' ? '#COT-' : '#FAC-') }}{{ $sale->document_number }}</td>
                                                                    <td class="py-2 text-muted" style="font-size: 0.85rem;">{{ $sale->created_at->format('d M Y') }}</td>
                                                                    <td class="py-2 fw-bold text-end" style="color: #10b981; font-size: 0.85rem;">${{ number_format($sale->total, 0, ',', '.') }}</td>
                                                                    <td class="py-2 text-center">
                                                                        @if($sale->status === 'paid')
                                                                            <span class="badge bg-success" style="font-size: 0.7rem; border-radius: 4px;">Pagado</span>
                                                                        @elseif($sale->status === 'pending')
                                                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem; border-radius: 4px;">Pendiente</span>
                                                                        @else
                                                                            <span class="badge bg-secondary" style="font-size: 0.7rem; border-radius: 4px;">{{ ucfirst($sale->status) }}</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
@endforeach

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden; background-color: #ffffff;">
            <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e5e7eb; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 1.25rem 1.5rem;">
                <h5 class="modal-title fw-bold" id="createModalLabel" style="color: #111827; font-size: 1.15rem;">
                    Nuevo Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-3" style="background-color: #ffffff;">
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                                <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #3b82f6; border: 1px solid #e2e8f0;">
                                    <i data-lucide="user" style="width: 14px; height: 14px;"></i>
                                </div>
                                <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Información General</h6>
                            </div>
                            <div class="row g-3">
                                <input type="hidden" name="document_type" value="CC">
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155 !important;">Cliente <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="Nombre del cliente" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155 !important;">Cédula <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="document_number" value="{{ old('document_number') }}" required placeholder="Ej. 10203040" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155 !important;">Celular</label>
                                    <input type="number" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="Ej. 3001234567" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155 !important;">Correo</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="ejemplo@correo.com" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-7 mb-1" style="color: #334155 !important;">Dirección</label>
                                    <input type="text" class="form-control" name="address" value="{{ old('address') }}" placeholder="Ej. Calle 123 # 45-67" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 1rem 1.5rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; color: #374151; background-color: #ffffff;">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-2" style="background: #3b82f6; border-radius: 8px; border: none;">
                        <i data-lucide="save" style="width: 16px; height: 16px;"></i> Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 ya se carga globalmente en layouts/admin.blade.php -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert2 Toasts Notification Setup
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif
        
        @if($errors->any())
            var createModalElement = document.getElementById('createModal');
            if (createModalElement) {
                var createModal = new bootstrap.Modal(createModalElement);
                createModal.show();
            }
        @endif
    });
</script>
@endsection
