@extends('layouts.dashboard')

@section('title', 'Detalles del Feriado')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h3 class="card-title mb-2 mb-md-0">
                        <i class="fas fa-calendar-check me-2"></i>
                        <span class="d-none d-sm-inline">Detalles del Día No Laborable/Feriado</span>
                        <span class="d-sm-none">Detalles del Feriado</span>
                    </h3>
                    <div class="btn-group-responsive">
                        <a href="{{ route('admin.feriados.edit', $feriado) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>
                            <span class="d-none d-sm-inline">Editar</span>
                        </a>
                        <a href="{{ route('admin.feriados.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>
                            <span class="d-none d-sm-inline">Volver a la Lista</span>
                            <span class="d-sm-none">Volver</span>
                        </a>
                    </div>
                </div>
            </div>

                <div class="card-body">
                    <div class="row g-3">
                        <!-- Información principal -->
                        <div class="col-12 col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Información General</h4>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-12 col-sm-4">Descripción:</dt>
                                        <dd class="col-12 col-sm-8">{{ $feriado->descripcion }}</dd>

                                        <dt class="col-12 col-sm-4">Tipo:</dt>
                                        <dd class="col-12 col-sm-8">
                                            <span class="badge bg-{{ $feriado->tipo == 'feriado' ? 'primary' : ($feriado->tipo == 'receso' ? 'success' : 'warning') }} badge-lg">
                                                {{ $feriado->tipo_formateado }}
                                            </span>
                                        </dd>

                                        <dt class="col-12 col-sm-4">Fechas:</dt>
                                        <dd class="col-12 col-sm-8">
                                            <strong>{{ $feriado->rango_fechas }}</strong>
                                            @if($feriado->fecha_fin)
                                                <br><small class="text-muted">Rango de fechas ({{ $feriado->fecha_inicio->diffInDays($feriado->fecha_fin) + 1 }} días)</small>
                                            @else
                                                <br><small class="text-muted">Día específico</small>
                                            @endif
                                        </dd>

                                        <dt class="col-12 col-sm-4">Estado:</dt>
                                        <dd class="col-12 col-sm-8">
                                            @if($feriado->activo)
                                                <span class="badge bg-success badge-lg">Activo</span>
                                            @else
                                                <span class="badge bg-secondary badge-lg">Inactivo</span>
                                            @endif
                                        </dd>

                                        <dt class="col-12 col-sm-4">Creado:</dt>
                                        <dd class="col-12 col-sm-8">{{ $feriado->created_at->format('d/m/Y H:i:s') }}</dd>

                                        <dt class="col-12 col-sm-4">Última modificación:</dt>
                                        <dd class="col-12 col-sm-8">{{ $feriado->updated_at->format('d/m/Y H:i:s') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Estadísticas y acciones -->
                        <div class="col-12 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Acciones</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.feriados.edit', $feriado) }}" class="btn btn-warning w-100">
                                            <i class="fas fa-edit me-1"></i>
                                            Editar Feriado
                                        </a>
                                        
                                        @if($feriado->activo)
                                        <form method="POST" action="{{ route('admin.feriados.destroy', $feriado) }}" 
                                              onsubmit="return confirm('¿Está seguro de desactivar este feriado?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-trash me-1"></i>
                                                Desactivar Feriado
                                            </button>
                                        </form>
                                        @endif
                                        
                                        <a href="{{ route('admin.feriados.create') }}" class="btn btn-success w-100">
                                            <i class="fas fa-plus me-1"></i>
                                            Crear Nuevo Feriado
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h4 class="card-title">Información Adicional</h4>
                                </div>
                                <div class="card-body">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-calendar-day"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Días Afectados</span>
                                            <span class="info-box-number">{{ count($diasAfectados) }}</span>
                                        </div>
                                    </div>

                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-business-time"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Días Laborables</span>
                                            <span class="info-box-number">
                                                {{ collect($diasAfectados)->where('es_laborable', true)->count() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Días afectados -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        Días Afectados por este Feriado
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Día de la Semana</th>
                                                    <th>Tipo de Día</th>
                                                    <th>Impacto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($diasAfectados as $dia)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $dia['fecha']->format('d/m/Y') }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ ucfirst($dia['dia_semana']) }}
                                                    </td>
                                                    <td>
                                                        @if($dia['es_laborable'])
                                                            <span class="badge badge-warning">Día Laborable</span>
                                                        @else
                                                            <span class="badge badge-secondary">Fin de Semana</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($dia['es_laborable'])
                                                            <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                                                            Afecta programación de clases
                                                        @else
                                                            <i class="fas fa-check-circle text-success mr-1"></i>
                                                            Sin impacto en horarios
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @if(collect($diasAfectados)->where('es_laborable', true)->count() > 0)
                                    <div class="alert alert-warning mt-3">
                                        <h5><i class="icon fas fa-exclamation-triangle"></i> Impacto en el Sistema:</h5>
                                        <ul class="mb-0">
                                            <li>No se programarán clases automáticamente en los días laborables marcados</li>
                                            <li>No se esperará registro de asistencia en estas fechas</li>
                                            <li>Los cálculos de asistencia excluirán estos días</li>
                                            <li>Los reportes académicos considerarán estos días como no lectivos</li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.badge-lg {
    font-size: 0.9em;
    padding: 0.5em 0.75em;
}

/* Responsive improvements */
@media (max-width: 767px) {
    .card-title {
        font-size: 1.1rem;
    }
    
    .btn {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    
    .table {
        font-size: 0.875rem;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .info-box {
        padding: 0.75rem;
    }
    
    .info-box-icon {
        width: 50px;
        height: 50px;
        margin-right: 0.75rem;
    }
    
    .info-box-number {
        font-size: 1.1rem;
    }
}

@media (max-width: 575px) {
    .card-header .d-flex {
        gap: 0.5rem;
    }
    
    .btn-group-responsive {
        width: 100%;
    }
    
    .btn-group-responsive .btn {
        flex: 1;
    }
}
</style>
@endsection