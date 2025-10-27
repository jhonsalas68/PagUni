@extends('layouts.dashboard')

@section('title', 'Detalle de Carrera')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalle de Carrera</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.carreras.edit', $carrera) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.carreras.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Información General</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Código:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-primary fs-6">{{ $carrera->codigo }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre:</label>
                                <p class="form-control-plaintext">{{ $carrera->nombre }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Facultad:</label>
                                <p class="form-control-plaintext">{{ $carrera->facultad->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Duración:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info fs-6">{{ $carrera->duracion_semestres }} semestres</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($carrera->descripcion)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción:</label>
                        <p class="form-control-plaintext">{{ $carrera->descripcion }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($carrera->materias->count() > 0)
            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Materias de la Carrera</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Semestre</th>
                                    <th>Créditos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carrera->materias->sortBy('semestre') as $materia)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $materia->codigo }}</span></td>
                                    <td>{{ $materia->nombre }}</td>
                                    <td><span class="badge bg-info">{{ $materia->semestre }}°</span></td>
                                    <td><span class="badge bg-success">{{ $materia->creditos }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Total de Materias:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-success fs-6">{{ $carrera->materias->count() }} materias</span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Total de Créditos:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-info fs-6">{{ $carrera->materias->sum('creditos') }} créditos</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Registro:</label>
                        <p class="form-control-plaintext">{{ $carrera->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Última Actualización:</label>
                        <p class="form-control-plaintext">{{ $carrera->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-danger">Zona de Peligro</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                    <form method="POST" action="{{ route('admin.carreras.destroy', $carrera) }}" 
                          onsubmit="return confirm('¿Está seguro de eliminar esta carrera? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash"></i> Eliminar Carrera
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection