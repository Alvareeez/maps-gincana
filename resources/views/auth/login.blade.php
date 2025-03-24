<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="{{ asset('css/loginStyle.css') }}">
</head>

<body>

    <div class="container">
        <div class="card">
            <!-- Logo a la izquierda -->
            <div class="row">
                <div class="col-md-6 logo">
                    <img src="{{ asset('img/fondo_GA.png') }}" alt="Logo">
                </div>
                <!-- Formulario de Login -->
                <div class="col-md-6 login-form">
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
        </div>
    </div>

</body>

</html>
