@extends('layouts.dashboard')

@section('title', 'Marcar Asistencia')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>✓ Marcar Asistencia</h2>
                    <p class="text-muted mb-0">Marca tu asistencia para las clases de hoy</p>
                </div>
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

    @if(empty($clasesHoy))
        <div class="alert alert-info">
            <h5>📅 No tienes clases hoy</h5>
            <p class="mb-0">Vuelve durante el horario de tus clases para marcar asistencia.</p>
        </div>
    @else
        <div class="row">
            @foreach($clasesHoy as $clase)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 {{ $clase['ya_marco'] ? 'border-success' : ($clase['en_horario'] ? 'border-primary' : 'border-secondary') }}">
                        <div class="card-header {{ $clase['ya_marco'] ? 'bg-success text-white' : ($clase['en_horario'] ? 'bg-primary text-white' : 'bg-secondary text-white') }}">
                            <h5 class="mb-0">{{ $clase['inscripcion']->grupo->materia->nombre }}</h5>
                            <small>Grupo: {{ $clase['inscripcion']->grupo->identificador }}</small>
                        </div>
                        <div class="card-body">
                            <p><strong>📍 Modalidad:</strong> Presencial</p>
                            <p><strong>🕐 Horario:</strong> {{ $clase['hora_inicio'] }} - {{ $clase['hora_fin'] }}</p>
                            <p><strong>🏫 Aula:</strong> {{ $clase['horario']->aula->codigo_aula }}</p>
                            <p><strong>📚 Tipo:</strong> {{ ucfirst($clase['horario']->tipo_clase ?? 'N/A') }}</p>

                            @if($clase['ya_marco'])
                                <div class="alert alert-success py-2 px-3 mb-0">
                                    <strong>✓ Asistencia marcada</strong>
                                </div>
                            @elseif($clase['en_horario'])
                                <div class="alert alert-info py-2 px-3 mb-3">
                                    <strong>⏰ Clase en curso</strong><br>
                                    <small>Puedes marcar tu asistencia ahora</small>
                                </div>
                                
                                <form action="{{ route('estudiante.asistencia.marcar') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="inscripcion_id" value="{{ $clase['inscripcion']->id }}">
                                    <input type="hidden" name="horario_id" value="{{ $clase['horario']->id }}">
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="confirmar{{ $clase['horario']->id }}" required>
                                        <label class="form-check-label" for="confirmar{{ $clase['horario']->id }}">
                                            Confirmo mi asistencia a esta clase
                                        </label>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        ✓ Marcar Asistencia
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-warning py-2 px-3 mb-0">
                                    <strong>⏳ Fuera de horario</strong><br>
                                    <small>Solo puedes marcar durante la clase</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5>ℹ️ Información Importante</h5>
                <ul class="mb-0">
                    <li><strong>Horario:</strong> Debes marcar tu asistencia durante el horario de clase</li>
                    <li><strong>Tolerancia:</strong> Puedes marcar desde 15 minutos antes hasta 15 minutos después</li>
                    <li><strong>Una vez por clase:</strong> Solo puedes marcar una vez por día para cada materia</li>
                    <li><strong>Confirmación:</strong> Debes confirmar con el checkbox antes de marcar</li>
                </ul>
            </div>
        </div>
    @endif
</div>
@endsection
