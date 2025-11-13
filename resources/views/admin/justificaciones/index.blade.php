@extends('layouts.dashboard')

@section('title', 'Justificaciones de Asistencia')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-file-medical"></i> Justificaciones de Asistencia
        </h1>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.justificaciones.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                               value="{{ request('fecha_inicio', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                               value="{{ request('fecha_fin', now()->endOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo" class="form-label">Tipo de Justificación</label>
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="medica" {{ request('tipo') == 'medica' ? 'selected' : '' }}>Médica</option>
                            <option value="personal" {{ request('tipo') == 'personal' ? 'selected' : '' }}>Personal</option>
                            <option value="academica" {{ request('tipo') == 'academica' ? 'selected' : '' }}>Académica</option>
                            <option value="administrativa" {{ request('tipo') == 'administrativa' ? 'selected' : '' }}>Administrativa</option>
                            <option value="otra" {{ request('tipo') == 'otra' ? 'selected' : '' }}>Otra</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('admin.justificaciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $justificaciones->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Médicas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['medica'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hospital fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Personales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['personal'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Académicas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['academica'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Justificaciones -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Justificaciones</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Profesor</th>
                            <th>Materia</th>
                            <th>Horario</th>
                            <th>Tipo</th>
                            <th>Estado Original</th>
                            <th>Justificación</th>
                            <th>Fecha Justificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($justificaciones as $asistencia)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}</td>
                            <td>
                                {{ $asistencia->profesor->nombre ?? 'N/A' }} 
                                {{ $asistencia->profesor->apellido ?? '' }}
                            </td>
                            <td>{{ $asistencia->horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</td>
                            <td>
                                {{ substr($asistencia->horario->hora_inicio, 0, 5) }} - 
                                {{ substr($asistencia->horario->hora_fin, 0, 5) }}
                            </td>
                            <td>
                                @php
                                    $tipos = [
                                        'medica' => ['bg-info', 'fas fa-hospital'],
                                        'personal' => ['bg-warning text-dark', 'fas fa-user'],
                                        'academica' => ['bg-success', 'fas fa-graduation-cap'],
                                        'administrativa' => ['bg-primary', 'fas fa-building'],
                                        'otra' => ['bg-secondary', 'fas fa-question']
                                    ];
                                    $tipoConfig = $tipos[$asistencia->tipo_justificacion] ?? ['bg-secondary', 'fas fa-question'];
                                @endphp
                                <span class="badge {{ $tipoConfig[0] }}">
                                    <i class="{{ $tipoConfig[1] }}"></i> {{ ucfirst($asistencia->tipo_justificacion) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $asistencia->estado === 'justificado' ? 'info' : 'danger' }}">
                                    {{ $asistencia->estado === 'justificado' ? 'Justificado' : 'Ausente/Tardanza' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="verJustificacion('{{ addslashes($asistencia->justificacion) }}', '{{ $asistencia->profesor->nombre ?? '' }} {{ $asistencia->profesor->apellido ?? '' }}')">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $asistencia->fecha_justificacion ? \Carbon\Carbon::parse($asistencia->fecha_justificacion)->format('d/m/Y H:i') : 'N/A' }}
                                </small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay justificaciones registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $justificaciones->firstItem() ?? 0 }} a {{ $justificaciones->lastItem() ?? 0 }} 
                    de {{ $justificaciones->total() }} justificaciones
                </div>
                <div>
                    {{ $justificaciones->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver justificación -->
<div class="modal fade" id="modalJustificacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Justificación de <span id="nombreProfesor"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="textoJustificacion"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function verJustificacion(texto, profesor) {
    document.getElementById('nombreProfesor').textContent = profesor;
    document.getElementById('textoJustificacion').textContent = texto;
    new bootstrap.Modal(document.getElementById('modalJustificacion')).show();
}
</script>
@endsection
