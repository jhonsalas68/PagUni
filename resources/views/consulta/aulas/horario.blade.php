@extends('layouts.app')

@section('title', 'Horario del Aula ' . $aula->codigo_aula)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-door-open"></i> Horario del Aula {{ $aula->codigo_aula }}
                        </h4>
                        <div>
                            @auth
                                @if(auth()->user()->tipo === 'administrador')
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                                        <i class="fas fa-home"></i> Volver al Dashboard
                                    </a>
                                @elseif(auth()->user()->tipo === 'profesor')
                                    <a href="{{ route('profesor.dashboard') }}" class="btn btn-light btn-sm">
                                        <i class="fas fa-home"></i> Volver al Dashboard
                                    </a>
                                @elseif(auth()->user()->tipo === 'estudiante')
                                    <a href="{{ route('estudiante.dashboard') }}" class="btn btn-light btn-sm">
                                        <i class="fas fa-home"></i> Volver al Dashboard
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('consulta.aulas.index') }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            @endauth
                            <button class="btn btn-light btn-sm" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Información del Aula -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5 class="text-primary">{{ $aula->nombre }}</h5>
                            <p class="text-muted mb-1">
                                <i class="fas fa-users"></i> Capacidad: {{ $aula->capacidad }} personas
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-tag"></i> Tipo: {{ ucfirst($aula->tipo) }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fechaConsulta" class="form-label">Fecha de Consulta:</label>
                                <input type="date" class="form-control" id="fechaConsulta" 
                                       value="{{ $fecha }}" onchange="cambiarFecha()">
                            </div>
                        </div>
                    </div>

                    <!-- Horario del Día -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-day"></i> 
                                Horario para {{ $fechaCarbon->format('d/m/Y') }} 
                                ({{ $fechaCarbon->locale('es')->dayName }})
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($horarios->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Horario</th>
                                                <th>Materia</th>
                                                <th>Profesor</th>
                                                <th>Grupo</th>
                                                <th>Estado</th>
                                                <th>Modalidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($horarios as $horario)
                                                @php
                                                    $asistencia = $asistencias->get($horario->id);
                                                    $ahora = now();
                                                    
                                                    // Intentar parsear con múltiples formatos
                                                    try {
                                                        $horaInicio = \Carbon\Carbon::createFromFormat('H:i', $horario->hora_inicio);
                                                    } catch (\Exception $e) {
                                                        try {
                                                            $horaInicio = \Carbon\Carbon::createFromFormat('H:i:s', $horario->hora_inicio);
                                                        } catch (\Exception $e2) {
                                                            $horaInicio = \Carbon\Carbon::parse($horario->hora_inicio);
                                                        }
                                                    }
                                                    
                                                    try {
                                                        $horaFin = \Carbon\Carbon::createFromFormat('H:i', $horario->hora_fin);
                                                    } catch (\Exception $e) {
                                                        try {
                                                            $horaFin = \Carbon\Carbon::createFromFormat('H:i:s', $horario->hora_fin);
                                                        } catch (\Exception $e2) {
                                                            $horaFin = \Carbon\Carbon::parse($horario->hora_fin);
                                                        }
                                                    }
                                                    
                                                    $estadoClase = 'programada';
                                                    $colorEstado = 'secondary';
                                                    
                                                    if ($asistencia) {
                                                        switch ($asistencia->estado) {
                                                            case 'presente':
                                                                $estadoClase = 'Completada';
                                                                $colorEstado = 'success';
                                                                break;
                                                            case 'en_clase':
                                                                $estadoClase = 'En Clase';
                                                                $colorEstado = 'primary';
                                                                break;
                                                            case 'tardanza':
                                                                $estadoClase = 'Con Tardanza';
                                                                $colorEstado = 'warning';
                                                                break;
                                                            case 'justificado':
                                                                $estadoClase = 'Justificada';
                                                                $colorEstado = 'info';
                                                                break;
                                                            case 'pendiente_qr':
                                                                $estadoClase = 'QR Generado';
                                                                $colorEstado = 'warning';
                                                                break;
                                                        }
                                                    } elseif ($fechaCarbon->isToday() && $ahora->gt($horaFin)) {
                                                        $estadoClase = 'Ausente';
                                                        $colorEstado = 'danger';
                                                    } elseif ($fechaCarbon->isToday() && $ahora->between($horaInicio, $horaFin)) {
                                                        $estadoClase = 'En Horario';
                                                        $colorEstado = 'warning';
                                                    }
                                                @endphp
                                                
                                                <tr>
                                                    <td>
                                                        <strong>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ ($horario->cargaAcademica->profesor->nombre ?? '') . ' ' . ($horario->cargaAcademica->profesor->apellido ?? '') }}
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            {{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $colorEstado }}">
                                                            {{ $estadoClase }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($asistencia && $asistencia->modalidad)
                                                            <span class="badge bg-{{ $asistencia->modalidad === 'presencial' ? 'primary' : 'info' }}">
                                                                {{ ucfirst($asistencia->modalidad) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Resumen del día -->
                                <div class="row mt-4">
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="text-primary">Total Clases</h6>
                                                <h4>{{ $horarios->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="text-success">Completadas</h6>
                                                <h4>{{ $asistencias->where('estado', 'presente')->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="text-warning">En Proceso</h6>
                                                <h4>{{ $asistencias->whereIn('estado', ['en_clase', 'pendiente_qr'])->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="text-danger">Sin Registro</h6>
                                                <h4>{{ $horarios->count() - $asistencias->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">No hay clases programadas</h4>
                                    <p class="text-muted">
                                        El aula {{ $aula->codigo_aula }} no tiene clases programadas para 
                                        {{ $fechaCarbon->format('d/m/Y') }}.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cambiarFecha() {
    const fecha = document.getElementById('fechaConsulta').value;
    if (fecha) {
        const url = new URL(window.location);
        url.searchParams.set('fecha', fecha);
        window.location.href = url.toString();
    }
}
</script>

<style>
@media print {
    .btn, .form-group { display: none !important; }
    .card { border: 1px solid #000 !important; }
}
</style>
@endsection