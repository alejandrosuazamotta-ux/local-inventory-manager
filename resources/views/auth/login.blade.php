<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Inventario') }} - Iniciar Sesión</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2563eb">

    <!-- Fuentes (autohospedadas para funcionar sin internet) -->
    <link href="{{ asset('vendor/fonts/login.css') }}" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="{{ asset('vendor/lucide/lucide.min.js') }}"></script>

    <!-- Vite Styles -->
    @vite(['resources/css/auth.css', 'resources/js/app.js'])
</head>
<body>
    <div class="login-wrapper">
        <!-- Left Side: Brand/Image -->
        <div class="login-side-image">
            <div class="login-side-content">
                <div style="background: white; padding: 1.5rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: inline-block; margin-bottom: 2rem;">
                    <img src="{{ asset('img/logo.png') }}?v={{ time() }}" alt="Limpiaplus 360 Logo" style="width: 150px; height: auto;">
                </div>
                <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; letter-spacing: -1px;">{{ config('app.name') }}</h1>
                <p style="font-size: 1.1rem; opacity: 0.9; max-width: 400px; margin: 0 auto; line-height: 1.6;">
                    Gestiona tus productos, clientes y operaciones de manera eficiente y moderna.
                </p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="login-form-container">
            <div class="login-form-wrapper">
                <div style="margin-bottom: 2rem;">
                    <h2 class="login-title">Bienvenido de vuelta</h2>
                    <p class="login-subtitle">Ingresa tus credenciales para acceder al panel.</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-icon-wrapper">
                            <i data-lucide="mail"></i>
                            <input id="email" type="email" class="form-control-custom @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="admin@admin.com">
                        </div>
                        @error('email')
                            <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.5rem; display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-icon-wrapper">
                            <i data-lucide="lock"></i>
                            <input id="password" type="password" class="form-control-custom @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                        </div>
                        @error('password')
                            <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.5rem; display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Options Row -->
                    <div class="options-row">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="accent-color: #0f172a; width: 16px; height: 16px; margin: 0; padding: 0;">
                            <span style="line-height: 1;">Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="forgot-password" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="login-btn">
                        <i data-lucide="log-in" style="width: 20px; height: 20px;"></i>
                        Iniciar Sesión
                    </button>
                </form>

                <div style="margin-top: 3rem; text-align: center; color: #6b7280; font-size: 0.85rem; font-weight: 500;">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Inventario') }}. Todos los derechos reservados.
                </div>
            </div>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
