@extends('layouts.admin')

@section('title', 'Nuevo Documento')

@section('content')
<div class="container-fluid px-2 px-md-3">
    
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0 fw-bold" style="color: #111827;">Ventas &bull; Nuevo Documento</h5>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card border-0" style="background: #ffffff; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
        <!-- Card Header -->
        <div class="card-header bg-transparent py-2 px-3 d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: 1px solid #f3f4f6 !important;">
            <div class="d-flex align-items-center gap-2">
                <div style="background: #eff6ff; padding: 6px; border-radius: 8px; border: 1px solid #bfdbfe;">
                    <i data-lucide="receipt" style="color: #2563eb; width: 16px; height: 16px;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0" style="color: #111827 !important; letter-spacing: -0.3px; font-size: 0.9rem;">Registrar Venta</h6>
                    <p class="mb-0 fs-7 d-flex align-items-center gap-2" style="color: #6b7280 !important; font-size: 0.75rem;">
                        <span style="width: 5px; height: 5px; background: #10B981; border-radius: 50%;"></span>
                        El sistema carga el Precio automáticamente.
                    </p>
                </div>
            </div>
        </div>

        <div class="card-body p-3">
            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.85rem; border-radius: 8px;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('invoices.store') }}" method="POST" id="salesForm">
                @csrf

                {{-- TIPO DE DOCUMENTO --}}
                @php $type = old('invoice_type', $invoiceType ?? 'paid'); @endphp
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="invoice_type" id="type_paid" value="paid" {{ $type==='paid'?'checked':'' }}>
                        <label class="btn btn-outline-success w-100 p-1 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 border-opacity-50" style="min-height: 38px;" for="type_paid">
                            <span style="font-size:1rem;">✅</span> <span class="fw-semibold" style="font-size: 0.8rem;">Pagada</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="invoice_type" id="type_credit" value="credit" {{ $type==='credit'?'checked':'' }}>
                        <label class="btn btn-outline-warning w-100 p-1 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 border-opacity-50" style="min-height: 38px;" for="type_credit">
                            <span style="font-size:1rem;">💳</span> <span class="fw-semibold" style="font-size: 0.8rem;">Crédito</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="invoice_type" id="type_quotation" value="quotation" {{ $type==='quotation'?'checked':'' }}>
                        <label class="btn btn-outline-primary w-100 p-1 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 border-opacity-50" style="min-height: 38px;" for="type_quotation">
                            <span style="font-size:1rem;">📄</span> <span class="fw-semibold" style="font-size: 0.8rem;">Cotización</span>
                        </label>
                    </div>
                </div>

                <div class="row g-3">
                    {{-- SECCIÓN CLIENTE --}}
                    <div class="col-lg-8">
                        <div class="p-2 rounded-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                            <h6 class="text-uppercase fw-bold mb-2" style="font-size: 0.65rem; color: #6b7280; letter-spacing: 0.5px;">👤 Datos del Cliente</h6>
                            <div class="row g-2">
                                <div class="col-12 position-relative">
                                    <input id="clientSearch" name="customer_name" placeholder="Nombre del cliente o razón social *" autocomplete="off" required class="form-control form-control-sm client-autocomplete-field" style="font-size: 0.75rem;">
                                    <div id="clientResults" class="d-none position-absolute w-100 mt-1 bg-white border rounded shadow z-3" style="max-height: 250px; overflow-y: auto;"></div>
                                </div>
                                <div class="col-sm-6 position-relative">
                                    <input id="cedula" name="customer_cedula" placeholder="Cédula / NIT" autocomplete="off" class="form-control form-control-sm client-autocomplete-field" style="font-size: 0.75rem;">
                                </div>
                                <div class="col-sm-6 position-relative">
                                    <input id="phone" name="customer_phone" placeholder="Celular / Teléfono" autocomplete="off" class="form-control form-control-sm client-autocomplete-field" style="font-size: 0.75rem;">
                                </div>
                                <div class="col-sm-6 position-relative">
                                    <input id="email" type="email" name="customer_email" placeholder="Correo electrónico" autocomplete="off" class="form-control form-control-sm client-autocomplete-field" style="font-size: 0.75rem;">
                                </div>
                                <div class="col-sm-6 position-relative">
                                    <input id="address" name="customer_address" placeholder="Dirección de entrega" autocomplete="off" class="form-control form-control-sm client-autocomplete-field" style="font-size: 0.75rem;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RESUMEN FINANCIERO --}}
                    <div class="col-lg-4">
                        <div class="p-2 rounded-3 h-100 d-flex flex-column" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                            <h6 class="text-uppercase fw-bold mb-2" style="font-size: 0.65rem; color: #6b7280; letter-spacing: 0.5px;">💰 Resumen</h6>
                            
                            <div id="creditBox" class="d-none mb-2 p-2 bg-white rounded shadow-sm" style="border: 1px solid #fcd34d;">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label text-warning fw-bold text-uppercase mb-0" style="font-size: 0.6rem;">📅 Vencimiento</label>
                                        <input type="date" name="due_date" class="form-control form-control-sm py-0 px-1" style="font-size: 0.75rem; height: 26px;">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-uppercase mb-0" style="font-size: 0.6rem; color: #6b7280;">💵 Abono Inicial</label>
                                        <input type="number" id="paidInput" name="paid" value="0" min="0" oninput="calc()" class="form-control form-control-sm text-success fw-bold font-monospace bg-light py-0 px-1" style="font-size: 0.75rem; height: 26px;">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto pt-2 border-top">
                                <div class="d-flex justify-content-between mb-1" style="color: #4b5563; font-size: 0.75rem;">
                                    <span>Subtotal:</span>
                                    <span class="font-monospace">$<span id="subtotalPreview">0</span></span>
                                </div>
                                <div class="d-flex justify-content-between text-danger mb-1" style="font-size: 0.75rem;">
                                    <span>Descuento:</span>
                                    <span class="font-monospace">-$<span id="discountPreview">0</span></span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold pt-1 border-top" style="color: #111827; font-size: 0.9rem;">
                                    <span>TOTAL:</span>
                                    <span class="font-monospace">$<span id="totalPreview">0</span></span>
                                </div>
                                <div id="balanceRow" class="d-flex justify-content-between text-warning fw-bold d-none pt-1 border-top mt-1" style="font-size: 0.9rem;">
                                    <span>PENDIENTE:</span>
                                    <span class="font-monospace">$<span id="balancePreview">0</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN PRODUCTOS --}}
                <div class="mt-3 p-2 rounded-3" style="background: #f9fafb; border: 1px solid #e5e7eb;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-uppercase fw-bold mb-0" style="font-size: 0.65rem; color: #6b7280; letter-spacing: 0.5px;">🛒 Detalle de Venta</h6>
                        <button type="button" onclick="addRow()" class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-sm py-1" style="border-radius: 4px; padding: 0 10px; background: #2563eb; border: none; font-size: 0.75rem;">
                            <i data-lucide="plus" style="width: 12px; height: 12px;"></i> Añadir
                        </button>
                    </div>

                    <div class="row g-1 d-none d-lg-flex mb-1 px-1 text-uppercase fw-semibold" style="font-size: 0.6rem; color: #6b7280;">
                        <div class="col-4">Producto / Servicio</div>
                        <div class="col-2 text-center">Cant.</div>
                        <div class="col-3 text-center">Precio Unit.</div>
                        <div class="col-2 text-center">Desc. %</div>
                        <div class="col-1"></div>
                    </div>

                    <div id="items" class="d-flex flex-column gap-1"></div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                    <a href="{{ route('invoices.index') }}" class="btn fw-semibold py-1 px-3" style="border: 1px solid #d1d5db; color: #374151; background: #ffffff; font-size: 0.8rem;">Cancelar</a>
                    <button type="submit" class="btn text-white fw-semibold shadow-sm py-1 px-3" style="background: #10b981; border: none; font-size: 0.8rem;">Guardar</button>
                </div>
            </form>
        </div>
    </div>
        </div>
    </div>
</div>

<script>
const products = @json($products);
let index = 0;

const creditBox = document.getElementById('creditBox');
const paidInput = document.getElementById('paidInput');
const items = document.getElementById('items');

function toggleType(){
    const type = document.querySelector('input[name="invoice_type"]:checked').value;
    if (type === 'credit') {
        creditBox.classList.remove('d-none');
    } else {
        creditBox.classList.add('d-none');
        paidInput.value = 0;
    }
    calc();
}

document.querySelectorAll('input[name="invoice_type"]').forEach(r =>
    r.addEventListener('change', toggleType)
);

function addRow(){
    const rowId = `row-${index}`;
    let options = `<option value="" data-price="0">-- Producto --</option>`;
    products.forEach(p => {
        options += `<option value="${p.id}" data-price="${p.price || 0}" data-stock="${p.stock || 0}">${p.name} (Stock: ${p.stock})</option>`;
    });

    const html = `
        <div id="${rowId}" class="row g-1 align-items-center bg-white p-1 rounded shadow-sm mx-0" style="border: 1px solid #e5e7eb;">
            <div class="col-12 col-lg-4">
                <select name="products[${index}][product_id]" onchange="updateRowPrice(this)" class="form-select form-select-sm" style="font-size: 0.75rem; border-color: #d1d5db; min-height: 28px; padding-top: 2px; padding-bottom: 2px;" required>
                    ${options}
                </select>
            </div>
            <div class="col-4 col-lg-2">
                <input type="number" name="products[${index}][quantity]" class="quantity form-control form-control-sm text-center fw-bold" style="font-size: 0.75rem; border-color: #d1d5db; min-height: 28px; padding-top: 2px; padding-bottom: 2px;" value="1" min="1" oninput="calc()">
            </div>
            <div class="col-4 col-lg-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-success fw-bold py-0" style="border-color: #d1d5db; border-right: none;">$</span>
                    <input type="number" name="products[${index}][price]" class="price-input form-control form-control-sm text-success fw-bold font-monospace ps-0" style="font-size: 0.75rem; border-color: #d1d5db; border-left: none; min-height: 28px; padding-top: 2px; padding-bottom: 2px;" step="0.01" oninput="calc()" required>
                </div>
            </div>
            <div class="col-3 col-lg-2">
                <div class="input-group input-group-sm">
                    <input type="number" name="products[${index}][discount]" class="discount-item form-control form-control-sm text-danger text-center fw-bold" style="font-size: 0.75rem; border-color: #d1d5db; min-height: 28px; padding-top: 2px; padding-bottom: 2px;" value="0" min="0" max="100" oninput="calc()">
                    <span class="input-group-text bg-white px-1 text-muted py-0" style="border-color: #d1d5db;">%</span>
                </div>
            </div>
            <div class="col-1 d-flex justify-content-end justify-content-lg-center">
                <button type="button" onclick="document.getElementById('${rowId}').remove(); calc()" class="btn btn-sm p-0 d-flex align-items-center justify-content-center" style="background: #fee2e2; color: #ef4444; border: none; width: 22px; height: 22px; border-radius: 4px;">
                    <i data-lucide="x" style="width: 12px; height: 12px;"></i>
                </button>
            </div>
        </div>`;
    
    items.insertAdjacentHTML('beforeend', html);
    if(typeof lucide !== 'undefined') lucide.createIcons();
    index++;
}

function updateRowPrice(select) {
    const selectedOption = select.options[select.selectedIndex];
    const precioVenta = selectedOption.getAttribute('data-price');
    const stock = selectedOption.getAttribute('data-stock');
    const row = select.closest('.row');
    const priceInput = row.querySelector('.price-input');
    const qtyInput = row.querySelector('.quantity');
    
    if (priceInput) {
        priceInput.value = precioVenta;
    }
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
    calc(); 
}

function calc(){
    let subtotal = 0;
    let totalDiscount = 0;
    
    const abono = parseFloat(document.getElementById('paidInput').value || 0);

    const rows = items.querySelectorAll('div[id^="row-"]');
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
    const pendiente = totalFinal - abono;

    document.getElementById('subtotalPreview').textContent = subtotal.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2});
    document.getElementById('discountPreview').textContent = totalDiscount.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2});
    document.getElementById('totalPreview').textContent = totalFinal.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2});

    const type = document.querySelector('input[name="invoice_type"]:checked').value;
    const balanceRow = document.getElementById('balanceRow');
    
    if(type === 'credit') {
        balanceRow.classList.remove('d-none');
        document.getElementById('balancePreview').textContent = pendiente.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 2});
    } else {
        balanceRow.classList.add('d-none');
    }
}

const resultsBox = document.getElementById('clientResults');

document.querySelectorAll('.client-autocomplete-field').forEach(input => {
    input.addEventListener('input', async () => {
        const q = input.value.trim();
        if (q.length < 2) { 
            resultsBox.classList.add('d-none'); 
            return; 
        }
        
        // Move resultsBox under the active input container
        input.parentNode.appendChild(resultsBox);
        
        try {
            const res = await fetch(`{{ route('clients.autocomplete') }}?q=${q}`);
            const data = await res.json();
            resultsBox.innerHTML = '';
            
            if (data.length === 0) {
                resultsBox.classList.add('d-none');
                return;
            }
            
            data.forEach(c => {
                resultsBox.innerHTML += `
                    <div class="px-3 py-2 border-bottom" style="cursor:pointer; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'" onclick="selectClient(${JSON.stringify(c).replace(/"/g,'&quot;')})">
                        <strong class="text-dark" style="font-size: 0.75rem;">${c.name}</strong> <span class="text-muted ms-2" style="font-size: 0.7rem;">· ${c.cedula || 'Sin CC'}</span>
                    </div>`;
            });
            resultsBox.classList.remove('d-none');
        } catch (e) { 
            console.error("Error clientes", e); 
        }
    });
});

document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('client-autocomplete-field') && e.target !== resultsBox && !resultsBox.contains(e.target)) {
        resultsBox.classList.add('d-none');
    }
});

function selectClient(c){
    document.getElementById('clientSearch').value = c.name;
    document.getElementById('cedula').value = c.cedula || '';
    document.getElementById('phone').value = c.cellphone || '';
    document.getElementById('email').value = c.email || '';
    document.getElementById('address').value = c.address || '';
    resultsBox.classList.add('d-none');
}

document.getElementById('salesForm').addEventListener('submit', function(e) {
    const type = document.querySelector('input[name="invoice_type"]:checked').value;
    const client = document.getElementById('clientSearch').value.trim();
    const productSelects = document.querySelectorAll('#items select');
    
    let hasEmptyProduct = false;
    productSelects.forEach(select => {
        if (!select.value) {
            hasEmptyProduct = true;
        }
    });

    let missing = [];
    if (!client) missing.push("<strong>Falta el Cliente:</strong> Debes escribir y seleccionar el cliente para saber a quién se le genera el documento.");
    
    if (type === 'credit') {
        const dueDate = document.querySelector('input[name="due_date"]').value;
        if (!dueDate) missing.push("<strong>Falta la Fecha de Vencimiento:</strong> Al ser una venta a crédito, es obligatorio indicar la fecha límite de pago para el control de cartera.");
    }
    
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
            submitBtn.innerHTML = submitBtn.dataset.originalHtml || 'Guardar';
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

window.onload = () => {
    addRow(); 
    toggleType();
};
</script>
@endsection
