@extends('layouts.dashboard')

@section('title', 'Gestión de Aulas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Aulas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.aulas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Aula
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Aulas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Edificio</th>
                            <th>Piso</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aulas as $aula)
                        <tr>
                            <td><span class="badge bg-info">{{ $aula->codigo_aula }}</span></td>
                            <td>{{ $aula->nombre }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $aula->tipo_aula)) }}
                                </span>
                            </td>
                            <td>{{ $aula->edificio }}</td>
                            <td>{{ $aula->piso }}°</td>
                            <td>{{ $aula->capacidad }} personas</td>
                            <td>
                                @php
                                    $estadoClass = match($aula->estado) {
                                        'disponible' => 'success',
                                        'ocupada' => 'warning',
                                        'mantenimiento' => 'info',
                                        'fuera_servicio' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $estadoClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $aula->estado)) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.aulas.edit', $aula) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.aulas.destroy', $aula) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar esta aula?')">
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
                        <td colspan="8" class="text-center">No hay aulas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection