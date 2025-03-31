@extends('layout.layoutGincana')

@section('title', 'Unirse a gincana')

{{-- @section('title', 'Issue ' . $data['issue']->id) --}}

@section('headerTitle', 'Escoge una gincana')
@section('volverAtras')
<a href="{{ route('home') }}" class="back-button">Volver atrás</a>
@endsection

@section('content')
    <div class="container text-center mt-5">
        <div id="contenedorGincanas" 
            class="row justify-content-center"
            data-gincanas-url="{{ route('gincana.gincanas-abiertas') }}">
            <!-- Aquí se cargarán las gincanas dinámicamente -->
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/gincana/paginaPrincipal.js') }}"></script>
@endpush