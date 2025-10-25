@extends('layouts.dashboard')

@section('title', 'Dashboard Estudiante')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Estudiante</h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bienvenido Estudiante</h6>
                </div>
                <div class="card-body">
                    <h4>¡Hola {{ session('user_name') }}!</h4>
                    <p>Código: <strong>{{ session('user_codigo') }}</strong></p>
                    <p>Bienvenido a tu panel de estudiante. Desde aquí puedes ver tus notas y materias.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection