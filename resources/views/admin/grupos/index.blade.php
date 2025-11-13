@extends('layouts.dashboard')

@section('title', 'Gestión de Grupos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Grupos</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.grupos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Grupo
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Grupos</h6>
        </div>
        <div class="card-body">
            <!-- Filtros de búsqueda -->
            <form method="GET" action="{{ route('admin.grupos.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="buscar" class="form-label">Buscar por Identificador o Materia</label>
                        <input type="text" 
                               class="form-control" 
                               id="buscar" 
                               name="buscar" 
                               value="{{ request('buscar') }}" 
                               placeholder="Ej: A, Cálculo, MAT-101">
                    </div>
                    <div class="col-md-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('admin.grupos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Identificador</th>
                            <th>Materia</th>
                            <th>Carrera</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grupos as $grupo)
                        <tr>
                            <td><span class="badge bg-primary">{{ $grupo->identificador }}</span></td>
                            <td>{{ $grupo->materia->nombre ?? 'N/A' }}</td>
                            <td>{{ $grupo->materia->carrera->nombre ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $grupo->capacidad_maxima }} estudiantes</span></td>
                            <td>
                                <span class="badge bg-{{ $grupo->estado == 'activo' ? 'success' : 'danger' }}">
                                    {{ ucfirst($grupo->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.grupos.show', $grupo) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.grupos.edit', $grupo) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.grupos.destroy', $grupo) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar este grupo?')">
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
                            <td colspan="6" class="text-center">No hay grupos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $grupos->firstItem() ?? 0 }} a {{ $grupos->lastItem() ?? 0 }} 
                    de {{ $grupos->total() }} grupos
                </div>
                <div>
                    {{ $grupos->links() }}
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