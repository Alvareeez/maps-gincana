@extends('layout.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Gincana en curso</h3>
                    <form action="{{ route('gincana.salir') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Salir del grupo</button>
                    </form>
                </div>
                <div class="card-body">
                    <h4>Has entrado en la gincana #{{ $id }}</h4>
                    <p>Aquí irá el contenido del juego...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 