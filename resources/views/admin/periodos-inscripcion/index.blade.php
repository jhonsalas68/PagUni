@extends('layouts.dashboard')

@section('title', 'Periodos de Inscripción')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>📅 Periodos de Inscripción</h2>
                <a href="{{ route('admin.periodos-inscripcion.create') }}" class="btn btn-primary">
                    ➕ Nuevo Periodo
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Periodo Académico</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periodos as $periodo)
                            <tr>
                                <td>{{ $periodo->nombre }}</td>
                                <td>{{ $periodo->periodo_academico }}</td>
                                <td>{{ $periodo->fecha_inicio->format('d/m/Y H:i') }}</td>
                                <td>{{ $periodo->fecha_fin->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($periodo->activo)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.periodos-inscripcion.edit', $periodo->id) }}" 
                                           class="btn btn-sm btn-warning">
                                            ✏️ Editar
                                        </a>
                                        
                                        @if($periodo->activo)
                                            <form action="{{ route('admin.periodos-inscripcion.desactivar', $periodo->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-secondary">
                                                    ⏸️ Desactivar
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.periodos-inscripcion.activar', $periodo->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    ▶️ Activar
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.periodos-inscripcion.destroy', $periodo->id) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Eliminar este periodo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay periodos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($periodos->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">{{ $periodos->firstItem() ?? 0 }}-{{ $periodos->lastItem() ?? 0 }} de {{ $periodos->total() }}</small>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        @if ($periodos->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">‹ Anterior</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $periodos->previousPageUrl() }}">‹ Anterior</a></li>
                        @endif
                        <li class="page-item disabled"><span class="page-link">Pág. {{ $periodos->currentPage() }} de {{ $periodos->lastPage() }}</span></li>
                        @if ($periodos->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $periodos->nextPageUrl() }}">Siguiente ›</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Siguiente ›</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
