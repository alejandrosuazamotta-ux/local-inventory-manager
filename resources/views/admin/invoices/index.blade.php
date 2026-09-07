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
        <h5 class="m-0 fw-bold" style="color: #111827;">Facturas &bull; Historial</h5>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 mb-4" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
        <div class="card-body p-3">
            <form action="{{ route('invoices.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-12">
                    <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden; background: #fff; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-transparent border-0" style="color: #94a3b8; padding-right: 8px;">
                            <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 ps-0 shadow-none auto-submit" placeholder="Buscar por número de factura o nombre de cliente..." value="{{ request('search') }}" style="font-size: 0.95rem; background: transparent;">
                        @if(request('search'))
                            <a href="{{ route('invoices.index') }}" class="input-group-text bg-transparent border-0 text-danger text-decoration-none" title="Limpiar Búsqueda">
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
                    <i data-lucide="file-text" style="color: #2563eb; width: 18px; height: 18px;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color: #111827 !important; letter-spacing: -0.3px; font-size: 0.95rem;">Historial de Facturas</h6>
                    <p class="mb-0 fs-7 d-flex align-items-center gap-2" style="color: #6b7280 !important;">
                        <span style="width: 5px; height: 5px; background: #10B981; border-radius: 50%;"></span>
                        Registro de todas las facturas cobradas en su totalidad
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                <a href="{{ route('invoices.export') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #10b981; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(16, 185, 129, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-spreadsheet" style="width: 14px; height: 14px;"></i> Exportar Excel
                </a>
                <a href="{{ route('invoices.export.pdf') }}" class="btn btn-sm d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #ef4444; color: white; border: none; box-shadow: 0 2px 4px -1px rgba(220, 38, 38, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> Exportar PDF
                </a>
                <a href="{{ route('sales.create') }}" class="btn btn-sm btn-primary d-inline-flex justify-content-center align-items-center gap-1 fw-semibold" style="border-radius: 6px; padding: 0 12px; background: #2563eb; border: none; box-shadow: 0 2px 4px -1px rgba(37, 99, 235, 0.2); height: 32px; font-size: 0.8rem;">
                    <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Nueva factura
                </a>
            </div>
        </div>

        <!-- Table Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless mb-0 align-middle" style="color: #374151;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                            <th class="fw-semibold text-uppercase ps-3 py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem; width: 40px;">#</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">NO. FACTURA</th>
                            <th class="fw-semibold text-uppercase py-2" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">CLIENTE</th>
                            <th class="fw-semibold text-uppercase py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">FECHA</th>
                            <th class="fw-semibold text-uppercase py-2 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">DESCUENTOS</th>
                            <th class="fw-semibold text-uppercase py-2 text-end" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">TOTAL</th>
                            <th class="fw-semibold text-uppercase pe-3 py-2 text-center" style="color: #6b7280; letter-spacing: 0.5px; font-size: 0.7rem;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr class="table-row-hover" style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.2s;">
                                <td class="ps-3 py-2 text-center text-secondary fw-semibold" style="font-size: 0.8rem;">
                                    {{ $invoices->firstItem() ? $invoices->firstItem() + $loop->index : $loop->iteration }}
                                </td>
                                <td class="py-2 fw-semibold text-primary" style="font-size: 0.8rem;">
                                    #FAC-{{ $invoice->document_number }}
                                </td>
                                <td class="py-2">
                                    <div class="d-flex flex-column justify-content-center">
                                        <span class="fw-bold" style="color: #111827; font-size: 0.8rem;">{{ $invoice->customer->name ?? 'Consumidor Final' }}</span>
                                    </div>
                                </td>
                                <td class="text-center py-2 text-secondary" style="font-size: 0.8rem;">
                                    {{ $invoice->created_at->format('d/m/Y') }}
                                </td>
                                @php
                                    $totalDiscount = $invoice->details->sum(function($detail) {
                                        return ($detail->unit_price * $detail->quantity) - $detail->subtotal;
                                    });
                                @endphp
                                <td class="fw-bold py-2 text-end text-danger" style="font-size: 0.8rem;">
                                    ${{ number_format($totalDiscount, 0, ',', '.') }}
                                </td>
                                <td class="fw-bold py-2 text-end text-success" style="font-size: 0.8rem;">
                                    ${{ number_format($invoice->total, 0, ',', '.') }}
                                </td>
                                <td class="text-center pe-3 py-1">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- View Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #f3f4f6; border: 1px solid #e5e7eb;" title="Ver" data-bs-toggle="modal" data-bs-target="#modalInvoice{{ $invoice->id }}">
                                            <i data-lucide="eye" style="color: #4b5563; width: 12px; height: 12px;"></i>
                                        </button>
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fef3c7; border: 1px solid #fde68a;" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditSale{{ $invoice->id }}">
                                            <i data-lucide="edit-2" style="color: #d97706; width: 12px; height: 12px;"></i>
                                        </button>
                                        <!-- PDF Button -->
                                        <a href="{{ route('invoices.download.pdf', $invoice) }}" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 26px; height: 26px; background: #fee2e2; border: 1px solid #fca5a5;" title="Descargar PDF">
                                            <i data-lucide="file-down" style="color: #dc2626; width: 12px; height: 12px;"></i>
                                        </a>
                                        <!-- Delete Button -->
                                        <form action="{{ route('sales.destroy', $invoice) }}" method="POST" class="d-inline" onsubmit="confirmDelete(event, '¿Estás seguro de que deseas eliminar esta factura?');">
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
                                    <i data-lucide="file-text" class="mb-3 d-block mx-auto" style="width: 48px; height: 48px; opacity: 0.5; color: #9ca3af;"></i>
                                    <p class="mb-0">Aún no hay facturas registradas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($invoices->hasPages())
        <div class="card-footer bg-transparent py-3 d-flex align-items-center justify-content-between custom-pagination-container" style="border-top: 1px solid #f3f4f6;">
            {{ $invoices->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

@foreach($invoices as $invoice)
<!-- Invoice Modal -->
                            <div class="modal fade" id="modalInvoice{{ $invoice->id }}" tabindex="-1" aria-labelledby="modalInvoiceLabel{{ $invoice->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                                        <div class="modal-header border-0 pb-0 pt-3 px-3">
                                            <h5 class="modal-title fw-bold" id="modalInvoiceLabel{{ $invoice->id }}" style="color: #0f172a; font-size: 1.1rem;">Detalles de la Factura</h5>
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
                                                                <span class="fw-bold text-end" style="color: #0f172a; font-size: 0.8rem;">{{ $invoice->customer->name ?? 'Consumidor Final' }}</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between mb-1 pb-1 border-bottom" style="border-color: #e2e8f0 !important;">
                                                                <span class="fw-semibold" style="color: #64748b; font-size: 0.8rem;">No. Factura</span>
                                                                <span class="fw-bold text-end" style="color: #10b981; font-size: 0.8rem;">#FAC-{{ $invoice->document_number }}</span>
                                                            </div>
                                                            <div class="d-flex justify-content-between">
                                                                <span class="fw-semibold" style="color: #64748b; font-size: 0.8rem;">Fecha de Emisión</span>
                                                                <span class="fw-bold text-end" style="color: #0f172a; font-size: 0.8rem;">{{ $invoice->created_at->format('d M Y') }}</span>
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
                                                            
                                                            <div class="p-2 mb-0" style="background: #d1fae5; border: 1px solid #a7f3d0; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                                                                <span class="fw-bold text-success" style="font-size: 0.8rem; letter-spacing: 0.5px;">TOTAL</span>
                                                                <span class="fw-bold text-success" style="font-size: 1rem;">${{ number_format($invoice->total, 0, ',', '.') }}</span>
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
                                                                @forelse($invoice->details as $detail)
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
                                        <div class="modal-footer border-0 pt-0 pb-3 px-3">
                                            <button type="button" class="btn btn-primary w-100 fw-bold py-2" data-bs-dismiss="modal" style="border-radius: 8px; background: #3b82f6; border: none; font-size: 0.9rem;">Entendido</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="modalEditSale{{ $invoice->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                                            <h5 class="modal-title fw-bold" style="color: #0f172a;">Editar Documento #{{ $invoice->document_number }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('sales.update', $invoice->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="invoice_type" value="{{ $invoice->invoice_type }}">
                                            
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <!-- Info General -->
                                                    <div class="col-lg-4">
                                                        <div class="p-2 rounded-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                                                            <h6 class="text-uppercase fw-bold mb-2" style="font-size: 0.65rem; color: #6b7280;">👤 Datos del Cliente</h6>
                                                            <div class="mb-2">
                                                                <input type="text" class="form-control form-control-sm" name="customer_name" value="{{ $invoice->customer->name ?? '' }}" placeholder="Nombre" required>
                                                            </div>
                                                            <div class="mb-2">
                                                                <input type="text" class="form-control form-control-sm" name="customer_cedula" value="{{ $invoice->customer->document_number ?? '' }}" placeholder="Cédula/NIT">
                                                            </div>
                                                            <div class="mb-2">
                                                                <input type="text" class="form-control form-control-sm" name="customer_phone" value="{{ $invoice->customer->phone ?? '' }}" placeholder="Teléfono">
                                                            </div>
                                                            <div class="mb-2">
                                                                <input type="text" class="form-control form-control-sm" name="customer_address" value="{{ $invoice->customer->address ?? '' }}" placeholder="Dirección">
                                                            </div>
                                                            
                                                        </div>
                                                        <div class="p-2 rounded-3 mt-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                                                            <h6 class="text-uppercase fw-bold mb-2" style="font-size: 0.65rem; color: #6b7280;">💰 Resumen</h6>
                                                            <div class="d-flex justify-content-between mb-1" style="font-size: 0.75rem;">
                                                                <span>Subtotal:</span>
                                                                <span class="font-monospace">$<span id="subtotalPreview-{{ $invoice->id }}">0</span></span>
                                                            </div>
                                                            <div class="d-flex justify-content-between text-danger mb-1" style="font-size: 0.75rem;">
                                                                <span>Descuento:</span>
                                                                <span class="font-monospace">-$<span id="discountPreview-{{ $invoice->id }}">0</span></span>
                                                            </div>
                                                            <div class="d-flex justify-content-between fw-bold pt-1 border-top" style="font-size: 0.9rem;">
                                                                <span>TOTAL:</span>
                                                                <span class="font-monospace">$<span id="totalPreview-{{ $invoice->id }}">0</span></span>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Productos -->
                                                    <div class="col-lg-8">
                                                        <div class="p-2 rounded-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <h6 class="text-uppercase fw-bold mb-0" style="font-size: 0.65rem; color: #6b7280;">🛒 Detalle de Venta</h6>
                                                                <button type="button" onclick="addRowEdit({{ $invoice->id }})" class="btn btn-sm btn-primary py-1" style="font-size: 0.7rem;">+ Añadir</button>
                                                            </div>
                                                            <div class="row g-1 d-none d-lg-flex mb-1 px-1 fw-semibold text-uppercase" style="font-size: 0.6rem; color: #6b7280;">
                                                                <div class="col-4">Producto</div>
                                                                <div class="col-2 text-center">Cant.</div>
                                                                <div class="col-3 text-center">Precio U.</div>
                                                                <div class="col-2 text-center">Desc %</div>
                                                                <div class="col-1"></div>
                                                            </div>
                                                            <div id="items-{{ $invoice->id }}" class="d-flex flex-column gap-1"></div>
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
                                            document.getElementById('modalEditSale{{ $invoice->id }}').addEventListener('show.bs.modal', function () {
                                                const container = document.getElementById('items-{{ $invoice->id }}');
                                                if(container.children.length === 0) {
                                                    const details = @json($invoice->details);
                                                    if(details.length > 0) {
                                                        details.forEach(d => addRowEdit({{ $invoice->id }}, d));
                                                    } else {
                                                        addRowEdit({{ $invoice->id }});
                                                    }
                                                    setTimeout(() => calcEdit({{ $invoice->id }}), 100);
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
        options += `<option value="${p.id}" data-price="${p.price || 0}" ${selected}>${p.name} (Stock: ${p.stock})</option>`;
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
    const row = select.closest('.row');
    const priceInput = row.querySelector('.price-input');
    if (priceInput) priceInput.value = precioVenta;
    calcEdit(saleId); 
}

function calcEdit(saleId){
    let subtotal = 0;
    let totalDiscount = 0;
    
    const itemsContainer = document.getElementById(`items-${saleId}`);
    if(!itemsContainer) return;
    const rows = itemsContainer.querySelectorAll('div[id^="row-"]');
    
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.quantity').value || 0);
        const price = parseFloat(row.querySelector('.price-input').value || 0);
        const discPercent = parseFloat(row.querySelector('.discount-item').value || 0);

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
</script>
@endsection
