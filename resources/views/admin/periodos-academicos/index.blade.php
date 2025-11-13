@extends('layouts.dashboard')

@section('title', 'Gestión de Periodos Académicos')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <h1 class="h2 mb-0">
                <i class="fas fa-calendar-alt"></i> Periodos Académicos
            </h1>
            <a href="{{ route('admin.periodos-academicos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Periodo
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ $errors->first('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 fw-bold text-primary">Filtros de Búsqueda</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.periodos-academicos.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <label for="buscar" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="buscar" name="buscar" 
                           value="{{ request('buscar') }}" placeholder="Código o nombre...">
                </div>
                <div class="col-md-3">
                    <label for="anio" class="form-label">Año</label>
                    <select class="form-select" id="anio" name="anio">
                        <option value="">Todos</option>
                        @foreach($anios as $anio)
                            <option value="{{ $anio }}" {{ request('anio') == $anio ? 'selected' : '' }}>
                                {{ $anio }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        <option value="finalizado" {{ request('estado') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de periodos -->
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 fw-bold text-primary">
            Listado de Periodos ({{ $periodos->total() }})
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Año</th>
                        <th>Semestre</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Estado</th>
                        <th>Actual</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periodos as $periodo)
                    <tr>
                        <td>
                            <strong>{{ $periodo->codigo }}</strong>
                        </td>
                        <td>{{ $periodo->nombre }}</td>
                        <td>{{ $periodo->anio }}</td>
                        <td>
                            <span class="badge bg-info">
                                {{ $periodo->semestre == 1 ? '1er' : '2do' }} Semestre
                            </span>
                        </td>
                        <td>{{ $periodo->fecha_inicio->format('d/m/Y') }}</td>
                        <td>{{ $periodo->fecha_fin->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $periodo->estado_badge }}">
                                {{ ucfirst($periodo->estado) }}
                            </span>
                        </td>
                        <td>
                            @if($periodo->es_actual)
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Actual
                                </span>
                            @else
                                <form action="{{ route('admin.periodos-academicos.marcar-actual', $periodo) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success" 
                                            title="Marcar como actual">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.periodos-academicos.edit', $periodo) }}" 
                                   class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.periodos-academicos.destroy', $periodo) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Está seguro de eliminar este periodo académico?')">
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
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No hay periodos académicos registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $periodos->links() }}
        </div>
    </div>
</div>
@endsection
