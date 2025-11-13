@extends('layouts.dashboard')

@section('title', 'Historial de Asistencias')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>📊 Historial de Asistencias</h2>
                <a href="{{ route('estudiante.dashboard') }}" class="btn btn-secondary">
                    ← Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    @foreach($inscripciones as $inscripcion)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ $inscripcion->grupo->materia->nombre }}</h5>
                <small>Grupo: {{ $inscripcion->grupo->identificador }}</small>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="mb-0">{{ $inscripcion->calcularPorcentajeAsistencia() }}%</h3>
                            <small class="text-muted">Asistencia</small>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar {{ $inscripcion->calcularPorcentajeAsistencia() >= 80 ? 'bg-success' : 'bg-danger' }}" 
                                 role="progressbar" 
                                 style="width: {{ $inscripcion->calcularPorcentajeAsistencia() }}%">
                                {{ $inscripcion->calcularPorcentajeAsistencia() }}%
                            </div>
                        </div>
                        @if($inscripcion->tieneAsistenciaBaja())
                            <div class="alert alert-warning mt-2 py-2">
                                ⚠️ Tu asistencia está por debajo del 80% requerido
                            </div>
                        @endif
                    </div>
                </div>

                @if($inscripcion->asistencias->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Estado</th>
                                    <th>Método</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inscripcion->asistencias->sortByDesc('fecha') as $asistencia)
                                    <tr>
                                        <td>{{ $asistencia->fecha->format('d/m/Y') }}</td>
                                        <td>{{ $asistencia->hora_registro->format('H:i') }}</td>
                                        <td>
                                            @if($asistencia->estado === 'presente')
                                                <span class="badge bg-success">✓ Presente</span>
                                            @else
                                                <span class="badge bg-danger">✗ Ausente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($asistencia->metodo_registro === 'qr')
                                                📱 QR
                                            @elseif($asistencia->metodo_registro === 'manual')
                                                ✍️ Manual
                                            @else
                                                🤖 Automático
                                            @endif
                                        </td>
                                        <td>{{ $asistencia->observaciones ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No hay registros de asistencia aún.</p>
                @endif
            </div>
        </div>
    @endforeach

    @if($inscripciones->isEmpty())
        <div class="alert alert-info">
            No tienes materias inscritas.
        </div>
    @endif
</div>
@endsection
