@extends('layouts.dashboard')

@section('title', 'Gestión de Calificaciones')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-graduation-cap"></i> Gestión de Calificaciones
        </h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Mis Grupos Asignados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Materia</th>
                                    <th>Grupo</th>
                                    <th>Código</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($grupos as $grupo)
                                    <tr>
                                        <td>{{ $grupo->materia->nombre }}</td>
                                        <td>{{ $grupo->identificador }}</td>
                                        <td>{{ $grupo->materia->codigo }}</td>
                                        <td>
                                            <a href="{{ route('profesor.calificaciones.gestion', $grupo->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i> Ingresar Notas
                                            </a>
                                            <a href="{{ route('profesor.calificaciones.resumen', $grupo->id) }}" class="btn btn-info btn-sm text-white">
                                                <i class="fas fa-list-ol"></i> Ver Promedios
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No tienes grupos asignados actualmente.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
