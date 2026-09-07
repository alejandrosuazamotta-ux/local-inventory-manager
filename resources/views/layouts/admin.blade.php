<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Mi Negocio') }} - Panel de Administración</title>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="{{ asset('vendor/lucide/lucide.min.js') }}"></script>
    <!-- Chart.js -->
    <script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
    <!-- Fuentes (autohospedadas para funcionar sin internet) -->
    <link href="{{ asset('vendor/fonts/inter.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/admin/admin.css'])
    @stack('styles')
</head>
<body>
    @auth
    <div class="wrapper" style="display: flex; width: 100%; min-height: 100vh;">
        <!-- Sidebar -->
        <aside class="sidebar-container" style="width: 230px; flex-shrink: 0; display: flex; flex-direction: column; z-index: 10; background-color: var(--sidebar-bg); border-right: 1px solid var(--sidebar-border); box-shadow: 2px 0 10px rgba(0,0,0,0.02);">
            <!-- Sidebar Header removed, moved to top header -->
            
            <div class="collapse d-md-flex flex-column flex-grow-1 w-100" id="mobileSidebar">
                <!-- Navigation Menu -->
                <nav class="nav-menu">
                
                <!-- Business Logo Section -->
                <div class="mb-4 d-flex justify-content-center align-items-center w-100" style="padding: 0 1rem; margin-top: 1rem;">
                    @php $hasLogo = file_exists(public_path('img/logo.png')); @endphp
                    @if($hasLogo)
                        <div data-bs-toggle="modal" data-bs-target="#logoModal" class="d-flex justify-content-center align-items-center w-100" style="cursor: pointer; transition: transform 0.2s; padding: 0.5rem;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';" title="Haz clic para ver el logo completo">
                            <img src="{{ asset('img/logo.png') }}?v={{ time() }}" alt="Logo" style="max-width: 100%; max-height: 120px; object-fit: contain;">
                        </div>
                    @else
                        <div class="text-center py-3">
                            <h5 class="fw-bold m-0" style="color: #0f172a;">{{ config('app.name', 'Mi Negocio') }}</h5>
                        </div>
                    @endif
                </div>

                <p class="nav-label mt-2">General</p>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i data-lucide="layout-dashboard"></i> <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                </ul>

                <p class="nav-label">Finanzas</p>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a href="{{ route('portfolio.index') }}" class="nav-link {{ request()->routeIs('portfolio.index') ? 'active' : '' }}">
                            <i data-lucide="wallet"></i> <span class="nav-text">Contabilidad</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                            <i data-lucide="calculator"></i> <span class="nav-text">Gastos</span>
                        </a>
                    </li>
                </ul>

                <p class="nav-label">Administración</p>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i data-lucide="box"></i> <span class="nav-text">Inventario</span>
                        </a>
                    </li>
                </ul>

                <p class="nav-label">Proveedores</p>
                <ul class="nav flex-column mb-3">
                    <li class="nav-item">
                        <a href="{{ route('supplier-debts.index') }}" class="nav-link {{ request()->routeIs('supplier-debts.*') ? 'active' : '' }}">
                            <i data-lucide="truck"></i> <span class="nav-text">Deudas Proveedores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('cartera-antigua.index') }}" class="nav-link {{ request()->routeIs('cartera-antigua.*') ? 'active' : '' }}">
                            <i data-lucide="folder-archive"></i> <span class="nav-text">Cartera Antigua</span>
                        </a>
                    </li>
                </ul>

                <!-- Menú Facturación -->
                <div class="nav-item mb-3 mt-1">
                    <div class="d-flex align-items-center justify-content-between nav-label-accordion mb-2" data-bs-toggle="collapse" data-bs-target="#facturacionMenu" aria-expanded="true" style="cursor: pointer; padding: 8px 10px;">
                        <div class="d-flex align-items-center gap-2">
                            <i data-lucide="file-spreadsheet" style="width: 18px; height: 18px; color: #10b981;"></i>
                            <span class="nav-label m-0 p-0" style="color: #94a3b8; font-size: 0.75rem;">FACTURACIÓN</span>
                        </div>
                        <i data-lucide="chevron-up" style="width: 16px; height: 16px; color: #94a3b8;" class="collapse-icon"></i>
                    </div>
                    
                    <div class="collapse show" id="facturacionMenu">
                        <ul class="nav flex-column ms-3" style="border-left: 1px solid #e2e8f0; padding-left: 0.5rem; margin-top: 0.5rem; margin-bottom: 0.5rem;">
                            <li class="nav-item">
                                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                                    <i data-lucide="users"></i> <span class="nav-text">Clientes</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                                    <i data-lucide="file-text"></i> <span class="nav-text">Facturas</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('quotes.index') }}" class="nav-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
                                    <i data-lucide="file-box"></i> <span class="nav-text">Cotizaciones</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('credits.index') }}" class="nav-link {{ request()->routeIs('credits.*') ? 'active' : '' }}">
                                    <i data-lucide="credit-card"></i> <span class="nav-text">Créditos</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                                    <i data-lucide="hand-coins"></i> <span class="nav-text">Abonos</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            </nav>
            
                <!-- Sidebar Footer -->
                <div class="sidebar-footer mt-auto">
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="logout-btn d-flex align-items-center gap-2">
                            <i data-lucide="log-out" style="color: #ef4444; width: 22px; height: 22px;"></i> 
                            <span style="color: #ef4444; font-weight: 500; font-size: 0.95rem;">Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Area -->
        <main class="main-content" style="flex-grow: 1; display: flex; flex-direction: column; overflow: hidden; background-color: var(--bg-main);">
            <!-- Top Header (Logged User Info) -->
            <header class="top-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn d-md-none p-1" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSidebar" aria-expanded="false" aria-controls="mobileSidebar" style="border: none;">
                        <i data-lucide="menu" style="width: 24px; height: 24px; color: #0f172a;"></i>
                    </button>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded d-flex justify-content-center align-items-center shadow-sm" style="background-color: #0f172a; width: 34px; height: 34px;">
                            <i data-lucide="store" style="width: 18px; height: 18px; color: white;"></i>
                        </div>
                        <h1 class="m-0 fw-bold" style="font-size: 1.15rem; color: #0f172a; letter-spacing: -0.02em;">{{ config('app.name', 'Mi Negocio') }}</h1>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <!-- Notification Bell -->
                    @php
                        $lowStockProducts = \App\Models\Product::where('stock', '<', 10)->get();
                        $lowStockCount = $lowStockProducts->count();
                        
                        $recentDiscounts = \App\Models\Sale::where('discount', '>', 0)->with('customer')->orderBy('created_at', 'desc')->take(5)->get();
                        $discountCount = $recentDiscounts->count();
                    @endphp
                    <!-- Discounts Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light rounded-circle p-2 position-relative d-flex align-items-center justify-content-center" type="button" id="discountDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 38px; height: 38px; border: 1px solid #e2e8f0; background-color: #f8fafc;">
                            <i data-lucide="tag" style="width: 18px; height: 18px; color: #8b5cf6;"></i>
                            @if($discountCount > 0)
                                <span class="position-absolute badge rounded-pill bg-purple border border-white" style="font-size: 0.65rem; padding: 0.25em 0.4em; top: -2px; right: -2px; box-shadow: 0 2px 4px rgba(139, 92, 246, 0.4); background-color: #8b5cf6; color: white;">
                                    {{ $discountCount }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" aria-labelledby="discountDropdown" style="width: 320px; border-radius: 12px; padding: 0;">
                            <div class="p-3 border-bottom bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                                <h6 class="mb-0 fw-bold" style="color: #0f172a;">Últimos Descuentos Aplicados</h6>
                            </div>
                            <div class="custom-scrollbar" style="max-height: 320px; overflow-y: auto;">
                                @forelse($recentDiscounts as $sale)
                                    @php
                                        $targetRoute = $sale->invoice_type === 'credit' ? route('credits.index') : route('invoices.index');
                                        $invoicePrefix = $sale->invoice_type === 'credit' ? '#CRD-' : '#FAC-';
                                    @endphp
                                    <li>
                                        <a class="dropdown-item py-2 px-3 border-bottom table-row-hover" href="{{ $targetRoute }}?search={{ urlencode($sale->document_number) }}" style="white-space: normal; transition: background-color 0.2s;">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="text-purple mt-1" style="color: #8b5cf6;">
                                                    <i data-lucide="gift" style="width: 16px; height: 16px;"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-semibold" style="font-size: 0.85rem; color: #1e293b; line-height: 1.2;">Factura {{ $invoicePrefix }}{{ $sale->document_number ?? $sale->id }}</p>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                        Cliente: {{ $sale->customer->name ?? 'Consumidor Final' }}
                                                    </small>
                                                    <small class="d-block mt-1 fw-bold" style="font-size: 0.75rem; color: #8b5cf6;">
                                                        Descuento: ${{ number_format($sale->discount, 0, ',', '.') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li>
                                        <div class="dropdown-item py-4 text-center text-muted" style="pointer-events: none;">
                                            <i data-lucide="info" class="mb-2" style="width: 24px; height: 24px; color: #94a3b8; opacity: 0.7;"></i>
                                            <p class="mb-0" style="font-size: 0.85rem;">No hay descuentos recientes</p>
                                        </div>
                                    </li>
                                @endforelse
                            </div>
                        </ul>
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn btn-light rounded-circle p-2 position-relative d-flex align-items-center justify-content-center" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 38px; height: 38px; border: 1px solid #e2e8f0; background-color: #f8fafc;">
                            <i data-lucide="bell" style="width: 18px; height: 18px; color: #475569;"></i>
                            @if($lowStockCount > 0)
                                <span class="position-absolute badge rounded-pill bg-danger border border-white" style="font-size: 0.65rem; padding: 0.25em 0.4em; top: -2px; right: -2px; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.4);">
                                    {{ $lowStockCount > 9 ? '9+' : $lowStockCount }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" aria-labelledby="notificationDropdown" style="width: 320px; border-radius: 12px; padding: 0;">
                            <div class="p-3 border-bottom bg-light" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                                <h6 class="mb-0 fw-bold" style="color: #0f172a;">Notificaciones de Stock</h6>
                            </div>
                            <div class="custom-scrollbar" style="max-height: 320px; overflow-y: auto;">
                                @forelse($lowStockProducts as $product)
                                    <li>
                                        <a class="dropdown-item py-2 px-3 border-bottom table-row-hover" href="{{ route('products.index') }}?search={{ urlencode($product->name) }}&show={{ $product->id }}" style="white-space: normal; transition: background-color 0.2s;">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="text-danger mt-1">
                                                    <i data-lucide="alert-triangle" style="width: 16px; height: 16px;"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-semibold" style="font-size: 0.85rem; color: #1e293b; line-height: 1.2;">{{ $product->name }}</p>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                        Quedan solo <strong class="text-danger">{{ $product->stock }}</strong> unidades.
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li>
                                        <div class="dropdown-item py-4 text-center text-muted" style="pointer-events: none;">
                                            <i data-lucide="check-circle-2" class="mb-2" style="width: 24px; height: 24px; color: #10b981; opacity: 0.7;"></i>
                                            <p class="mb-0" style="font-size: 0.85rem;">Stock en niveles óptimos</p>
                                        </div>
                                    </li>
                                @endforelse
                            </div>
                            @if($lowStockCount > 0)
                                <div class="p-2 border-top text-center bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                    <a href="{{ route('products.index') }}" class="text-decoration-none fw-bold" style="font-size: 0.75rem; color: #2563eb;">Gestionar Inventario <i data-lucide="arrow-right" style="width: 12px; height: 12px; margin-left: 2px;"></i></a>
                                </div>
                            @endif
                        </ul>
                    </div>

                    <div style="width: 1px; height: 28px; background-color: #e2e8f0; margin: 0 8px;"></div>

                    <div class="d-flex align-items-center gap-2" style="padding: 4px 8px; border-radius: 8px; transition: background-color 0.2s; cursor: default;">
                        <div class="rounded-circle d-flex justify-content-center align-items-center shadow-sm" style="width: 36px; height: 36px; background-color: #0f172a; color: #d4af37; border: 2px solid #ffffff;">
                            <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="d-none d-md-flex flex-column justify-content-center">
                            <p class="mb-0 fw-bold" style="font-size: 0.85rem; color: #0f172a !important; line-height: 1;">{{ Auth::user()->name ?? 'Administrador' }}</p>
                            <span class="d-flex align-items-center gap-1 mt-1" style="font-size: 0.7rem; color: #10b981 !important; font-weight: 500;">
                                <span style="width: 6px; height: 6px; background-color: #10b981; border-radius: 50%; display: inline-block;"></span>
                                En línea
                            </span>
                        </div>
                    </div>

                    <div style="width: 1px; height: 28px; background-color: #e2e8f0; margin: 0 4px;"></div>
                    
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn rounded-circle p-2 d-flex align-items-center justify-content-center btn-logout-top" title="Cerrar sesión" style="width: 38px; height: 38px; border: 1px solid #fecaca; background-color: #fef2f2; transition: all 0.2s;">
                            <i data-lucide="log-out" style="width: 18px; height: 18px; color: #ef4444;"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content -->
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>
    @else
        <!-- Pantalla Login -->
        <div class="login-wrapper">
            @yield('content')
        </div>
    @endauth

    <!-- Logo Modal -->
    <div class="modal fade" id="logoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="background-color: rgba(255,255,255,0.8); border-radius: 50%; padding: 0.5rem;"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <img src="{{ asset('img/logo.png') }}?v={{ time() }}" alt="Logo" class="img-fluid rounded" style="max-height: 80vh; background: white; padding: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Inicializar Lucide Icons
        lucide.createIcons();

        // Auto-submit para filtros de búsqueda con preservación de foco y debounce seguro
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Re-enfocar el buscador solo si viene de un envío de búsqueda por parámetro en URL
            if (urlParams.has('search') || urlParams.has('q')) {
                const activeSearchInput = document.querySelector('input[name="search"]:not([type="hidden"]), input[name="q"]:not([type="hidden"])');
                if (activeSearchInput && activeSearchInput.value) {
                    activeSearchInput.focus();
                    const val = activeSearchInput.value;
                    activeSearchInput.value = '';
                    activeSearchInput.value = val;
                }
            }

            document.querySelectorAll('form[method="GET"]').forEach(form => {
                let timeout = null;
                form.querySelectorAll('input[name="search"], input[name="q"], input.auto-submit').forEach(input => {
                    input.addEventListener('input', function() {
                        clearTimeout(timeout);
                        // Debounce amplio (1400ms) para permitir escribir frases/nombres completos sin interrupciones
                        timeout = setTimeout(() => {
                            const currentSearchParam = urlParams.get(input.name) || '';
                            if (input.value.trim() !== currentSearchParam.trim()) {
                                form.submit();
                            }
                        }, 1400);
                    });

                    // Si presiona Enter, enviar inmediatamente cancelando el timer
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            clearTimeout(timeout);
                            form.submit();
                        }
                    });
                });
                
                form.querySelectorAll('select').forEach(select => {
                    // Solo adjuntar si no tiene un onchange ya definido en el HTML
                    if(!select.getAttribute('onchange')) {
                        select.addEventListener('change', function() {
                            form.submit();
                        });
                    }
                });
            });
        });
    </script>
    
    <!-- SweetAlert2 Global Notifications -->
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1e293b',
                iconColor: 'auto',
                customClass: {
                    popup: 'shadow-lg border-0 rounded-4'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
        });
    </script>

    @if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Toast.fire({
                icon: 'success',
                title: @json(session('success'))
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Toast.fire({
                icon: 'error',
                title: @json(session('error'))
            });
        });
    </script>
    @endif

    @if(session('warning'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Toast.fire({
                icon: 'warning',
                title: @json(session('warning'))
            });
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'error',
                title: 'ERROR DE FORMULARIO',
                html: `<div class="text-center mt-2" style="font-size: 0.95rem;">
                    <p class="mb-3"><strong>¡Ups! Te falta lo siguiente:</strong></p>
                    <div class="d-flex flex-column gap-2 text-danger fw-semibold">
                        @foreach($errors->all() as $error)
                            <div>⚠️ {{ $error }}</div>
                        @endforeach
                    </div>
                </div>`,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido',
                customClass: {
                    popup: 'rounded-4 border-0 shadow-lg'
                }
            });
        });
    </script>
    @endif

    <script>

        // Delete Confirmation SweetAlert
        function confirmDelete(event, text) {
            event.preventDefault();
            const form = event.target.closest('form') || event.target;
            Swal.fire({
                title: '¿Estás seguro?',
                text: text || 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 border-0 shadow-lg',
                    confirmButton: 'btn btn-danger px-4 py-2 rounded-3 ms-2',
                    cancelButton: 'btn btn-secondary px-4 py-2 rounded-3'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        // Prevent double form submission globally
        document.addEventListener('submit', function(e) {
            const form = e.target;
            
            // Skip for forms with method GET (like search filters) as they might be triggered multiple times via auto-submit
            if (form.method && form.method.toUpperCase() === 'GET') {
                return;
            }

            if (form.classList.contains('is-submitting')) {
                e.preventDefault();
                return;
            }
            
            form.classList.add('is-submitting');
            
            const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('input[type="submit"]');
            if (submitBtn) {
                if (!submitBtn.dataset.originalHtml) {
                    submitBtn.dataset.originalHtml = submitBtn.innerHTML || submitBtn.value;
                }
                
                // Add a small delay so the form can still submit its value if the button has a name attribute
                setTimeout(() => {
                    submitBtn.disabled = true;
                    if (submitBtn.tagName === 'BUTTON') {
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Guardando...';
                    } else {
                        submitBtn.value = 'Guardando...';
                    }
                }, 10);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
