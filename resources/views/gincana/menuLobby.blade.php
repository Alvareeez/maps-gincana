@extends('layout.layoutGincana')

@section('title', 'Unirse a grupo')

{{-- @section('title', 'Issue ' . $data['issue']->id) --}}

@section('headerTitle', 'Unirse a grupo')


@section('content')
    <a href="{{ route('gincana.menu') }}">Volver atrás</a>
    <h3 id="nombreGrupo"></h3>
    <h1>Grupos Disponibles</h1>
    <div id="contenedorGrupos">

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/gincana/menuLobby.js') }}"></script>
@endpush