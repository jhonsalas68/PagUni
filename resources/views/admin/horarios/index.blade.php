@extends('layouts.dashboard')

@section('title', 'Gestión de Horarios')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Horarios</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.horarios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Horario
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Horarios</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Hora</th>
                            <th>Duración</th>
                            <th>Materia</th>
                            <th>Profesor</th>
                            <th>Grupo</th>
                            <th>Aula</th>
                            <th>Período</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($horarios as $horario)
                        <tr>
                            <td>
                                @php
                                    $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                @endphp
                                <span class="badge bg-primary">{{ $dias[$horario->dia_semana] ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $horario->duracion_horas ? number_format($horario->duracion_horas, 2) . ' hrs' : 'N/A' }}
                                </span>
                                @if($horario->es_semestral)
                                    <br><small class="text-muted">{{ number_format($horario->carga_horaria_semestral, 0) }} hrs/sem</small>
                                @endif
                            </td>
                            <td>{{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</td>
                            <td>{{ $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</td>
                            <td>{{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</td>
                            <td>{{ $horario->aula->codigo_aula ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $horario->periodo_academico ?? 'N/A' }}</span>
                                @if($horario->es_semestral)
                                    <br><small class="badge bg-success">Semestral</small>
                                @else
                                    <br><small class="badge bg-warning">Específico</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $tipos = [
                                        'teorica' => 'bg-info',
                                        'practica' => 'bg-warning',
                                        'laboratorio' => 'bg-success'
                                    ];
                                @endphp
                                <span class="badge {{ $tipos[$horario->tipo_clase] ?? 'bg-secondary' }}">
                                    {{ ucfirst($horario->tipo_clase) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.horarios.show', $horario) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.horarios.edit', $horario) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.horarios.destroy', $horario) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar este horario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No hay horarios registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection