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
<body class="animated-bg flex items-center justify-center min-h-screen text-white">

    <div class="text-center" x-data="{ show: false }" x-init="setTimeout(() => show = true, 500)">
        <h1 class="title">¡Bienvenido a Mi Aplicación!</h1>
        <p class="subtitle">Descubre nuevas funciones y experiencias increíbles.</p>
        
        <div class="buttons">
            <a href="{{ route('register') }}" class="btn btn-register">Registro</a>
            <a href="{{ route('login') }}" class="btn btn-login">Login</a>
        </div>
    </div>

</body>
</html>
