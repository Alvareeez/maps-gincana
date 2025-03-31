@extends('layout.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Gincana en curso</h3>
                    
                </div>
                <div class="card-body" id="estado-juego">
                    <!-- Aquí se cargará dinámicamente el contenido -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p>Cargando estado del juego...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/gincana/juego.js') }}" data-gincana-id="{{ $id }}"></script>
@endpush
@endsection