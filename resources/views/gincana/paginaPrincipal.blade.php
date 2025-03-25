@extends('layout.layoutGincana')

@section('title', 'Unirse a gincana')

{{-- @section('title', 'Issue ' . $data['issue']->id) --}}

@section('headerTitle', 'Escoge una gincana')

@section('content')
    <div class="container text-center mt-5">
        <div id="contenedorGincanas" class="row justify-content-center">
            <!-- Aquí se cargarán las gincanas dinámicamente -->
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/gincana/paginaPrincipal.js') }}"></script>
@endpush