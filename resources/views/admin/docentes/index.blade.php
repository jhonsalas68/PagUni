@extends('layouts.dashboard')

@section('title', 'Gestión de Docentes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Docentes</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.docentes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Registrar Nuevo Docente
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.docentes.search') }}" class="row g-3">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="q" 
                           placeholder="Buscar por código o nombre del docente..." 
                           value="{{ $query ?? '' }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    @if(isset($query))
                        <a href="{{ route('admin.docentes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Docentes -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                Lista de Docentes 
                @if(isset($query))
                    - Resultados para: "{{ $query }}"
                @endif
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Especialidad</th>
                            <th>Tipo Contrato</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($docentes as $docente)
                        <tr>
                            <td>
                                <span class="badge bg-primary">{{ $docente->codigo_docente }}</span>
                            </td>
                            <td>{{ $docente->nombre_completo }}</td>
                            <td>{{ $docente->email }}</td>
                            <td>{{ $docente->especialidad }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst(str_replace('_', ' ', $docente->tipo_contrato)) }}
                                </span>
                            </td>
                            <td>
                                @if($docente->estado == 'activo')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.docentes.edit', $docente) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    
                                    @if($docente->estado == 'activo')
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmarDesactivacion({{ $docente->id }}, '{{ $docente->nombre_completo }}')">
                                            <i class="fas fa-ban"></i> Desactivar
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('admin.docentes.activate', $docente) }}" 
                                              style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Activar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                @if(isset($query))
                                    No se encontraron docentes que coincidan con la búsqueda.
                                @else
                                    No hay docentes registrados.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Desactivación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea desactivar al docente <strong id="docenteNombre"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Advertencia:</strong> La cuenta de acceso será inhabilitada automáticamente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="desactivarForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i> Desactivar Docente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmarDesactivacion(docenteId, docenteNombre) {
    document.getElementById('docenteNombre').textContent = docenteNombre;
    document.getElementById('desactivarForm').action = `/admin/docentes/${docenteId}`;
    
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
}
</script>
@endsection