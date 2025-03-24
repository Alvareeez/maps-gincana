<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Mi App</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Archivos de estilos y scripts -->
    <link rel="stylesheet" href="{{ asset('css/homeStyle.css') }}">
    <script defer src="{{ asset('js/homeScripts.js') }}"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="animated-bg">

    <!-- Botón de Cerrar Sesión (Posicionado en la esquina superior derecha con animación) -->
    @auth
        <form action="{{ route('logout') }}" method="POST" class="absolute top-0 right-0 m-4">
            @csrf
            <button type="submit" class="btn btn-logout slide-in">CERRAR SESIÓN</button>
        </form>
    @endauth

    <!-- Logo -->
    <img src="{{ asset('img/fondo_GA.png') }}" alt="Logo de la app" class="logo">

    <div class="text-center">
        @guest
            <!-- Si no está logueado -->
            <h1 class="title">¡Bienvenido a GA MAPS!</h1>
            <p class="subtitle">Descubre nuevas funciones y experiencias increíbles.</p>
        @else
            <!-- Si está logueado -->
            <h1 class="title">Hola, {{ auth()->user()->nombre }}!</h1>
            <p class="subtitle">Hoy, ¿qué función quieres utilizar?</p>
        @endguest

        <div class="buttons flex flex-wrap gap-4 justify-center mt-4">
            @guest
                <!-- Si no está logueado -->
                <a href="{{ route('register') }}" class="btn btn-register">Registro</a>
                <a href="{{ route('login') }}" class="btn btn-login">Login</a>
            @else
                <!-- Si está logueado -->
                <a href="#" class="btn btn-map">MAPA</a>
                <a href="#" class="btn btn-gincana">GINCANA</a>

                @if(auth()->user()->id_rol == 1)
                    <a href="{{ route('admin.index') }}" class="btn btn-admin">ADMIN</a>
                @endif
            @endguest
        </div>
    </div>

</body>

</html>
