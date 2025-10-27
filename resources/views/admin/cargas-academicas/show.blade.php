@extends('layouts.dashboard')

@section('title', 'Detalle de Carga Académica')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalle de Carga Académica</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.cargas-academicas.edit', $cargaAcademica) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.cargas-academicas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Información General</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Profesor:</label>
                                <p class="form-control-plaintext">{{ $cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Especialidad:</label>
                                <p class="form-control-plaintext">{{ $cargaAcademica->profesor->especialidad ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email del Profesor:</label>
                                <p class="form-control-plaintext">{{ $cargaAcademica->profesor->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Teléfono:</label>
                                <p class="form-control-plaintext">{{ $cargaAcademica->profesor->telefono ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Información Académica</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Materia:</label>
                                <p class="form-control-plaintext">{{ $cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Código de Materia:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-secondary">{{ $cargaAcademica->grupo->materia->codigo ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Grupo:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info fs-6">Grupo {{ $cargaAcademica->grupo->identificador ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Capacidad del Grupo:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-warning text-dark">{{ $cargaAcademica->grupo->capacidad_maxima ?? 'N/A' }} estudiantes</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Carrera:</label>
                                <p class="form-control-plaintext">{{ $cargaAcademica->grupo->materia->carrera->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Facultad:</label>
                                <p class="form-control-plaintext">{{ $cargaAcademica->grupo->materia->carrera->facultad->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Semestre:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info">{{ $cargaAcademica->grupo->materia->semestre ?? 'N/A' }}° Semestre</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Créditos:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-success">{{ $cargaAcademica->grupo->materia->creditos ?? 'N/A' }} créditos</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($cargaAcademica->horarios->count() > 0)
            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Horarios Asignados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th>Horario</th>
                                    <th>Duración</th>
                                    <th>Aula</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cargaAcademica->horarios as $horario)
                                <tr>
                                    <td>
                                        @php
                                            $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                        @endphp
                                        <span class="badge bg-primary">{{ $dias[$horario->dia_semana] ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $horario->duracion_horas ? number_format($horario->duracion_horas, 2) . ' hrs' : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $horario->aula->codigo_aula ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $tipos = [
                                                'teorica' => 'bg-info',
                                                'practica' => 'bg-warning',
                                                'laboratorio' => 'bg-success'
                                            ];
                                        @endphp
                                        <span class="badge {{ $tipos[$horario->tipo_clase] ?? 'bg-secondary' }}">
                                            {{ ucfirst($horario->tipo_clase) }}
                                        </span>
                                    </td>
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
                    <h6 class="m-0 font-weight-bold text-primary">Estado y Período</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Período:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-primary fs-6">{{ $cargaAcademica->periodo }}</span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado:</label>
                        <p class="form-control-plaintext">
                            @php
                                $estadoColors = [
                                    'asignado' => 'success',
                                    'pendiente' => 'warning',
                                    'completado' => 'info',
                                    'cancelado' => 'danger'
                                ];
                            @endphp
                            <span class="badge bg-{{ $estadoColors[$cargaAcademica->estado] ?? 'secondary' }} fs-6">
                                {{ ucfirst($cargaAcademica->estado) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Total de Horarios:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-info fs-6">{{ $cargaAcademica->horarios->count() }} horarios</span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Horas Semanales:</label>
                        <p class="form-control-plaintext">
                            @php
                                $totalHoras = $cargaAcademica->horarios->sum('duracion_horas');
                            @endphp
                            <span class="badge bg-success fs-6">{{ number_format($totalHoras, 2) }} horas</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Asignación:</label>
                        <p class="form-control-plaintext">{{ $cargaAcademica->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Última Actualización:</label>
                        <p class="form-control-plaintext">{{ $cargaAcademica->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-danger">Zona de Peligro</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Esta acción no se puede deshacer y eliminará todos los horarios asociados.</p>
                    <form method="POST" action="{{ route('admin.cargas-academicas.destroy', $cargaAcademica) }}" 
                          onsubmit="return confirm('¿Está seguro de eliminar esta carga académica? Se eliminarán también todos los horarios asociados.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash"></i> Eliminar Carga Académica
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection