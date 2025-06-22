<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'DC Tecnologia - Sistema de Vendas')</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @if(app()->environment('production'))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if($cssFile)
            <link rel="stylesheet" href="/build/{{ $cssFile }}">
        @endif
        @if($jsFile)
            <script type="module" src="/build/{{ $jsFile }}"></script>
        @endif
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <!-- Fallback Bootstrap CSS via CDN para produção se Vite falhar -->
    @if(app()->environment('production'))
        <noscript>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer">
        </noscript>
    @endif

    <!-- Verificação de carregamento de assets -->
    <script>
        // Verificar se Bootstrap CSS foi carregado
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const testElement = document.createElement('div');
                testElement.className = 'container d-none';
                testElement.style.position = 'absolute';
                testElement.style.top = '-9999px';
                document.body.appendChild(testElement);

                const computedStyle = window.getComputedStyle(testElement);
                const hasBootstrap = computedStyle.maxWidth !== 'none' || computedStyle.display === 'none';

                if (!hasBootstrap) {
                    console.warn('Bootstrap CSS não foi carregado corretamente, carregando fallback...');
                    // Carregar Bootstrap via CDN como fallback
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css';
                    link.integrity = 'sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH';
                    link.crossOrigin = 'anonymous';
                    document.head.appendChild(link);

                    // Carregar Font Awesome também
                    const faLink = document.createElement('link');
                    faLink.rel = 'stylesheet';
                    faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
                    faLink.integrity = 'sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==';
                    faLink.crossOrigin = 'anonymous';
                    faLink.referrerPolicy = 'no-referrer';
                    document.head.appendChild(faLink);
                } else {
                    console.log('Bootstrap CSS carregado com sucesso!');
                }

                document.body.removeChild(testElement);
            }, 100); // Aguardar um pouco para garantir que o CSS foi aplicado
        });
    </script>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-store me-2"></i>
                DC Tecnologia
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                               href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('vendas.*') ? 'active' : '' }}"
                               href="{{ route('vendas.index') }}">
                                <i class="fas fa-shopping-cart me-1"></i>
                                Vendas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('parcelas.*') ? 'active' : '' }}"
                               href="{{ route('parcelas.index') }}">
                                <i class="fas fa-credit-card me-1"></i>
                                Parcelas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
                               href="{{ route('clientes.index') }}">
                                <i class="fas fa-users me-1"></i>
                                Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('produtos.*') ? 'active' : '' }}"
                               href="{{ route('produtos.index') }}">
                                <i class="fas fa-box me-1"></i>
                                Produtos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('relatorios.*') ? 'active' : '' }}"
                               href="{{ route('relatorios.index') }}">
                                <i class="fas fa-chart-bar me-1"></i>
                                Relatórios
                            </a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-1"></i>
                                            Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Entrar
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Erro de validação:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0 text-muted">
                &copy; {{ date('Y') }} DC Tecnologia - Sistema de Vendas
            </p>
        </div>
    </footer>

    <!-- Verificação de carregamento do Bootstrap JS -->
    <script>
        // Verificar se Bootstrap JS foi carregado
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (typeof window.bootstrap === 'undefined') {
                    console.warn('Bootstrap JS não foi carregado corretamente, carregando fallback...');
                    // Carregar Bootstrap JS via CDN como fallback
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js';
                    script.integrity = 'sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz';
                    script.crossOrigin = 'anonymous';
                    document.head.appendChild(script);
                } else {
                    console.log('Bootstrap JS carregado com sucesso!');
                }
            }, 200); // Aguardar um pouco mais para o JS carregar
        });
    </script>

    @stack('scripts')
</body>
</html>
