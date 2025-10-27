@extends('layouts.dashboard')

@section('title', 'Gestión de Materias')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Materias</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.materias.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Materia
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Materias</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Carrera</th>
                            <th>Facultad</th>
                            <th>Semestre</th>
                            <th>Créditos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materias as $materia)
                        <tr>
                            <td><span class="badge bg-primary">{{ $materia->codigo }}</span></td>
                            <td>{{ $materia->nombre }}</td>
                            <td>{{ $materia->carrera->nombre ?? 'N/A' }}</td>
                            <td>{{ $materia->carrera->facultad->nombre ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $materia->semestre }}°</span></td>
                            <td><span class="badge bg-success">{{ $materia->creditos }}</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.materias.show', $materia) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.materias.edit', $materia) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.materias.destroy', $materia) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar esta materia?')">
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
                            <td colspan="7" class="text-center">No hay materias registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection