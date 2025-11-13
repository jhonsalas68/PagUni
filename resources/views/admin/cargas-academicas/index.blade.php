@extends('layouts.dashboard')

@section('title', 'Gestión de Cargas Académicas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Cargas Académicas</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.cargas-academicas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Carga Académica
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros compactos -->
    <div class="mb-3">
        <form method="GET" action="{{ route('admin.cargas-academicas.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="carrera_id" class="form-select form-select-sm">
                    <option value="">Todas las carreras</option>
                    @foreach($carreras as $carrera)
                        <option value="{{ $carrera->id }}" {{ request('carrera_id') == $carrera->id ? 'selected' : '' }}>
                            {{ $carrera->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="periodo" class="form-select form-select-sm">
                    <option value="">Todos los períodos</option>
                    @foreach($periodos as $periodo)
                        <option value="{{ $periodo }}" {{ request('periodo') == $periodo ? 'selected' : '' }}>
                            {{ $periodo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="asignado" {{ request('estado') == 'asignado' ? 'selected' : '' }}>Asignado</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="profesor_id" class="form-select form-select-sm">
                    <option value="">Todos los profesores</option>
                    @foreach($profesores as $profesor)
                        <option value="{{ $profesor->id }}" {{ request('profesor_id') == $profesor->id ? 'selected' : '' }}>
                            {{ $profesor->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
            @if(request()->hasAny(['carrera_id', 'periodo', 'estado', 'profesor_id']))
            <div class="col-12">
                <a href="{{ route('admin.cargas-academicas.index') }}" class="btn btn-sm btn-link">
                    <i class="fas fa-times"></i> Limpiar filtros
                </a>
            </div>
            @endif
        </form>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Profesor</th>
                            <th>Materia / Carrera</th>
                            <th>Grupo</th>
                            <th>Período</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cargasAcademicas as $carga)
                        <tr>
                            <td>{{ $carga->profesor->nombre_completo ?? 'N/A' }}</td>
                            <td>
                                <div>{{ $carga->grupo->materia->nombre ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $carga->grupo->materia->carrera->nombre ?? 'N/A' }}</small>
                            </td>
                            <td><span class="badge bg-info">{{ $carga->grupo->identificador ?? 'N/A' }}</span></td>
                            <td><span class="badge bg-primary">{{ $carga->periodo }}</span></td>
                            <td>
                                @php
                                    $estadoColors = [
                                        'asignado' => 'success',
                                        'pendiente' => 'warning',
                                        'completado' => 'info',
                                        'cancelado' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $estadoColors[$carga->estado] ?? 'secondary' }}">
                                    {{ ucfirst($carga->estado) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.cargas-academicas.show', $carga) }}" class="btn btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.cargas-academicas.edit', $carga) }}" class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.cargas-academicas.destroy', $carga) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar esta carga académica?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay cargas académicas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($cargasAcademicas->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $cargasAcademicas->firstItem() ?? 0 }}-{{ $cargasAcademicas->lastItem() ?? 0 }} de {{ $cargasAcademicas->total() }}
                    </small>
                </div>
                <div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Botón Anterior --}}
                            @if ($cargasAcademicas->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">← Anterior</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $cargasAcademicas->appends(request()->query())->previousPageUrl() }}">← Anterior</a>
                                </li>
                            @endif

                            {{-- Indicador de página --}}
                            <li class="page-item disabled">
                                <span class="page-link">
                                    Página {{ $cargasAcademicas->currentPage() }} de {{ $cargasAcademicas->lastPage() }}
                                </span>
                            </li>

                            {{-- Botón Siguiente --}}
                            @if ($cargasAcademicas->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $cargasAcademicas->appends(request()->query())->nextPageUrl() }}">Siguiente →</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">Siguiente →</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection