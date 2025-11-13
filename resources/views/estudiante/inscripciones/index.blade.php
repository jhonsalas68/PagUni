@extends('layouts.dashboard')

@section('title', 'Inscripción de Materias')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>📚 Inscripción de Materias</h2>
                <a href="{{ route('estudiante.dashboard') }}" class="btn btn-secondary">
                    ← Volver al Dashboard
                </a>
            </div>
            
            @if($periodoActivo)
                <div class="alert alert-info">
                    <strong>Periodo Activo:</strong> {{ $periodoActivo->nombre }}<br>
                    <strong>Válido hasta:</strong> {{ $periodoActivo->fecha_fin->format('d/m/Y H:i') }}
                </div>
            @else
                <div class="alert alert-warning">
                    {{ $mensaje ?? 'No hay periodo de inscripción activo.' }}
                </div>
            @endif
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

    @if($periodoActivo && $grupos->isNotEmpty())
        <div class="row">
            @foreach($grupos as $grupo)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 {{ $grupo->ya_inscrito ? 'border-success' : '' }}">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ $grupo->materia->nombre }}</h5>
                            <small>Código: {{ $grupo->materia->codigo }}</small>
                        </div>
                        <div class="card-body">
                            <p><strong>Grupo:</strong> {{ $grupo->identificador }}</p>
                            <p><strong>Docente:</strong> 
                                @if($grupo->cargaAcademica->first())
                                    {{ $grupo->cargaAcademica->first()->profesor->nombre_completo }}
                                @else
                                    No asignado
                                @endif
                            </p>
                            
                            <p><strong>Horarios:</strong></p>
                            <ul class="list-unstyled">
                                @foreach($grupo->horarios as $horario)
                                    <li>
                                        <small>
                                            {{ implode(', ', array_map('ucfirst', $horario->dias_semana)) }}
                                            {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}
                                            <br>Aula: {{ $horario->aula->codigo_aula }}
                                        </small>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-3">
                                <span class="badge {{ $grupo->cupos_disponibles > 5 ? 'bg-success' : 'bg-warning' }}">
                                    {{ $grupo->cupos_disponibles }} cupos disponibles
                                </span>
                            </div>
                        </div>
                        <div class="card-footer">
                            @if($grupo->ya_inscrito_grupo)
                                <button class="btn btn-success w-100" disabled>
                                    ✓ Ya inscrito en este grupo
                                </button>
                            @elseif($grupo->ya_inscrito_materia)
                                <button class="btn btn-warning w-100" disabled>
                                    ⚠️ Ya inscrito en otro grupo
                                </button>
                                <small class="text-muted d-block mt-1">
                                    Debes dar de baja primero para cambiar de grupo
                                </small>
                            @elseif($grupo->cupos_disponibles > 0)
                                <form action="{{ route('estudiante.inscripciones.store') }}" method="POST" 
                                      onsubmit="return confirm('¿Confirmas la inscripción a esta materia?')">
                                    @csrf
                                    <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Inscribirse
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary w-100" disabled>
                                    Sin cupos
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($periodoActivo)
        <div class="alert alert-info">
            No hay materias disponibles para inscripción en este momento.
        </div>
    @endif
</div>
@endsection
