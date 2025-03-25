@extends('layout.layoutGincana')

@section('title', 'Unirse a grupo')

@section('headerTitle', 'Unirse a grupo')

@section('volverAtras')
<a href="{{ route('gincana.menu') }}" class="back-button">Volver atrás</a>
@endsection

@section('content')
    <div class="container text-center mt-5">
        <div id="contenedorGrupos" class="row justify-content-center">
            <!-- Aquí se cargarán los grupos dinámicamente -->
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/gincana/menuLobby.js') }}"></script>
@endpush