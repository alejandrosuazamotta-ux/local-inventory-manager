@extends('layouts.admin')

@push('styles')
<style>
    .expense-type-btn {
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
        border: 1.5px solid #cbd5e1;
        background-color: #ffffff;
        color: #475569;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    .expense-type-btn:hover {
        background-color: #f8fafc;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .btn-check:checked + .expense-type-personal {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        border-color: #1d4ed8 !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35) !important;
    }
    .btn-check:checked + .expense-type-merchandise {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%) !important;
        color: #ffffff !important;
        border-color: #c2410c !important;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.35) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 mx-auto" style="max-width: 1300px;">
    
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0 fw-bold" style="color: #111827;">Gastos &bull; Historial</h5>
    </div>

    <!-- Summary Cards -->
    <div class="row g-2 mb-3">
        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm" style="border-radius: 10px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe !important;">
                <div class="card-body p-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase fw-bold d-block" style="color: #1e40af; font-size: 0.65rem; letter-spacing: 0.4px;">Gastos Personales</span>
                        <h5 class="fw-bolder mb-0 mt-1" style="color: #1e3a8a; font-size: 1.15rem;">${{ number_format($totalPersonal ?? 0, 0, ',', '.') }}</h5>
                    </div>
                    <div style="background: #ffffff; padding: 6px 8px; border-radius: 8px; color: #2563eb;" class="shadow-sm">
                        <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm" style="border-radius: 10px; background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border: 1px solid #fed7aa !important;">
                <div class="card-body p-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase fw-bold d-block" style="color: #9a3412; font-size: 0.65rem; letter-spacing: 0.4px;">Gastos de Mercancía</span>
                        <h5 class="fw-bolder mb-0 mt-1" style="color: #7c2d12; font-size: 1.15rem;">${{ number_format($totalMerchandise ?? 0, 0, ',', '.') }}</h5>
                    </div>
                    <div style="background: #ffffff; padding: 6px 8px; border-radius: 8px; color: #ea580c;" class="shadow-sm">
                        <i data-lucide="package" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm" style="border-radius: 10px; background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid #fca5a5 !important;">
                <div class="card-body p-2 px-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase fw-bold d-block" style="color: #991b1b; font-size: 0.65rem; letter-spacing: 0.4px;">Total Egresos (Caja)</span>
                        <h5 class="fw-bolder mb-0 mt-1" style="color: #7f1d1d; font-size: 1.15rem;">${{ number_format($totalExpenses ?? 0, 0, ',', '.') }}</h5>
                    </div>
                    <div style="background: #ffffff; padding: 6px 8px; border-radius: 8px; color: #dc2626;" class="shadow-sm">
                        <i data-lucide="calculator" style="width: 18px; height: 18px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
        <div class="card-body p-3">
            <form action="{{ route('expenses.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-8">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; background: #fff; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-transparent border-0" style="color: #94a3b8; padding-right: 8px;">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 ps-0 shadow-none" placeholder="Buscar por descripción..." value="{{ request('search') }}" style="font-size: 0.95rem; background: transparent;">
                        @if(request('search'))
                            <a href="{{ route('expenses.index', ['type' => request('type')]) }}" class="input-group-text bg-transparent border-0 text-danger text-decoration-none" title="Limpiar Búsqueda">
                                <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <select name="type" class="form-select shadow-sm" onchange="this.form.submit()" style="border-radius: 8px; border-color: #e2e8f0; font-size: 0.9rem;">
                        <option value="">-- Todos los Tipos de Gasto --</option>
                        <option value="personal" {{ request('type') === 'personal' ? 'selected' : '' }}>👤 Gastos Personales</option>
                        <option value="merchandise" {{ request('type') === 'merchandise' ? 'selected' : '' }}>📦 Gastos de Mercancía</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
        <!-- Card Header -->
        <div class="card-header bg-transparent py-2 px-3 d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: 1px solid #f3f4f6 !important;">
            <div class="d-flex align-items-center gap-2">
                <div style="background: #fee2e2; padding: 8px; border-radius: 8px; border: 1px solid #fecaca;">
                    <i data-lucide="calculator" style="color: #ef4444; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color: #111827 !important; letter-spacing: -0.3px; font-size: 0.95rem;">Historial de Gastos</h6>
                    <p class="mb-0 fs-7 d-flex align-items-center gap-2" style="color: #6b7280 !important;">
                        <span style="width: 5px; height: 5px; background: #ef4444; border-radius: 50%;"></span>
                        Registro detallado de egresos diarios cargados a la caja (Personales y Mercancía)
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                <a href="{{ route('expenses.export', ['search' => request('search'), 'type' => request('type')]) }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #10b981; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(16, 185, 129, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-spreadsheet" style="width: 14px; height: 14px;"></i> Exportar Excel
                </a>
                <a href="{{ route('expenses.export.pdf', ['search' => request('search'), 'type' => request('type')]) }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #ef4444; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(220, 38, 38, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Exportar PDF
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#createExpenseModal" class="btn btn-sm btn-primary d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; border: none; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Registrar Gasto
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
                            <th class="fw-semibold text-uppercase py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 12%;">FECHA</th>
                            <th class="fw-semibold text-uppercase py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 18%;">TIPO DE GASTO</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 35%;">DESCRIPCIÓN</th>
                            <th class="fw-semibold text-uppercase py-2 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 15%;">MONTO</th>
                            <th class="fw-semibold text-uppercase pe-3 py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 15%;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr class="table-row-hover" style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.2s;">
                                <td class="ps-3 py-2 text-center text-secondary fw-semibold" style="font-size: 0.8rem;">
                                    {{ $expenses->firstItem() ? $expenses->firstItem() + $loop->index : $loop->iteration }}
                                </td>
                                <td class="py-2 text-center text-secondary" style="font-size: 0.8rem;">
                                    {{ $expense->date ? $expense->date->format('d/m/Y') : $expense->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-2 text-center">
                                    @if(($expense->type ?? 'personal') === 'merchandise')
                                        <span class="badge shadow-sm" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); color: #ea580c; border: 1px solid #fed7aa; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                                            📦 Mercancía
                                        </span>
                                    @else
                                        <span class="badge shadow-sm" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #2563eb; border: 1px solid #bfdbfe; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                                            👤 Personal
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2">
                                    <div class="d-flex flex-column justify-content-center">
                                        <span class="fw-bold" style="color: #111827; font-size: 0.8rem;">{{ $expense->description }}</span>
                                    </div>
                                </td>
                                <td class="fw-bold py-2 text-end text-danger" style="font-size: 0.8rem;">
                                    -${{ number_format($expense->amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center pe-3 py-1">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- View Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #f3f4f6; border: 1px solid #e5e7eb;" title="Ver" data-bs-toggle="modal" data-bs-target="#modalExpense{{ $expense->id }}">
                                            <i data-lucide="eye" style="color: #4b5563; width: 12px; height: 12px;"></i>
                                        </button>
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fef3c7; border: 1px solid #fde68a;" title="Editar" data-bs-toggle="modal" data-bs-target="#editExpenseModal{{ $expense->id }}">
                                            <i data-lucide="edit-2" style="color: #d97706; width: 12px; height: 12px;"></i>
                                        </button>
                                        <!-- Delete Button -->
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="confirmDelete(event, '¿Estás seguro de que deseas eliminar este gasto?');">
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
                                    <i data-lucide="calculator" class="mb-3 d-block mx-auto" style="width: 48px; height: 48px; opacity: 0.5; color: #9ca3af;"></i>
                                    <p class="mb-0">Aún no hay gastos registrados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($expenses->hasPages())
        <div class="card-footer bg-transparent py-3 d-flex align-items-center justify-content-between custom-pagination-container" style="border-top: 1px solid #f3f4f6;">
            {{ $expenses->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal: Registrar Gasto -->
<div class="modal fade" id="createExpenseModal" tabindex="-1" aria-labelledby="createExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="createExpenseModalLabel" style="color: #0f172a;">Registrar Gasto Diario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Tipo de Gasto -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary fs-7">Tipo de Gasto <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type" id="type_personal_create" value="personal" checked>
                                <label class="expense-type-btn expense-type-personal" for="type_personal_create">
                                    👤 Gasto Personal
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type" id="type_merchandise_create" value="merchandise">
                                <label class="expense-type-btn expense-type-merchandise" for="type_merchandise_create">
                                    📦 Gasto Mercancía
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold text-secondary fs-7">Descripción / Concepto <span class="text-danger">*</span></label>
                        <input type="text" name="description" id="description" class="form-control py-2 shadow-none" required placeholder="Ej. Almuerzo, transporte, flete mercancía..." style="border-color: #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
                    </div>

                    <!-- Fecha -->
                    <div class="mb-3">
                        <label for="date" class="form-label fw-semibold text-secondary fs-7">Fecha del Gasto <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="date" class="form-control py-2 shadow-none" required value="{{ date('Y-m-d') }}" style="border-color: #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
                    </div>

                    <!-- Monto -->
                    <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #ef4444; border: 1px solid #e2e8f0;">
                                <i data-lucide="coins" style="width: 14px; height: 14px;"></i>
                            </div>
                            <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Monto del Gasto</h6>
                        </div>
                        
                        <div class="mb-1">
                            <div class="input-group shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text border-end-0 py-2" style="background: #ffffff; border-color: #cbd5e1; color: #ef4444; font-weight: bold; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                    <i data-lucide="dollar-sign" style="width: 18px; height: 18px;"></i>
                                </span>
                                <input type="text" name="amount" id="expenseAmountInput" class="form-control format-number border-start-0 py-2 ps-0 shadow-none" required placeholder="Ej. 10.000" style="border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; font-size: 1.1rem; font-weight: 600; color: #0f172a;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 1rem 1.5rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; color: #374151;">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 8px; border: none; padding: 8px 18px;">
                        <i data-lucide="save" style="width: 16px; height: 16px;"></i> Registrar Gasto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($expenses as $expense)
<!-- Modal: Ver Gasto -->
<div class="modal fade" id="modalExpense{{ $expense->id }}" tabindex="-1" aria-labelledby="modalExpenseLabel{{ $expense->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="modalExpenseLabel{{ $expense->id }}" style="color: #0f172a;">Detalles del Gasto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="card mb-3 border-0" style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #e2e8f0 !important;">
                            <i data-lucide="receipt" style="color: #ef4444; width: 18px; height: 18px;"></i>
                            <span class="fw-bold text-dark" style="font-size: 0.9rem;">Información General</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary fs-8">No. Gasto:</span>
                            <span class="fw-semibold text-dark fs-8">GST-{{ $expense->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary fs-8">Tipo de Gasto:</span>
                            <span class="fw-bold fs-8">
                                @if(($expense->type ?? 'personal') === 'merchandise')
                                    <span class="badge" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); color: #ea580c; border: 1px solid #fed7aa;">📦 Gasto Mercancía</span>
                                @else
                                    <span class="badge" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #2563eb; border: 1px solid #bfdbfe;">👤 Gasto Personal</span>
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary fs-8">Fecha Gasto:</span>
                            <span class="fw-semibold text-dark fs-8">{{ $expense->date ? $expense->date->format('d/m/Y') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary fs-8">Fecha Registro:</span>
                            <span class="fw-semibold text-dark fs-8">{{ $expense->created_at ? $expense->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        <div class="mb-2">
                            <div class="text-secondary fs-8 mb-1">Descripción:</div>
                            <div class="p-2 rounded bg-white text-dark fw-semibold fs-8" style="border: 1px solid #e2e8f0; min-height: 50px;">
                                {{ $expense->description }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0" style="background: #fef2f2; border-radius: 12px; border: 1px solid #fee2e2 !important;">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-danger fw-semibold d-block" style="font-size: 0.75rem;">MONTO REGISTRADO</span>
                            <small class="text-secondary" style="font-size: 0.65rem;">(Descontado de la caja)</small>
                        </div>
                        <h3 class="fw-bold text-danger m-0" style="font-size: 1.5rem;">
                            ${{ number_format($expense->amount, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal" style="border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600;">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Editar Gasto -->
<div class="modal fade" id="editExpenseModal{{ $expense->id }}" tabindex="-1" aria-labelledby="editExpenseModalLabel{{ $expense->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="editExpenseModalLabel{{ $expense->id }}" style="color: #0f172a;">Editar Gasto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('expenses.update', $expense) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Tipo de Gasto -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary fs-7">Tipo de Gasto <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type" id="type_personal_edit{{ $expense->id }}" value="personal" {{ ($expense->type ?? 'personal') === 'personal' ? 'checked' : '' }}>
                                <label class="expense-type-btn expense-type-personal" for="type_personal_edit{{ $expense->id }}">
                                    👤 Gasto Personal
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="type" id="type_merchandise_edit{{ $expense->id }}" value="merchandise" {{ ($expense->type ?? 'personal') === 'merchandise' ? 'checked' : '' }}>
                                <label class="expense-type-btn expense-type-merchandise" for="type_merchandise_edit{{ $expense->id }}">
                                    📦 Gasto Mercancía
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="description{{ $expense->id }}" class="form-label fw-semibold text-secondary fs-7">Descripción / Concepto <span class="text-danger">*</span></label>
                        <input type="text" name="description" id="description{{ $expense->id }}" class="form-control py-2 shadow-none" required placeholder="Ej. Compra de papelería" value="{{ $expense->description }}" style="border-color: #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
                    </div>

                    <!-- Fecha -->
                    <div class="mb-3">
                        <label for="date{{ $expense->id }}" class="form-label fw-semibold text-secondary fs-7">Fecha del Gasto <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="date{{ $expense->id }}" class="form-control py-2 shadow-none" required value="{{ $expense->date ? $expense->date->format('Y-m-d') : $expense->created_at->format('Y-m-d') }}" style="border-color: #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
                    </div>

                    <!-- Monto -->
                    <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #ef4444; border: 1px solid #e2e8f0;">
                                <i data-lucide="coins" style="width: 14px; height: 14px;"></i>
                            </div>
                            <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Monto del Gasto</h6>
                        </div>
                        
                        <div class="mb-1">
                            <div class="input-group shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text border-end-0 py-2" style="background: #ffffff; border-color: #cbd5e1; color: #ef4444; font-weight: bold; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                    <i data-lucide="dollar-sign" style="width: 18px; height: 18px;"></i>
                                </span>
                                <input type="text" name="amount" id="expenseAmountInput{{ $expense->id }}" class="form-control format-number border-start-0 py-2 ps-0 shadow-none" required placeholder="Ej. 10.000" value="{{ number_format($expense->amount, 0, ',', '.') }}" style="border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; font-size: 1.1rem; font-weight: 600; color: #0f172a;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 1rem 1.5rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; color: #374151;">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border-radius: 8px; border: none; padding: 8px 18px;">
                        <i data-lucide="save" style="width: 16px; height: 16px;"></i> Guardar Cambios
                    </button>
                </div>
            </form>
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
