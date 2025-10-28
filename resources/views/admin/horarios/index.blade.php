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
                            <th>Día y Aula</th>
                            <th>Horario</th>
                            <th>Materia</th>
                            <th>Profesor</th>
                            <th>Grupo</th>
                            <th>Tipo de Clase</th>
                            <th>Período</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($horarios as $horario)
                        <tr>
                            <td>
                                @php
                                    $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                    $diasCortos = ['', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                                    
                                    // Obtener todos los horarios de la misma materia para mostrar el patrón completo
                                    $horariosMateria = \App\Models\Horario::where('carga_academica_id', $horario->carga_academica_id)
                                        ->with(['aula'])
                                        ->orderBy('dia_semana')
                                        ->get();
                                    
                                    $patronCompleto = [];
                                    foreach($horariosMateria as $h) {
                                        $diaTexto = $diasCortos[$h->dia_semana] ?? 'N/A';
                                        $aulaTexto = $h->aula->codigo_aula ?? 'N/A';
                                        if($h->aula->tipo_aula === 'laboratorio') {
                                            $aulaTexto = 'Lab ' . str_replace(['Lab', 'LAB', 'Laboratorio'], '', $aulaTexto);
                                        }
                                        $patronCompleto[] = $diaTexto . ' ' . $aulaTexto;
                                    }
                                    $patronTexto = implode(' - ', $patronCompleto);
                                @endphp
                                <div>
                                    <strong>{{ $patronTexto }}</strong>
                                    @if($horariosMateria->count() > 1)
                                        <br><small class="text-muted">{{ $horariosMateria->count() }} días por semana</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong>{{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}</strong>
                                <br><small class="text-success">{{ $horario->duracion_horas ? number_format($horario->duracion_horas, 1) . 'h' : 'N/A' }}</small>
                            </td>
                            <td>
                                <strong>{{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $horario->cargaAcademica->grupo->materia->codigo ?? '' }}</small>
                            </td>
                            <td>{{ $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @php
                                    $tipos = [
                                        'teorica' => ['bg-info', 'fas fa-book'],
                                        'practica' => ['bg-warning text-dark', 'fas fa-tools'],
                                        'laboratorio' => ['bg-success', 'fas fa-flask']
                                    ];
                                    $tipoConfig = $tipos[$horario->tipo_clase] ?? ['bg-secondary', 'fas fa-question'];
                                @endphp
                                <span class="badge {{ $tipoConfig[0] }}">
                                    <i class="{{ $tipoConfig[1] }}"></i> {{ ucfirst($horario->tipo_clase) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $horario->periodo_academico ?? 'N/A' }}</span>
                                @if($horario->es_semestral)
                                    <br><small class="badge bg-success">Semestral</small>
                                @endif
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