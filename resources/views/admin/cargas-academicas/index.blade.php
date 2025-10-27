@extends('layouts.dashboard')

@section('title', 'Gestión de Cargas Académicas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Cargas Académicas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.cargas-academicas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Carga Académica
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Cargas Académicas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Profesor</th>
                            <th>Materia</th>
                            <th>Grupo</th>
                            <th>Período</th>
                            <th>Estado</th>
                            <th>Horarios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cargasAcademicas as $carga)
                        <tr>
                            <td>{{ $carga->profesor->nombre_completo ?? 'N/A' }}</td>
                            <td>{{ $carga->grupo->materia->nombre ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $carga->grupo->identificador ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $carga->periodo }}</span>
                            </td>
                            <td>
                                @php
                                    $estadoColors = [
                                        'asignado' => 'success',
                                        'pendiente' => 'warning',
                                        'completado' => 'info',
                                        'cancelado' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $estadoColors[$carga->estado] ?? 'secondary' }}">
                                    {{ ucfirst($carga->estado) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $carga->horarios->count() }} horarios</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.cargas-academicas.show', $carga) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.cargas-academicas.edit', $carga) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.cargas-academicas.destroy', $carga) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar esta carga académica?')">
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
                            <td colspan="7" class="text-center">No hay cargas académicas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection