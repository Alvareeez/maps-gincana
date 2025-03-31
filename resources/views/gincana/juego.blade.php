@extends('layout.app')

@section('headerTitle')
    <img src="{{ asset('img/fondo_GA.png') }}" alt="Logo Gincana" style="height: 70px;" class="mb-3">
    <h1 class="text-warning">Gincana en curso</h1>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card bg-dark text-white border-warning shadow-lg">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Estado del juego</h3>
                </div>
                <div class="card-body" id="estado-juego">
                    <div class="text-center py-5">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-3">Cargando estado del juego...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/gincana/juego.js') }}" data-gincana-id="{{ $id }}"></script>
@endpush
