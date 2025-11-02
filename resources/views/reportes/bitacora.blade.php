@extends('layouts.dashboard')

@section('title', 'Bitácora de Acceso y Uso')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-list-alt"></i> Bitácora de Acceso y Uso
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Reportes
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reportes.bitacora') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                           value="{{ $fechaInicio }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                           value="{{ $fechaFin }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="tipo_actividad" class="form-label">Tipo</label>
                                    <select class="form-select" id="tipo_actividad" name="tipo_actividad">
                                        <option value="todas" {{ $tipoActividad === 'todas' ? 'selected' : '' }}>Todas</option>
                                        <option value="qr_generados" {{ $tipoActividad === 'qr_generados' ? 'selected' : '' }}>QR Generados</option>
                                        <option value="asistencias" {{ $tipoActividad === 'asistencias' ? 'selected' : '' }}>Asistencias</option>
                                        <option value="faltas" {{ $tipoActividad === 'faltas' ? 'selected' : '' }}>Faltas</option>
                                        <option value="justificaciones" {{ $tipoActividad === 'justificaciones' ? 'selected' : '' }}>Justificaciones</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="profesor_id" class="form-label">Profesor</label>
                                    <select class="form-select" id="profesor_id" name="profesor_id">
                                        <option value="">Todos</option>
                                        @foreach($profesores as $profesor)
                                            <option value="{{ $profesor->id }}" {{ $profesorId == $profesor->id ? 'selected' : '' }}>
                                                {{ $profesor->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="materia_id" class="form-label">Materia</label>
                                    <select class="form-select" id="materia_id" name="materia_id">
                                        <option value="">Todas</option>
                                        @foreach($materias as $materia)
                                            <option value="{{ $materia->id }}" {{ $materiaId == $materia->id ? 'selected' : '' }}>
                                                {{ $materia->codigo }} - {{ $materia->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="aula_id" class="form-label">Aula</label>
                                    <select class="form-select" id="aula_id" name="aula_id">
                                        <option value="">Todas</option>
                                        @foreach($aulas as $aula)
                                            <option value="{{ $aula->id }}" {{ $aulaId == $aula->id ? 'selected' : '' }}>
                                                {{ $aula->codigo_aula }} - {{ $aula->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-search"></i> Filtrar
                                    </button>
                                    <a href="{{ route('reportes.bitacora') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Actividades
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['total_actividades'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                QR Generados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['qr_generados'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-qrcode fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Asistencias Registradas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['asistencias_registradas'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Docentes Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['docentes_activos'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Faltas Registradas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['faltas_registradas'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Justificaciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['justificaciones'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Materias Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['materias_activas'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-purple shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">
                                Aulas Utilizadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $estadisticas['aulas_utilizadas'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Rankings -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy"></i> Top Profesores Activos
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($profesoresActivos as $index => $profesor)
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3">
                                @if($index < 3)
                                    <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'dark') }} rounded-pill">
                                        {{ $index + 1 }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark rounded-pill">{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $profesor->nombre }} {{ $profesor->apellido }}</div>
                                <small class="text-muted">{{ $profesor->total_actividades }} actividades</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> No hay datos
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-book"></i> Materias Más Activas
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($materiasActivas as $index => $materia)
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3">
                                <span class="badge bg-success rounded-pill">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $materia->codigo }}</div>
                                <small class="text-muted">{{ $materia->nombre }}</small>
                                <br>
                                <small class="text-success">{{ $materia->total_actividades }} actividades</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> No hay datos
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-door-open"></i> Aulas Más Utilizadas
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($aulasUtilizadas as $index => $aula)
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3">
                                <span class="badge bg-info rounded-pill">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $aula->codigo_aula }}</div>
                                <small class="text-muted">{{ $aula->nombre }}</small>
                                <br>
                                <small class="text-info">{{ $aula->total_actividades }} actividades</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle"></i> No hay datos
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Actividades -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history"></i> Registro de Actividades
                    </h6>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="exportarBitacoraPDF()">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="exportarBitacoraExcel()">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Fecha/Hora</th>
                                    <th>Docente</th>
                                    <th>Actividad</th>
                                    <th>Materia</th>
                                    <th>Estado</th>
                                    <th>Modalidad</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($actividades as $actividad)
                                    <tr>
                                        <td>
                                            <small>
                                                {{ $actividad->created_at->format('d/m/Y') }}<br>
                                                <strong>{{ $actividad->created_at->format('H:i:s') }}</strong>
                                            </small>
                                        </td>
                                        <td>
                                            <strong>{{ $actividad->horario->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            @if($actividad->qr_token)
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-qrcode"></i> QR Generado
                                                </span>
                                            @else
                                                <span class="badge bg-info">
                                                    <i class="fas fa-clipboard-check"></i> Registro Asistencia
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $actividad->horario->cargaAcademica->grupo->materia->codigo ?? 'N/A' }}</strong>
                                            <br>
                                            {{ $actividad->horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">
                                                Grupo: {{ $actividad->horario->cargaAcademica->grupo->identificador ?? 'N/A' }}
                                            </small>
                                            @if($actividad->horario->aula)
                                                <br>
                                                <small class="text-info">
                                                    <i class="fas fa-door-open"></i> {{ $actividad->horario->aula->codigo_aula }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($actividad->estado)
                                                @case('presente')
                                                    <span class="badge bg-success">Presente</span>
                                                    @break
                                                @case('tardanza')
                                                    <span class="badge bg-warning">Tardanza</span>
                                                    @break
                                                @case('falta')
                                                    <span class="badge bg-danger">Falta</span>
                                                    @break
                                                @case('justificada')
                                                    <span class="badge bg-secondary">Justificada</span>
                                                    @break
                                                @case('pendiente_qr')
                                                    <span class="badge bg-primary">Pendiente QR</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light text-dark">{{ $actividad->estado }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($actividad->modalidad)
                                                <span class="badge bg-{{ $actividad->modalidad === 'presencial' ? 'primary' : 'info' }}">
                                                    {{ ucfirst($actividad->modalidad) }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                @if($actividad->hora_entrada)
                                                    <strong>Entrada:</strong> {{ $actividad->hora_entrada }}<br>
                                                @endif
                                                @if($actividad->hora_salida)
                                                    <strong>Salida:</strong> {{ $actividad->hora_salida }}<br>
                                                @endif
                                                @if($actividad->duracion_clase)
                                                    <strong>Duración:</strong> {{ $actividad->duracion_clase }} min<br>
                                                @endif
                                                @if($actividad->numero_sesion)
                                                    <strong>Sesión:</strong> #{{ $actividad->numero_sesion }}
                                                @endif
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            No se encontraron actividades en el período seleccionado
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($actividades->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $actividades->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formularios ocultos para exportación -->
<form id="exportPdfForm" action="{{ route('reportes.bitacora-pdf') }}" method="POST" style="display: none;" target="_blank">
    @csrf
    <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
    <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
    <input type="hidden" name="tipo_actividad" value="{{ $tipoActividad }}">
    <input type="hidden" name="profesor_id" value="{{ $profesorId }}">
    <input type="hidden" name="materia_id" value="{{ $materiaId }}">
    <input type="hidden" name="aula_id" value="{{ $aulaId }}">
</form>

<form id="exportExcelForm" action="{{ route('reportes.bitacora-excel') }}" method="POST" style="display: none;" target="_blank">
    @csrf
    <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
    <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
    <input type="hidden" name="tipo_actividad" value="{{ $tipoActividad }}">
    <input type="hidden" name="profesor_id" value="{{ $profesorId }}">
    <input type="hidden" name="materia_id" value="{{ $materiaId }}">
    <input type="hidden" name="aula_id" value="{{ $aulaId }}">
</form>

<script>
function exportarBitacoraPDF() {
    // Actualizar valores del formulario con los filtros actuales
    const form = document.getElementById('exportPdfForm');
    const urlParams = new URLSearchParams(window.location.search);
    
    form.querySelector('input[name="fecha_inicio"]').value = urlParams.get('fecha_inicio') || '{{ $fechaInicio }}';
    form.querySelector('input[name="fecha_fin"]').value = urlParams.get('fecha_fin') || '{{ $fechaFin }}';
    form.querySelector('input[name="tipo_actividad"]').value = urlParams.get('tipo_actividad') || '{{ $tipoActividad }}';
    form.querySelector('input[name="profesor_id"]').value = urlParams.get('profesor_id') || '{{ $profesorId }}';
    form.querySelector('input[name="materia_id"]').value = urlParams.get('materia_id') || '{{ $materiaId }}';
    form.querySelector('input[name="aula_id"]').value = urlParams.get('aula_id') || '{{ $aulaId }}';
    
    form.submit();
}

function exportarBitacoraExcel() {
    // Actualizar valores del formulario con los filtros actuales
    const form = document.getElementById('exportExcelForm');
    const urlParams = new URLSearchParams(window.location.search);
    
    form.querySelector('input[name="fecha_inicio"]').value = urlParams.get('fecha_inicio') || '{{ $fechaInicio }}';
    form.querySelector('input[name="fecha_fin"]').value = urlParams.get('fecha_fin') || '{{ $fechaFin }}';
    form.querySelector('input[name="tipo_actividad"]').value = urlParams.get('tipo_actividad') || '{{ $tipoActividad }}';
    form.querySelector('input[name="profesor_id"]').value = urlParams.get('profesor_id') || '{{ $profesorId }}';
    form.querySelector('input[name="materia_id"]').value = urlParams.get('materia_id') || '{{ $materiaId }}';
    form.querySelector('input[name="aula_id"]').value = urlParams.get('aula_id') || '{{ $aulaId }}';
    
    form.submit();
}
</script>
@endsection