@extends('layouts.dashboard')

@section('title', 'Dashboard Profesor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Profesor</h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bienvenido Profesor</h6>
                </div>
                <div class="card-body">
                    <h4>¡Hola {{ session('user_name') }}!</h4>
                    <p>Código: <strong>{{ session('user_codigo') }}</strong></p>
                    <p>Bienvenido a tu panel de profesor. Desde aquí puedes gestionar tus clases y calificar a tus estudiantes.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection