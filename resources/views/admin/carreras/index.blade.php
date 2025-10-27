@extends('layouts.dashboard')

@section('title', 'Gestión de Carreras')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Carreras</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.carreras.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Carrera
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Carreras</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Facultad</th>
                            <th>Duración</th>
                            <th>Materias</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carreras as $carrera)
                        <tr>
                            <td><span class="badge bg-primary">{{ $carrera->codigo }}</span></td>
                            <td>{{ $carrera->nombre }}</td>
                            <td>{{ $carrera->facultad->nombre ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $carrera->duracion_semestres }} semestres</span></td>
                            <td><span class="badge bg-success">{{ $carrera->materias->count() }} materias</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.carreras.show', $carrera) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.carreras.edit', $carrera) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.carreras.destroy', $carrera) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar esta carrera?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay carreras registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection