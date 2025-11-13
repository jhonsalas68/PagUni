@extends('layouts.dashboard')

@section('title', 'Dashboard Estudiante')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Estudiante</h1>
            </div>
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">📚 Materias Inscritas</h5>
                    <h2 class="mb-0">{{ $totalMaterias ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">✓ Asistencia Promedio</h5>
                    <h2 class="mb-0">{{ number_format($promedioAsistencia ?? 0, 1) }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">📅 Clases Hoy</h5>
                    <h2 class="mb-0">{{ $clasesHoy ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">⚠️ Alertas</h5>
                    <h2 class="mb-0">{{ $alertas ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⚡ Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('estudiante.asistencia.escaner') }}" class="btn btn-primary btn-lg w-100">
                                📱 Marcar Asistencia
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('estudiante.mis-materias') }}" class="btn btn-info btn-lg w-100">
                                📖 Mis Materias
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('estudiante.inscripciones.index') }}" class="btn btn-success btn-lg w-100">
                                ➕ Inscribir Materias
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('estudiante.asistencia.historial') }}" class="btn btn-secondary btn-lg w-100">
                                📊 Ver Historial
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mis materias -->
    @if(isset($inscripciones) && $inscripciones->isNotEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📚 Mis Materias</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($inscripciones as $inscripcion)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $inscripcion->grupo->materia->nombre }}</h6>
                                            <p class="card-text small text-muted">
                                                Grupo: {{ $inscripcion->grupo->identificador }}<br>
                                                @if($inscripcion->grupo->cargaAcademica->first())
                                                    Docente: {{ $inscripcion->grupo->cargaAcademica->first()->profesor->nombre_completo }}
                                                @endif
                                            </p>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar {{ $inscripcion->porcentaje_asistencia >= 80 ? 'bg-success' : 'bg-danger' }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $inscripcion->porcentaje_asistencia }}%">
                                                    {{ number_format($inscripcion->porcentaje_asistencia, 1) }}%
                                                </div>
                                            </div>
                                            @if($inscripcion->porcentaje_asistencia < 80)
                                                <small class="text-danger">⚠️ Asistencia baja</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5>👋 ¡Bienvenido {{ session('user_name') }}!</h5>
                    <p>No tienes materias inscritas aún. 
                        <a href="{{ route('estudiante.inscripciones.index') }}" class="alert-link">Haz clic aquí para inscribirte</a>
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
