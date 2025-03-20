<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <!-- Usamos una fuente estilizada (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/registroStyle.css') }}">
</head>

<body>

    <div class="container">
        <div class="register-container">
            <div class="logo">
                <img src="img/fondo_GA.png" alt="Logo">
            </div>

            <div class="register-form">
                <h1>Registrarse</h1>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Nombre input -->
                    <input type="text" name="nombre" placeholder="Nombre Completo" required>

                    <!-- Apellido input -->
                    <input type="text" name="apellido" placeholder="Apellido" required> <!-- Nuevo campo para apellido -->

                    <!-- Email input -->
                    <input type="email" name="email" placeholder="Correo Electrónico" required>

                    <!-- Username input -->
                    <input type="text" name="username" placeholder="Nombre de Usuario" required>

                    <!-- Password input -->
                    <input type="password" name="password" placeholder="Contraseña" required>

                    <!-- Confirm Password input -->
                    <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>

                    <!-- Submit button -->
                    <button type="submit">Registrarse</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
