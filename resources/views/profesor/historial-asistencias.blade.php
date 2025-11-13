@extends('layouts.dashboard')

@section('title', 'Historial de Asistencias')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-history"></i> Historial de Asistencias
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="{{ route('profesor.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter"></i> Filtros de Búsqueda
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('profesor.historial-asistencias') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                       value="{{ $fechaInicio }}">
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                       value="{{ $fechaFin }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <a href="{{ route('profesor.historial-asistencias') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Asistencias -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table"></i> Registro de Asistencias
                    </h6>
                </div>
                <div class="card-body">
                    @if($asistencias->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Materia</th>
                                        <th>Horario</th>
                                        <th>Aula</th>
                                        <th>Entrada</th>
                                        <th>Salida</th>
                                        <th>Duración</th>
                                        <th>Estado</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($asistencias as $asistencia)
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $asistencia->fecha->format('d/m/Y') }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $asistencia->horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $asistencia->horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                {{ $asistencia->horario->hora_inicio }} - {{ $asistencia->horario->hora_fin }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $asistencia->horario->aula->codigo_aula ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($asistencia->hora_entrada)
                                                    <span class="text-success">
                                                        <i class="fas fa-sign-in-alt"></i> {{ $asistencia->hora_entrada }}
                                                    </span>
                                                    @if(!$asistencia->validado_en_horario)
                                                        <br><small class="text-warning">
                                                            <i class="fas fa-exclamation-triangle"></i> Tardanza
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($asistencia->hora_salida)
                                                    <span class="text-info">
                                                        <i class="fas fa-sign-out-alt"></i> {{ $asistencia->hora_salida }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($asistencia->duracion_clase)
                                                    <span class="badge bg-primary">
                                                        {{ $asistencia->duracion_clase }} min
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $asistencia->estado_color }}">
                                                    {{ $asistencia->estado_texto }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($asistencia->observaciones)
                                                    <small>{{ $asistencia->observaciones }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        @if($asistencias->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <small class="text-muted">
                                Mostrando {{ $asistencias->firstItem() ?? 0 }}-{{ $asistencias->lastItem() ?? 0 }} de {{ $asistencias->total() }}
                            </small>
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    @if ($asistencias->onFirstPage())
                                        <li class="page-item disabled"><span class="page-link">‹ Anterior</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $asistencias->appends(request()->query())->previousPageUrl() }}">‹ Anterior</a></li>
                                    @endif
                                    
                                    <li class="page-item disabled">
                                        <span class="page-link">Pág. {{ $asistencias->currentPage() }} de {{ $asistencias->lastPage() }}</span>
                                    </li>
                                    
                                    @if ($asistencias->hasMorePages())
                                        <li class="page-item"><a class="page-link" href="{{ $asistencias->appends(request()->query())->nextPageUrl() }}">Siguiente ›</a></li>
                                    @else
                                        <li class="page-item disabled"><span class="page-link">Siguiente ›</span></li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay registros de asistencia</h5>
                            <p class="text-muted">No se encontraron asistencias en el período seleccionado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection