<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="{{asset('css/estilosGincana.css')}}">

    <title>@yield('title')</title>
</head>
<body>
    <header>
        <nav>
        <!-- Botón "Volver atrás" -->
        <div class="back-button-container">
            <a href="{{ route('home') }}" class="back-button">Volver atrás</a>
        </div>
        <!-- Título principal -->
        <div class="header-title">
            @yield('headerTitle') <!-- Aquí va tu título dinámico -->
        </div>
        </nav>
    </header>

    @yield('content')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>