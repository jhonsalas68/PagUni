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
            <!-- Filtros de búsqueda -->
            <form method="GET" action="{{ route('admin.horarios.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="buscar" class="form-label">Buscar por Materia, Profesor o Aula</label>
                        <input type="text" 
                               class="form-control" 
                               id="buscar" 
                               name="buscar" 
                               value="{{ request('buscar') }}" 
                               placeholder="Ej: Cálculo, Juan Pérez, A-101">
                    </div>
                    <div class="col-md-3">
                        <label for="dia" class="form-label">Día de la Semana</label>
                        <select class="form-select" id="dia" name="dia">
                            <option value="">Todos los días</option>
                            <option value="lunes" {{ request('dia') == 'lunes' ? 'selected' : '' }}>Lunes</option>
                            <option value="martes" {{ request('dia') == 'martes' ? 'selected' : '' }}>Martes</option>
                            <option value="miercoles" {{ request('dia') == 'miercoles' ? 'selected' : '' }}>Miércoles</option>
                            <option value="jueves" {{ request('dia') == 'jueves' ? 'selected' : '' }}>Jueves</option>
                            <option value="viernes" {{ request('dia') == 'viernes' ? 'selected' : '' }}>Viernes</option>
                            <option value="sabado" {{ request('dia') == 'sabado' ? 'selected' : '' }}>Sábado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="tipo_clase" class="form-label">Tipo de Clase</label>
                        <select class="form-select" id="tipo_clase" name="tipo_clase">
                            <option value="">Todos</option>
                            <option value="teorica" {{ request('tipo_clase') == 'teorica' ? 'selected' : '' }}>Teórica</option>
                            <option value="practica" {{ request('tipo_clase') == 'practica' ? 'selected' : '' }}>Práctica</option>
                            <option value="laboratorio" {{ request('tipo_clase') == 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>

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
                                    $diasMap = [
                                        'lunes' => 'Lu',
                                        'martes' => 'Ma',
                                        'miercoles' => 'Mi',
                                        'jueves' => 'Ju',
                                        'viernes' => 'Vi',
                                        'sabado' => 'Sa'
                                    ];
                                    
                                    $diasTexto = [];
                                    foreach($horario->dias_semana as $dia) {
                                        $diasTexto[] = $diasMap[$dia] ?? ucfirst(substr($dia, 0, 2));
                                    }
                                    $diasStr = implode(' ', $diasTexto);
                                    $aulaTexto = $horario->aula->codigo_aula ?? 'N/A';
                                @endphp
                                <div>
                                    <strong>{{ $diasStr }}</strong>
                                    <br><small class="text-muted">{{ $aulaTexto }}</small>
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
                            <td>
                                {{ $horario->cargaAcademica->profesor->nombre ?? 'N/A' }} 
                                {{ $horario->cargaAcademica->profesor->apellido ?? '' }}
                            </td>
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
                            <td colspan="8" class="text-center">No hay horarios registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $horarios->firstItem() ?? 0 }} a {{ $horarios->lastItem() ?? 0 }} 
                    de {{ $horarios->total() }} horarios
                </div>
                <div>
                    {{ $horarios->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para la paginación Bootstrap */
.pagination {
    margin: 0;
}
.pagination .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #0d6efd;
    background-color: #fff;
    border: 1px solid #dee2e6;
}
.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}
.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}
.pagination .page-link:hover {
    color: #0a58ca;
    background-color: #e9ecef;
    border-color: #dee2e6;
}
</style>

<script>
// Auto-scroll al inicio al cambiar de página
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.search.includes('page=')) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>
@endsection