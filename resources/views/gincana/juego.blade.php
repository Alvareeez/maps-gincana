@extends('layout.app')

@section('title', 'Gincana en curso')

@section('content')
<div class="container-fluid p-0">
    <div id="estado-juego">
        <!-- Aquí se cargará dinámicamente el contenido del juego -->
        <div class="text-center py-5">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3">Cargando estado del juego...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/gincana/juego.js') }}" data-gincana-id="{{ $id }}"></script>
@endpush
