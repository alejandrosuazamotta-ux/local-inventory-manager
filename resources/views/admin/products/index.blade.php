@extends('layouts.admin')

@section('content')
@push('styles')
    @vite(['resources/css/admin/products.css'])
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
        <h5 class="m-0 fw-bold" style="color: #111827;">Inventario &bull; Listado</h5>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
        <div class="card-body p-3">
            <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-12">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; background: #fff; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-transparent border-0" style="color: #94a3b8; padding-right: 8px;">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 ps-0 shadow-none auto-submit" placeholder="Buscar por nombre o código de producto..." value="{{ request('search') }}" style="font-size: 0.95rem; background: transparent;">
                        @if(request('search'))
                            <a href="{{ route('products.index') }}" class="input-group-text bg-transparent border-0 text-danger text-decoration-none" title="Limpiar Búsqueda">
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
                    <i data-lucide="castle" style="color: #2563eb; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color: #111827 !important; letter-spacing: -0.3px; font-size: 0.95rem;">Inventario</h6>
                    <p class="mb-0 fs-7 d-flex align-items-center gap-2" style="color: #6b7280 !important;">
                        <span style="width: 5px; height: 5px; background: #10B981; border-radius: 50%;"></span>
                        Gestión de productos y análisis de rentabilidad
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">

                <a href="{{ route('products.export', ['search' => request('search')]) }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold shadow-sm" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 6px; padding: 0 12px; height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-spreadsheet" style="width: 14px; height: 14px;"></i> Excel
                </a>
                <a href="{{ route('products.export.pdf', ['search' => request('search')]) }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #ef4444; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(220, 38, 38, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Exportar PDF
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#createProductModal" class="btn btn-sm btn-primary d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #2563eb; border: none; box-shadow: 0 2px 4px -1px rgba(37, 99, 235, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Nuevo producto
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
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">NOMBRE DEL PRODUCTO</th>
                            <th class="fw-semibold text-uppercase py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">CANTIDAD</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">P. COMPRA</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">P. VENTA</th>
                            <th class="fw-semibold text-uppercase py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">UTILIDAD</th>
                            <th class="fw-semibold text-uppercase py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">% UTILIDAD</th>
                            <th class="fw-semibold text-uppercase pe-3 py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                            @php
                                $utilidad = $product->price - $product->cost;
                            @endphp
                            <tr class="table-row-hover" style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.2s;">
                                <td class="ps-3 py-1" style="color: #6b7280; font-size: 0.75rem;">{{ $index + 1 + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                <td class="py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                        <span class="fw-bold" style="color: #111827; font-size: 0.8rem;">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="text-center py-1">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <span class="fw-bold" style="color: #111827; font-size: 0.8rem;">{{ $product->stock }}</span>
                                        @if($product->stock > 0)
                                            <div style="width: 16px; height: 2px; background: #10B981; margin-top: 2px; border-radius: 2px;"></div>
                                        @else
                                            <div style="width: 16px; height: 2px; background: #EF4444; margin-top: 2px; border-radius: 2px;"></div>
                                        @endif
                                    </div>
                                </td>
                                <td class="fw-medium py-1" style="color: #4b5563; font-size: 0.8rem;">${{ number_format($product->cost, 0) }}</td>
                                <td class="fw-bold py-1" style="color: #059669; font-size: 0.8rem;">${{ number_format($product->price, 0) }}</td>
                                <td class="text-center py-1">
                                    <span class="badge" style="background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; border-radius: 4px; padding: 3px 6px; font-weight: 500; font-size: 0.7rem;">
                                        ${{ number_format($utilidad, 0) }}
                                    </span>
                                </td>
                                <td class="text-center py-1">
                                    @php
                                        $porcentajeUtilidad = $product->price > 0 ? (($product->price - $product->cost) / $product->price) * 100 : 0;
                                    @endphp
                                    <span class="badge" style="background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; border-radius: 4px; padding: 3px 6px; font-weight: 500; font-size: 0.7rem;">
                                        {{ number_format($porcentajeUtilidad, 1) }}%
                                    </span>
                                </td>
                                <td class="text-center pe-3 py-1">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#showProductModal{{ $product->id }}" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #f3f4f6; border: 1px solid #e5e7eb;">
                                            <i data-lucide="eye" style="color: #4b5563; width: 12px; height: 12px;"></i>
                                        </button>
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fef3c7; border: 1px solid #fde68a;">
                                            <i data-lucide="edit-2" style="color: #d97706; width: 12px; height: 12px;"></i>
                                        </button>
                                        <button type="button" onclick="confirmDeleteProduct({{ $product->id }})" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fee2e2; border: 1px solid #fecaca;" title="Eliminar Producto">
                                            <i data-lucide="trash-2" style="color: #ef4444; width: 12px; height: 12px;"></i>
                                        </button>
                                        <form id="delete-product-form-{{ $product->id }}" action="{{ route('products.destroy', $product) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5" style="color: #6b7280;">
                                    <i data-lucide="box" class="mb-3" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                                    <p>No se encontraron productos.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
        <div class="card-footer bg-transparent py-3 d-flex align-items-center justify-content-between custom-pagination-container" style="border-top: 1px solid #f3f4f6;">
            {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Crear Producto -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"> <!-- Removido modal-xl para que sea estrecho y alto -->
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);">
      <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e5e7eb; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 1.25rem 1.5rem;">
        <h5 class="modal-title fw-bold" id="createProductModalLabel" style="color: #111827;">Nuevo Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <div class="modal-body p-3" style="background-color: #ffffff;">
            <!-- Campos Ocultos para validación de base de datos -->
            <input type="hidden" name="status" value="active">
            <input type="hidden" name="min_stock" value="0">
            <input type="hidden" name="barcode" value="">

            <div class="d-flex flex-column gap-3">
                
                <!-- Sección Información General -->
                <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                        <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #3b82f6; border: 1px solid #e2e8f0;">
                            <i data-lucide="package" style="width: 14px; height: 14px;"></i>
                        </div>
                        <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Información General</h6>
                    </div>
                    
                    <div class="mb-2">
                        <label for="name" class="form-label fw-semibold fs-7 mb-1" style="color: #334155;">Nombre del Producto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Ej. Camiseta deportiva..." style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);">
                        @error('name') <div class="invalid-feedback fs-8">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-1">
                        <label for="stock" class="form-label fw-semibold fs-7 mb-1" style="color: #334155;">Cantidad Inicial <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock') }}" required placeholder="0" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                        @error('stock') <div class="invalid-feedback fs-8">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Sección Precios y Utilidad -->
                <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                        <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #10b981; border: 1px solid #e2e8f0;">
                            <i data-lucide="tags" style="width: 14px; height: 14px;"></i>
                        </div>
                        <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Estructura de Precios</h6>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="cost" class="form-label fw-semibold fs-7 mb-1" style="color: #334155;">Costo de Compra <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text border-end-0 py-1" style="background: #ffffff; border-color: #cbd5e1; color: #94a3b8; font-weight: 500; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                    <i data-lucide="dollar-sign" style="width:14px; height:14px;"></i>
                                </span>
                                <input type="text" inputmode="numeric" class="form-control format-number border-start-0 py-1 @error('cost') is-invalid @enderror" id="cost" name="cost" value="{{ old('cost') }}" required placeholder="Ej. 10.000" style="border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; padding-left: 0; font-size: 0.9rem;">
                            </div>
                            @error('cost') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label for="price" class="form-label fw-semibold fs-7 mb-1" style="color: #334155;">Precio de Venta <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text border-end-0 py-1" style="background: #ffffff; border-color: #cbd5e1; color: #94a3b8; font-weight: 500; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                    <i data-lucide="dollar-sign" style="width:14px; height:14px;"></i>
                                </span>
                                <input type="text" inputmode="numeric" class="form-control format-number border-start-0 py-1 @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required placeholder="Ej. 15.000" style="border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; padding-left: 0; font-size: 0.9rem;">
                            </div>
                            @error('price') <div class="text-danger fs-8 mt-1">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="col-12 mt-2">
                            <div class="p-2 px-3 rounded-3 d-flex justify-content-between align-items-center shadow-sm" style="background: linear-gradient(135deg, #ecfdf5 0%, #dcfce7 100%); border: 1px solid #a7f3d0;">
                                <div>
                                    <span class="d-block fw-bold" style="color: #065f46; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Utilidad Neta</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span id="profit-percentage-display" class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.85rem;">0.00%</span>
                                    <div class="fw-bolder" style="color: #064e3b; font-size: 1.2rem; letter-spacing: -0.5px;">
                                        $<span id="profit-display-text">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 1rem 1.5rem;">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; color: #374151;">Cancelar</button>
            <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-2" style="background: #3b82f6; border-radius: 8px;">
                <i data-lucide="save" style="width: 16px; height: 16px;"></i> Guardar Producto
            </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modales de Ver Producto (Uno por cada fila) -->
@foreach($products as $product)
<div class="modal fade" id="showProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
      <div class="modal-header border-bottom-0 pb-0" style="background-color: #ffffff; padding: 1.25rem 1.5rem;">
        <h5 class="modal-title fw-bold" style="color: #0f172a; font-size: 1.15rem;">Detalles del Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3" style="background-color: #ffffff;">
          <div class="d-flex flex-column gap-3">
              
              <!-- Sección Información General -->
              <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                  <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                      <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #3b82f6; border: 1px solid #e2e8f0;">
                          <i data-lucide="package" style="width: 14px; height: 14px;"></i>
                      </div>
                      <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Información General</h6>
                  </div>
                  
                  <div class="mb-2 d-flex justify-content-between align-items-center">
                      <span class="fw-semibold fs-7" style="color: #64748b;">Nombre del Producto</span>
                      <span class="fw-bold" style="color: #1e293b; font-size: 0.95rem;">{{ $product->name }}</span>
                  </div>
                  <div class="mb-2 d-flex justify-content-between align-items-center">
                      <span class="fw-semibold fs-7" style="color: #64748b;">Cantidad Inicial</span>
                      <span class="fw-bold {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}" style="font-size: 0.95rem;">
                          {{ $product->stock }} {{ $product->stock > 0 ? '(Disponible)' : '(Agotado)' }}
                      </span>
                  </div>
                  <div class="mb-1 d-flex justify-content-between align-items-center">
                      <span class="fw-semibold fs-7" style="color: #64748b;">Fecha de Registro</span>
                      <span class="fw-bold" style="color: #1e293b; font-size: 0.95rem;">{{ $product->created_at ? $product->created_at->format('d M Y') : 'N/A' }}</span>
                  </div>
              </div>

              <!-- Sección Precios y Utilidad -->
              <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                  <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                      <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #10b981; border: 1px solid #e2e8f0;">
                          <i data-lucide="tags" style="width: 14px; height: 14px;"></i>
                      </div>
                      <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Análisis Financiero</h6>
                  </div>
                  
                  <div class="row g-3">
                      <div class="col-sm-6">
                          <div class="p-2 rounded" style="background: #ffffff; border: 1px solid #e2e8f0;">
                              <span class="d-block fw-semibold" style="color: #64748b; font-size: 0.75rem;">Costo Compra</span>
                              <span class="fw-bold" style="color: #0f172a; font-size: 1rem;">${{ number_format($product->cost, 0, ',', '.') }}</span>
                          </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="p-2 rounded" style="background: #ffffff; border: 1px solid #e2e8f0;">
                              <span class="d-block fw-semibold" style="color: #64748b; font-size: 0.75rem;">Precio Venta</span>
                              <span class="fw-bold" style="color: #0f172a; font-size: 1rem;">${{ number_format($product->price, 0, ',', '.') }}</span>
                          </div>
                      </div>
                      
                      <div class="col-12 mt-2">
                          <div class="p-2 px-3 rounded-3 d-flex justify-content-between align-items-center shadow-sm" style="background: linear-gradient(135deg, #ecfdf5 0%, #dcfce7 100%); border: 1px solid #a7f3d0;">
                              <div>
                                  <span class="d-block fw-bold" style="color: #065f46; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Utilidad Neta</span>
                              </div>
                              <div class="d-flex align-items-center gap-2">
                                  @php
                                      $showPorcentaje = $product->price > 0 ? (($product->price - $product->cost) / $product->price) * 100 : 0;
                                  @endphp
                                  <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.85rem;">{{ number_format($showPorcentaje, 2) }}%</span>
                                  <div class="fw-bolder" style="color: #064e3b; font-size: 1.2rem; letter-spacing: -0.5px;">
                                      ${{ number_format($product->price - $product->cost, 0, ',', '.') }}
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
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
@endforeach

<!-- Modales de Editar Producto -->
@foreach($products as $product)
<div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-labelledby="editProductModalLabel{{ $product->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
      <div class="modal-header border-bottom-0 pb-0" style="background-color: #ffffff; padding: 1.25rem 1.5rem;">
        <h5 class="modal-title fw-bold" id="editProductModalLabel{{ $product->id }}" style="color: #0f172a; font-size: 1.15rem;">Editar Producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body p-3" style="background-color: #ffffff;">
            <input type="hidden" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}">
            <input type="hidden" name="barcode" value="{{ old('barcode', $product->barcode) }}">

            <div class="d-flex flex-column gap-3">
                <!-- Sección Información General -->
                <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                        <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #3b82f6; border: 1px solid #e2e8f0;">
                            <i data-lucide="package" style="width: 14px; height: 14px;"></i>
                        </div>
                        <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Información General</h6>
                    </div>

                    <div class="mb-2">
                        <label for="edit_name_{{ $product->id }}" class="form-label fw-semibold fs-7 mb-1" style="color: #334155;">Nombre del Producto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name_{{ $product->id }}" name="name" value="{{ old('name', $product->name) }}" required style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);">
                    </div>
                    <div class="mb-2">
                        <label for="edit_stock_{{ $product->id }}" class="form-label fw-semibold fs-7 mb-1" style="color: #334155;">Cantidad Inicial <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror" id="edit_stock_{{ $product->id }}" name="stock" value="{{ old('stock', $product->stock) }}" required style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 0.4rem 0.75rem; font-size: 0.9rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="edit_status_{{ $product->id }}" {{ old('status', $product->status) === 'active' ? 'checked' : '' }} onchange="document.getElementById('edit_status_hidden_{{ $product->id }}').value = this.checked ? 'active' : 'inactive'">
                        <input type="hidden" id="edit_status_hidden_{{ $product->id }}" name="status" value="{{ old('status', $product->status) }}">
                        <label class="form-check-label fw-semibold fs-7" for="edit_status_{{ $product->id }}" style="color: #334155;">Producto activo (visible para vender)</label>
                    </div>
                </div>

                <!-- Sección Precios y Utilidad -->
                <div class="p-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: #cbd5e1 !important;">
                        <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 28px; height: 28px; background: #ffffff; color: #10b981; border: 1px solid #e2e8f0;">
                            <i data-lucide="tags" style="width: 14px; height: 14px;"></i>
                        </div>
                        <h6 class="fw-bold m-0" style="color: #0f172a; font-size: 0.95rem;">Estructura de Precios</h6>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="edit_cost_{{ $product->id }}" class="form-label fw-semibold fs-7 mb-1" style="color: #334155;">Costo de Compra <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text border-end-0 py-1" style="background: #ffffff; border-color: #cbd5e1; color: #94a3b8; font-weight: 500; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                    <i data-lucide="dollar-sign" style="width:14px; height:14px;"></i>
                                </span>
                                <input type="text" inputmode="numeric" class="form-control format-number edit-cost-input" data-target="{{ $product->id }}" id="edit_cost_{{ $product->id }}" name="cost" value="{{ number_format(old('cost', $product->cost), 0, ',', '.') }}" required style="border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; padding-left: 0; font-size: 0.9rem;">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="edit_price_{{ $product->id }}" class="form-label fw-semibold fs-7 mb-1" style="color: #334155;">Precio de Venta <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm" style="border-radius: 8px;">
                                <span class="input-group-text border-end-0 py-1" style="background: #ffffff; border-color: #cbd5e1; color: #94a3b8; font-weight: 500; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                                    <i data-lucide="dollar-sign" style="width:14px; height:14px;"></i>
                                </span>
                                <input type="text" inputmode="numeric" class="form-control format-number edit-price-input" data-target="{{ $product->id }}" id="edit_price_{{ $product->id }}" name="price" value="{{ number_format(old('price', $product->price), 0, ',', '.') }}" required style="border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; padding-left: 0; font-size: 0.9rem;">
                            </div>
                        </div>
                        
                        <div class="col-12 mt-2">
                            <div class="p-2 px-3 rounded-3 d-flex justify-content-between align-items-center shadow-sm" style="background: linear-gradient(135deg, #ecfdf5 0%, #dcfce7 100%); border: 1px solid #a7f3d0;">
                                <div>
                                    <span class="d-block fw-bold" style="color: #065f46; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Utilidad Neta</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @php
                                        $editPorcentaje = $product->price > 0 ? (($product->price - $product->cost) / $product->price) * 100 : 0;
                                    @endphp
                                    <span id="edit-profit-percentage-{{ $product->id }}" class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.85rem;">{{ number_format($editPorcentaje, 2) }}%</span>
                                    <div class="fw-bolder" style="color: #064e3b; font-size: 1.2rem; letter-spacing: -0.5px;">
                                        $<span id="edit-profit-display-{{ $product->id }}">{{ number_format($product->price - $product->cost, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 1rem 1.5rem;">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; color: #374151;">Cancelar</button>
            <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center gap-2" style="background: #3b82f6; border-radius: 8px;">
                <i data-lucide="save" style="width: 16px; height: 16px;"></i> Actualizar Producto
            </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach



<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal Calculator Logic
        const costInput = document.getElementById('cost');
        const priceInput = document.getElementById('price');
        const profitDisplayText = document.getElementById('profit-display-text');

        if(costInput && priceInput && profitDisplayText) {
            function calculateProfit() {
                const cost = parseFloat(costInput.value.replace(/\./g, '')) || 0;
                const price = parseFloat(priceInput.value.replace(/\./g, '')) || 0;
                const profit = price - cost;

                let percentage = 0;
                if (price > 0) {
                    percentage = (profit / price) * 100;
                }

                if (costInput.value === '' && priceInput.value === '') {
                    profitDisplayText.textContent = '0.00';
                    const percentageDisplay = document.getElementById('profit-percentage-display');
                    if (percentageDisplay) percentageDisplay.textContent = '0.00%';
                    return;
                }

                profitDisplayText.textContent = profit.toLocaleString('es-CO');
                const percentageDisplay = document.getElementById('profit-percentage-display');
                if (percentageDisplay) percentageDisplay.textContent = percentage.toFixed(2) + '%';
            }

            costInput.addEventListener('input', calculateProfit);
            priceInput.addEventListener('input', calculateProfit);
            calculateProfit();
        }

        // Modal Calculator Logic for Edit Modals
        const editCostInputs = document.querySelectorAll('.edit-cost-input');
        const editPriceInputs = document.querySelectorAll('.edit-price-input');

        function calculateEditProfit(targetId) {
            const costInput = document.getElementById('edit_cost_' + targetId);
            const priceInput = document.getElementById('edit_price_' + targetId);
            const profitDisplay = document.getElementById('edit-profit-display-' + targetId);
            const percentageDisplay = document.getElementById('edit-profit-percentage-' + targetId);

            if (costInput && priceInput && profitDisplay) {
                const cost = parseFloat(costInput.value.replace(/\./g, '')) || 0;
                const price = parseFloat(priceInput.value.replace(/\./g, '')) || 0;
                const profit = price - cost;
                
                let percentage = 0;
                if (price > 0) {
                    percentage = (profit / price) * 100;
                }
                
                profitDisplay.textContent = profit.toLocaleString('es-CO');
                if (percentageDisplay) {
                    percentageDisplay.textContent = percentage.toFixed(2) + '%';
                }
            }
        }

        editCostInputs.forEach(input => {
            input.addEventListener('input', function() {
                calculateEditProfit(this.dataset.target);
            });
        });

        editPriceInputs.forEach(input => {
            input.addEventListener('input', function() {
                calculateEditProfit(this.dataset.target);
            });
        });

        // Formato de miles con puntos al escribir (Costo de Compra / Precio de Venta)
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

        // Asegurar que los íconos se rendericen en los modales si es necesario
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('shown.bs.modal', function () {
                lucide.createIcons();
            });
        });

        const createModal = document.getElementById('createProductModal');
        if (createModal) {
            createModal.addEventListener('shown.bs.modal', function () {
                document.getElementById('name').focus();
            });
        }
        // Auto-abrir modal si hay errores de validación
        @if($errors->any())
            var createModalElement = document.getElementById('createProductModal');
            if (createModalElement) {
                var createModal = new bootstrap.Modal(createModalElement);
                createModal.show();
            }
        @endif

        // Auto-abrir modal desde notificación
        @if(request()->has('show'))
            var showModalElement = document.getElementById('showProductModal{{ request("show") }}');
            if (showModalElement) {
                var showModal = new bootstrap.Modal(showModalElement);
                showModal.show();
            }
        @endif

        // SweetAlert Confirmación de Eliminación Destructiva
        window.confirmDeleteProduct = function(productId) {
            Swal.fire({
                title: '¡Peligro! Operación Destructiva',
                html: '<div class="text-start" style="font-size: 0.95rem;">' +
                      '<p>Estás a punto de eliminar este producto. Al hacerlo:</p>' +
                      '<ul class="text-danger fw-bold">' +
                      '<li>Se eliminarán TODAS las facturas donde se haya vendido.</li>' +
                      '<li>Se eliminarán TODOS los abonos de esas facturas.</li>' +
                      '<li>Se restará el total histórico de las finanzas y el dashboard.</li>' +
                      '</ul>' +
                      '<p class="mb-0">Esta acción <b>NO</b> se puede deshacer. ¿Deseas continuar?</p></div>',
                icon: 'warning',
                iconColor: '#ef4444',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Sí, borrar todo',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'shadow-lg border-0 rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-product-form-' + productId).submit();
                }
            });
        };
    });
</script>
@endsection
