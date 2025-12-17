@extends('layouts.dashboard')

@section('title', 'Reporte de Rendimiento')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line"></i> Reporte de Rendimiento Académico
        </h1>
        <a href="{{ route('reportes.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
        </a>
    </div>

    @if(isset($error))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> {{ $error }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('reportes.rendimiento') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="materia_id" class="form-label font-weight-bold">Materia:</label>
                    <select name="materia_id" id="materia_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Seleccione Materia --</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ $materiaId == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nombre }} ({{ $materia->codigo }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="grupo_id" class="form-label font-weight-bold">Grupo:</label>
                    <select name="grupo_id" id="grupo_id" class="form-select" {{ $grupos->isEmpty() ? 'disabled' : '' }}>
                        <option value="">-- Seleccione Grupo --</option>
                        @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id }}" {{ $grupoId == $grupo->id ? 'selected' : '' }}>
                                Grupo {{ $grupo->identificador }}
                                @if($grupo->cargaAcademica && $grupo->cargaAcademica->first() && $grupo->cargaAcademica->first()->profesor)
                                    - {{ $grupo->cargaAcademica->first()->profesor->nombre }} {{ $grupo->cargaAcademica->first()->profesor->apellido }}
                                @else
                                    - Sin Docente
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(!empty($estudiantes))
        <!-- Resumen -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Estudiantes</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['total'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Aprobados</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['aprobados'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Reprobados</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['reprobados'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Promedio General</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['promedio_general'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calculator fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Resultados -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Detalle de Notas Finales</h6>
                @if(!empty($estudiantes))
                    <div class="btn-group">
                        <form action="{{ route('reportes.rendimiento-pdf') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="materia_id" value="{{ $materiaId }}">
                            <input type="hidden" name="grupo_id" value="{{ $grupoId }}">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf"></i> Descargar PDF
                            </button>
                        </form>
                        <form action="{{ route('reportes.rendimiento-excel') }}" method="POST" style="display: inline; margin-left: 5px;">
                            @csrf
                            <input type="hidden" name="materia_id" value="{{ $materiaId }}">
                            <input type="hidden" name="grupo_id" value="{{ $grupoId }}">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Descargar Excel
                            </button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Estudiante</th>
                                <th>Nota Final (0-100)</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estudiantes as $estudiante)
                                <tr>
                                    <td>{{ $estudiante['codigo'] }}</td>
                                    <td>{{ $estudiante['nombres'] }}</td>
                                    <td class="text-center font-weight-bold">{{ $estudiante['nota_final'] }}</td>
                                    <td class="text-center">
                                        @if($estudiante['estado'] == 'Aprobado')
                                            <span class="badge badge-success text-white" style="background-color: #28a745; font-size: 0.9em; padding: 0.4em 0.8em;">
                                                <i class="fas fa-check-circle"></i> Aprobado
                                            </span>
                                        @else
                                            <span class="badge badge-danger text-white" style="background-color: #dc3545; font-size: 0.9em; padding: 0.4em 0.8em;">
                                                <i class="fas fa-times-circle"></i> Reprobado
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif($grupoId)
        <div class="alert alert-info">
            No se encontraron estudiantes activos en este grupo.
        </div>
    @endif
</div>
@endsection
