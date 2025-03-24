@extends('layout.layoutGincana')

@section('title', 'Unirse a gincana')

{{-- @section('title', 'Issue ' . $data['issue']->id) --}}

@section('headerTitle', 'Unirse a gincana')


@section('content')
    <h1>Gincanas Disponibles</h1>
    <div id="contenedorGincanas">

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/gincana/paginaPrincipal.js') }}"></script>
@endpush