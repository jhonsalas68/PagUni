@extends('layouts.dashboard')

@section('title', 'Gestión de Aulas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Aulas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.aulas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Aula
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Aulas</h6>
        </div>
        <div class="card-body">
            <!-- Filtros de búsqueda -->
            <form method="GET" action="{{ route('admin.aulas.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="buscar" class="form-label">Buscar por Código, Nombre o Edificio</label>
                        <input type="text" 
                               class="form-control" 
                               id="buscar" 
                               name="buscar" 
                               value="{{ request('buscar') }}" 
                               placeholder="Ej: A-101, Aula Magna, Edificio A">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo_aula" class="form-label">Tipo de Aula</label>
                        <select class="form-select" id="tipo_aula" name="tipo_aula">
                            <option value="">Todos los tipos</option>
                            <option value="aula" {{ request('tipo_aula') == 'aula' ? 'selected' : '' }}>Aula</option>
                            <option value="laboratorio" {{ request('tipo_aula') == 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                            <option value="auditorio" {{ request('tipo_aula') == 'auditorio' ? 'selected' : '' }}>Auditorio</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="">Todos</option>
                            <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="ocupada" {{ request('estado') == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                            <option value="mantenimiento" {{ request('estado') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="fuera_servicio" {{ request('estado') == 'fuera_servicio' ? 'selected' : '' }}>Fuera de Servicio</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('admin.aulas.index') }}" class="btn btn-secondary">
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
                            <th>Tipo</th>
                            <th>Edificio</th>
                            <th>Piso</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aulas as $aula)
                        <tr>
                            <td><span class="badge bg-info">{{ $aula->codigo_aula }}</span></td>
                            <td>{{ $aula->nombre }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $aula->tipo_aula)) }}
                                </span>
                            </td>
                            <td>{{ $aula->edificio }}</td>
                            <td>{{ $aula->piso }}°</td>
                            <td>{{ $aula->capacidad }} personas</td>
                            <td>
                                @php
                                    $estadoClass = match($aula->estado) {
                                        'disponible' => 'success',
                                        'ocupada' => 'warning',
                                        'mantenimiento' => 'info',
                                        'fuera_servicio' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $estadoClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $aula->estado)) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.aulas.edit', $aula) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.aulas.destroy', $aula) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar esta aula?')">
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
                        <td colspan="8" class="text-center">No hay aulas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $aulas->firstItem() ?? 0 }} a {{ $aulas->lastItem() ?? 0 }} 
                    de {{ $aulas->total() }} aulas
                </div>
                <div>
                    {{ $aulas->links() }}
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