@extends('layouts.dashboard')

@section('title', 'Detalle de Grupo')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalle de Grupo</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.grupos.edit', $grupo) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.grupos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Grupo</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Identificador:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-primary fs-6">Grupo {{ $grupo->identificador }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $grupo->estado == 'activo' ? 'success' : 'danger' }} fs-6">
                                        {{ ucfirst($grupo->estado) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Capacidad Máxima:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info fs-6">{{ $grupo->capacidad_maxima }} estudiantes</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre Completo:</label>
                                <p class="form-control-plaintext">{{ $grupo->nombre_completo }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Información de la Materia</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Materia:</label>
                                <p class="form-control-plaintext">{{ $grupo->materia->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Código:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-secondary">{{ $grupo->materia->codigo ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Carrera:</label>
                                <p class="form-control-plaintext">{{ $grupo->materia->carrera->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Facultad:</label>
                                <p class="form-control-plaintext">{{ $grupo->materia->carrera->facultad->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Semestre:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info">{{ $grupo->materia->semestre ?? 'N/A' }}° Semestre</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Créditos:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-success">{{ $grupo->materia->creditos ?? 'N/A' }} créditos</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($grupo->cargaAcademica->count() > 0)
            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Cargas Académicas Asignadas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Profesor</th>
                                    <th>Período</th>
                                    <th>Estado</th>
                                    <th>Horarios</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grupo->cargaAcademica as $carga)
                                <tr>
                                    <td>{{ $carga->profesor->nombre_completo ?? 'N/A' }}</td>
                                    <td><span class="badge bg-primary">{{ $carga->periodo }}</span></td>
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
                                    <td><span class="badge bg-info">{{ $carga->horarios->count() }} horarios</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Cargas Académicas:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-info fs-6">{{ $grupo->cargaAcademica->count() }} asignaciones</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Creación:</label>
                        <p class="form-control-plaintext">{{ $grupo->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Última Actualización:</label>
                        <p class="form-control-plaintext">{{ $grupo->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-danger">Zona de Peligro</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Esta acción eliminará el grupo y todas sus cargas académicas asociadas.</p>
                    <form method="POST" action="{{ route('admin.grupos.destroy', $grupo) }}" 
                          onsubmit="return confirm('¿Está seguro de eliminar este grupo? Se eliminarán también todas las cargas académicas asociadas.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash"></i> Eliminar Grupo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection