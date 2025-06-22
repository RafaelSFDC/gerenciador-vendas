<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - DC Tecnologia</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm mt-5">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-store me-2"></i>
                            DC Tecnologia
                        </h4>
                        <small>Sistema de Vendas</small>
                    </div>
                    <div class="card-body p-4">
                        <!-- Status Messages -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>
                                    E-mail
                                </label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       autocomplete="username">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>
                                    Senha
                                </label>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       required
                                       autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="remember"
                                       name="remember">
                                <label class="form-check-label" for="remember">
                                    Lembrar-me
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    Entrar
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-decoration-none">
                                    <small>Esqueceu sua senha?</small>
                                </a>
                            @endif
                        </div>

                        @if (Route::has('register'))
                            <hr>
                            <div class="text-center">
                                <p class="mb-0">
                                    <small>Não tem uma conta?</small>
                                </p>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-user-plus me-1"></i>
                                    Criar conta
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Credenciais de teste -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle me-1"></i>
                            Credenciais de Teste
                        </h6>
                        <p class="card-text">
                            <strong>E-mail:</strong> vendedor@dctecnologia.com<br>
                            <strong>Senha:</strong> 123456
                        </p>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="preencherCredenciais()">
                            <i class="fas fa-magic me-1"></i>
                            Preencher automaticamente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function preencherCredenciais() {
            document.getElementById('email').value = 'vendedor@dctecnologia.com';
            document.getElementById('password').value = '123456';
        }
    </script>
</body>
</html>
