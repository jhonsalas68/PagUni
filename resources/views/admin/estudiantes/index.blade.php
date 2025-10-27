@extends('layouts.dashboard')

@section('title', 'Gestión de Estudiantes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Estudiantes</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.estudiantes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Estudiante
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Estudiantes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Carrera</th>
                            <th>Semestre</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estudiantes as $estudiante)
                        <tr>
                            <td><span class="badge bg-primary">{{ $estudiante->codigo_estudiante }}</span></td>
                            <td>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</td>
                            <td>{{ $estudiante->email }}</td>
                            <td>{{ $estudiante->carrera->nombre ?? 'Sin carrera' }}</td>
                            <td>{{ $estudiante->semestre_actual ?? 'N/A' }}°</td>
                            <td>
                                @php
                                    $estadoColors = [
                                        'activo' => 'success',
                                        'inactivo' => 'danger',
                                        'graduado' => 'info',
                                        'retirado' => 'warning'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $estadoColors[$estudiante->estado] ?? 'secondary' }}">
                                    {{ ucfirst($estudiante->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.estudiantes.show', $estudiante) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.estudiantes.edit', $estudiante) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.estudiantes.destroy', $estudiante) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar este estudiante?')">
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
                            <td colspan="7" class="text-center">No hay estudiantes registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection