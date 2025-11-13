@extends('layouts.dashboard')

@section('title', 'Mis Materias')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>📖 Mis Materias Inscritas</h2>
                <a href="{{ route('estudiante.dashboard') }}" class="btn btn-secondary">
                    ← Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($inscripciones->isEmpty())
        <div class="alert alert-info">
            No tienes materias inscritas en este momento.
            <a href="{{ route('estudiante.inscripciones.index') }}" class="alert-link">Ir a inscripciones</a>
        </div>
    @else
        <div class="row">
            @foreach($inscripciones as $inscripcion)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ $inscripcion->grupo->materia->nombre }}</h5>
                            <small>Grupo: {{ $inscripcion->grupo->identificador }}</small>
                        </div>
                        <div class="card-body">
                            <p><strong>Docente:</strong> 
                                @if($inscripcion->grupo->cargaAcademica->first())
                                    {{ $inscripcion->grupo->cargaAcademica->first()->profesor->nombre_completo }}
                                @else
                                    No asignado
                                @endif
                            </p>
                            
                            <p><strong>Horarios:</strong></p>
                            <ul class="list-unstyled">
                                @foreach($inscripcion->grupo->horarios as $horario)
                                    <li class="mb-2">
                                        <small>
                                            <strong>{{ implode(', ', array_map('ucfirst', $horario->dias_semana)) }}</strong>
                                            <br>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}
                                            <br>Aula: {{ $horario->aula->codigo_aula }} ({{ ucfirst($horario->aula->tipo_aula) }})
                                            <br>
                                            @if($horario->tipo_clase === 'teorica')
                                                <span class="badge bg-primary">📚 Teórica</span>
                                            @elseif($horario->tipo_clase === 'practica')
                                                <span class="badge bg-success">💻 Práctica</span>
                                            @elseif($horario->tipo_clase === 'laboratorio')
                                                <span class="badge bg-info">🔬 Laboratorio</span>
                                            @else
                                                <span class="badge bg-secondary">📖 {{ ucfirst($horario->tipo_clase ?? 'N/A') }}</span>
                                            @endif
                                            <span class="badge bg-success">🏫 Presencial</span>
                                        </small>
                                    </li>
                                @endforeach
                            </ul>

                            @if(isset($clasesHoyPorInscripcion[$inscripcion->id]) && !empty($clasesHoyPorInscripcion[$inscripcion->id]))
                                <div class="alert alert-info py-2 px-3 mb-0">
                                    <strong>📅 Clase de hoy:</strong>
                                    @foreach($clasesHoyPorInscripcion[$inscripcion->id] as $clase)
                                        <div class="mt-2">
                                            <small>
                                                🕐 {{ $clase['horario']->hora_inicio }} - {{ $clase['horario']->hora_fin }}
                                                <br>
                                                @if($clase['ya_marco'])
                                                    <span class="badge bg-success">✓ Asistencia marcada ({{ ucfirst($clase['estado_asistencia']) }})</span>
                                                @elseif($clase['en_horario'])
                                                    <span class="badge bg-warning">⏰ En horario - Puedes marcar</span>
                                                @else
                                                    <span class="badge bg-secondary">⏳ Fuera de horario</span>
                                                @endif
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-3">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar {{ $inscripcion->porcentaje_asistencia >= 80 ? 'bg-success' : 'bg-danger' }}" 
                                         role="progressbar" 
                                         style="width: {{ $inscripcion->porcentaje_asistencia }}%"
                                         aria-valuenow="{{ $inscripcion->porcentaje_asistencia }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($inscripcion->porcentaje_asistencia, 1) }}%
                                    </div>
                                </div>
                                <small class="text-muted">Asistencia</small>
                                
                                @if($inscripcion->porcentaje_asistencia < 80)
                                    <div class="alert alert-warning mt-2 py-1 px-2 small">
                                        ⚠️ Asistencia baja
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-grid gap-2">
                                @if(isset($clasesHoyPorInscripcion[$inscripcion->id]) && !empty($clasesHoyPorInscripcion[$inscripcion->id]))
                                    @php
                                        $puedeMarcar = collect($clasesHoyPorInscripcion[$inscripcion->id])->contains('puede_marcar', true);
                                    @endphp
                                    <button type="button" 
                                            class="btn btn-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalAsistencia{{ $inscripcion->id }}"
                                            {{ !$puedeMarcar ? 'disabled' : '' }}>
                                        ✓ Marcar Asistencia
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary btn-sm" disabled>
                                        Sin clases hoy
                                    </button>
                                @endif
                                
                                @if($inscripcion->puede_dar_baja)
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalBaja{{ $inscripcion->id }}">
                                        Dar de Baja
                                    </button>
                                @endif
                            </div>

                            <!-- Modal de Marcar Asistencia -->
                            @if(isset($clasesHoyPorInscripcion[$inscripcion->id]) && !empty($clasesHoyPorInscripcion[$inscripcion->id]))
                                <div class="modal fade" id="modalAsistencia{{ $inscripcion->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">✓ Marcar Asistencia</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h6>{{ $inscripcion->grupo->materia->nombre }}</h6>
                                                <p class="text-muted mb-3">Grupo: {{ $inscripcion->grupo->identificador }}</p>

                                                @foreach($clasesHoyPorInscripcion[$inscripcion->id] as $clase)
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <p class="mb-2">
                                                                <strong>🕐 Horario:</strong> {{ $clase['horario']->hora_inicio }} - {{ $clase['horario']->hora_fin }}
                                                            </p>
                                                            <p class="mb-2">
                                                                <strong>🏫 Aula:</strong> {{ $clase['horario']->aula->codigo_aula }}
                                                            </p>
                                                            <p class="mb-3">
                                                                <strong>📚 Tipo:</strong> 
                                                                @if($clase['horario']->tipo_clase === 'teorica')
                                                                    <span class="badge bg-primary">Teórica</span>
                                                                @elseif($clase['horario']->tipo_clase === 'practica')
                                                                    <span class="badge bg-success">Práctica</span>
                                                                @elseif($clase['horario']->tipo_clase === 'laboratorio')
                                                                    <span class="badge bg-info">Laboratorio</span>
                                                                @endif
                                                                <span class="badge bg-success">Presencial</span>
                                                            </p>

                                                            @if($clase['ya_marco'])
                                                                <div class="alert alert-success mb-0">
                                                                    <strong>✓ Asistencia ya marcada</strong>
                                                                    <br><small>Estado: {{ ucfirst($clase['estado_asistencia']) }}</small>
                                                                </div>
                                                            @elseif($clase['puede_marcar'])
                                                                <form action="{{ route('estudiante.asistencia.marcar') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="inscripcion_id" value="{{ $inscripcion->id }}">
                                                                    <input type="hidden" name="horario_id" value="{{ $clase['horario']->id }}">
                                                                    
                                                                    <div class="form-check mb-3">
                                                                        <input class="form-check-input" type="checkbox" id="confirmar{{ $inscripcion->id }}_{{ $clase['horario']->id }}" required>
                                                                        <label class="form-check-label" for="confirmar{{ $inscripcion->id }}_{{ $clase['horario']->id }}">
                                                                            Confirmo mi asistencia a esta clase
                                                                        </label>
                                                                    </div>
                                                                    
                                                                    <button type="submit" class="btn btn-primary w-100">
                                                                        ✓ Marcar Asistencia
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <div class="alert alert-warning mb-0">
                                                                    <strong>⏳ Fuera de horario</strong>
                                                                    <br><small>Solo puedes marcar durante la clase (15 min antes/después)</small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($inscripcion->puede_dar_baja)
                                <!-- Modal de confirmación de baja -->
                                <div class="modal fade" id="modalBaja{{ $inscripcion->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar Baja</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('estudiante.inscripciones.destroy', $inscripcion->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-body">
                                                    <p>¿Estás seguro de dar de baja la materia <strong>{{ $inscripcion->grupo->materia->nombre }}</strong>?</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Motivo (opcional)</label>
                                                        <textarea name="motivo" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger">Confirmar Baja</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5>ℹ️ Información sobre Asistencia</h5>
                <ul class="mb-0">
                    <li><strong>Marcar Asistencia:</strong> Solo puedes marcar durante el horario de clase (15 min antes/después)</li>
                    <li><strong>Clases de Hoy:</strong> Se muestran con un indicador en cada materia</li>
                    <li><strong>Estado:</strong> Verás si ya marcaste asistencia o si estás en horario para marcar</li>
                    <li><strong>Tipo de Clase:</strong> Se indica si es teórica, práctica o laboratorio</li>
                    <li><strong>Modalidad:</strong> Todas las clases son presenciales</li>
                </ul>
            </div>
        </div>
    @endif
</div>
@endsection
