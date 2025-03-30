<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/loginStyle.css') }}">
    <style>
        /* Estilos para validaciones */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        .error-message {
            color: #ff4444;
            font-size: 0.8rem;
            margin-top: 0.2rem;
            display: none;
        }
        .error-border {
            border-color: #ff4444 !important;
        }
        .success-border {
            border-color: #00C851 !important;
        }
        #submitBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <a href="/" class="logo-link">
                <img src="{{ asset('img/fondo_GA.png') }}" alt="Logo">
            </a>
        </div>

        <div class="login-form">
            <h1>Iniciar Sesión</h1>
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <div class="form-group">
                    <input type="text" name="email" id="email" placeholder="Correo Electrónico">
                    <span class="error-message" id="email-error">Debe ser un email válido (contener @ y dominio)</span>
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" id="password" placeholder="Contraseña">
                    <span class="error-message" id="password-error">La contraseña debe tener al menos 6 caracteres</span>
                </div>
                
                <button type="submit" id="submitBtn" disabled>Entrar</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/loginValidations.js') }}"></script>
</body>
</html>