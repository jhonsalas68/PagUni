@extends('layouts.dashboard')

@section('title', 'Gestión de Materias')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Materias</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.materias.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Materia
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Materias</h6>
        </div>
        <div class="card-body">
            <!-- Filtros de búsqueda -->
            <form method="GET" action="{{ route('admin.materias.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="buscar" class="form-label">Buscar por Nombre o Código</label>
                        <input type="text" 
                               class="form-control" 
                               id="buscar" 
                               name="buscar" 
                               value="{{ request('buscar') }}" 
                               placeholder="Ej: Cálculo, MAT-101">
                    </div>
                    <div class="col-md-3">
                        <label for="semestre" class="form-label">Semestre</label>
                        <select class="form-select" id="semestre" name="semestre">
                            <option value="">Todos los semestres</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('semestre') == $i ? 'selected' : '' }}>
                                    {{ $i }}° Semestre
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('admin.materias.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Carrera</th>
                            <th>Facultad</th>
                            <th>Semestre</th>
                            <th>Créditos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materias as $materia)
                        <tr>
                            <td><span class="badge bg-primary">{{ $materia->codigo }}</span></td>
                            <td>{{ $materia->nombre }}</td>
                            <td>{{ $materia->carrera->nombre ?? 'N/A' }}</td>
                            <td>{{ $materia->carrera->facultad->nombre ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $materia->semestre }}°</span></td>
                            <td><span class="badge bg-success">{{ $materia->creditos }}</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.materias.show', $materia) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.materias.edit', $materia) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" title="Acciones rápidas">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.grupos.create', ['materia_id' => $materia->id]) }}">
                                                <i class="fas fa-users"></i> Crear Grupo
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.cargas-academicas.create', ['materia_id' => $materia->id]) }}">
                                                <i class="fas fa-chalkboard-teacher"></i> Asignar Profesor
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.horarios.create', ['materia_id' => $materia->id]) }}">
                                                <i class="fas fa-calendar-alt"></i> Crear Horario
                                            </a></li>
                                        </ul>
                                    </div>
                                    <form method="POST" action="{{ route('admin.materias.destroy', $materia) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar esta materia?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay materias registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $materias->firstItem() ?? 0 }} a {{ $materias->lastItem() ?? 0 }} 
                    de {{ $materias->total() }} materias
                </div>
                <div>
                    {{ $materias->links() }}
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