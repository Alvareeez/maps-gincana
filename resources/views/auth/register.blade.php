<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/registroStyle.css') }}">
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
    <div class="container">
        <div class="register-container">
            <div class="logo">
                <a href="/" class="logo-link">
                    <img src="{{ asset('img/fondo_GA.png') }}" alt="Logo">
                </a>
            </div>

            <div class="register-form">
                <h1>Registrarse</h1>
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="nombre" id="nombre" placeholder="Nombre Completo">
                        <span class="error-message" id="nombre-error">El nombre es requerido (mín. 3 caracteres)</span>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" name="apellido" id="apellido" placeholder="Apellido">
                        <span class="error-message" id="apellido-error">El apellido es requerido (mín. 3 caracteres)</span>
                    </div>
                    
                    <div class="form-group">
                        <input type="email" name="email" id="email" placeholder="Correo Electrónico">
                        <span class="error-message" id="email-error">Debe ser un email válido</span>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" name="username" id="username" placeholder="Nombre de Usuario">
                        <span class="error-message" id="username-error">Usuario requerido (mín. 4 caracteres)</span>
                    </div>
                    
                    <div class="form-group">
                        <input type="password" name="password" id="password" placeholder="Contraseña">
                        <span class="error-message" id="password-error">La contraseña debe tener al menos 6 caracteres</span>
                    </div>
                    
                    <div class="form-group">
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirmar Contraseña">
                        <span class="error-message" id="password-confirm-error">Las contraseñas no coinciden</span>
                    </div>
                    
                    <button type="submit" id="submitBtn" disabled>Registrarse</button>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/registroValidations.js') }}"></script>
</body>
</html>