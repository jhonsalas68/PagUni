@extends('layouts.dashboard')

@section('title', 'Gestión de Grupos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Grupos</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.grupos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Grupo
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Grupos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Identificador</th>
                            <th>Materia</th>
                            <th>Carrera</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grupos as $grupo)
                        <tr>
                            <td><span class="badge bg-primary">{{ $grupo->identificador }}</span></td>
                            <td>{{ $grupo->materia->nombre ?? 'N/A' }}</td>
                            <td>{{ $grupo->materia->carrera->nombre ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $grupo->capacidad_maxima }} estudiantes</span></td>
                            <td>
                                <span class="badge bg-{{ $grupo->estado == 'activo' ? 'success' : 'danger' }}">
                                    {{ ucfirst($grupo->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.grupos.show', $grupo) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.grupos.edit', $grupo) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.grupos.destroy', $grupo) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar este grupo?')">
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
                            <td colspan="6" class="text-center">No hay grupos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection