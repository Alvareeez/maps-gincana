<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/loginStyle.css') }}">
</head>

<body>

    <div class="login-container">
        <!-- Logo a la izquierda -->
        <div class="logo">
            <img src="{{ asset('img/fondo_GA.png') }}" alt="Logo">
        </div>

        <!-- Formulario de Login -->
        <div class="login-form">
            <h1>Iniciar Sesión</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Email input -->
                <input type="text" name="email" placeholder="Correo Electrónico" required>

                <!-- Password input -->
                <input type="password" name="password" placeholder="Contraseña" required>

                <!-- Submit button -->
                <button type="submit">Entrar</button>
            </form>
        </div>
    </div>

</body>

</html>
