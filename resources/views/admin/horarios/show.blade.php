@extends('layouts.dashboard')

@section('title', 'Detalle de Horario')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalle de Horario</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.horarios.edit', $horario) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Horario</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Día de la Semana:</label>
                                <p class="form-control-plaintext">
                                    @php
                                        $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                    @endphp
                                    <span class="badge bg-primary fs-6">{{ $dias[$horario->dia_semana] ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Horario:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info fs-6">{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</span>
                                    @if($horario->duracion_horas)
                                        <span class="badge bg-success fs-6 ms-1">{{ number_format($horario->duracion_horas, 2) }} hrs</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($horario->usar_configuracion_por_dia && $horario->configuracion_dias)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Configuración Específica por Día:</label>
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0"><i class="fas fa-cogs"></i> Configuración Avanzada</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Día</th>
                                                        <th>Aula</th>
                                                        <th>Tipo de Clase</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($horario->getConfiguracionCompleta() as $config)
                                                    <tr>
                                                        <td><span class="badge bg-primary">{{ $config['dia_nombre'] }}</span></td>
                                                        <td><span class="badge bg-info">{{ $config['aula_nombre'] }}</span></td>
                                                        <td><span class="badge bg-success">{{ $config['tipo_clase_nombre'] }}</span></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo de Clase:</label>
                                <p class="form-control-plaintext">
                                    @php
                                        $tipos = [
                                            'teorica' => 'bg-info',
                                            'practica' => 'bg-warning',
                                            'laboratorio' => 'bg-success'
                                        ];
                                    @endphp
                                    <span class="badge {{ $tipos[$horario->tipo_clase] ?? 'bg-secondary' }} fs-6">
                                        {{ ucfirst($horario->tipo_clase) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Aula:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-success fs-6">{{ $horario->aula->codigo_aula ?? 'N/A' }}</span>
                                    - {{ $horario->aula->nombre ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
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
                                <p class="form-control-plaintext">{{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Código de Materia:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-secondary">{{ $horario->cargaAcademica->grupo->materia->codigo ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Profesor:</label>
                                <p class="form-control-plaintext">{{ $horario->cargaAcademica->profesor->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Grupo:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-warning text-dark">{{ $horario->cargaAcademica->grupo->nombre ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Detalles del Aula</h6>
                </div>
                <div class="card-body">
                    @if($horario->aula)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Capacidad:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-info fs-6">{{ $horario->aula->capacidad }} personas</span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Aula:</label>
                        <p class="form-control-plaintext">{{ $horario->aula->tipo_aula_legible }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ubicación:</label>
                        <p class="form-control-plaintext">{{ $horario->aula->ubicacion_completa }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-success">{{ $horario->aula->estado_legible }}</span>
                        </p>
                    </div>
                    @else
                    <p class="text-muted">No hay información del aula disponible.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Sistema</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Registro:</label>
                        <p class="form-control-plaintext">{{ $horario->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Última Actualización:</label>
                        <p class="form-control-plaintext">{{ $horario->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-danger">Zona de Peligro</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                    <form method="POST" action="{{ route('admin.horarios.destroy', $horario) }}" 
                          onsubmit="return confirm('¿Está seguro de eliminar este horario? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash"></i> Eliminar Horario
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection