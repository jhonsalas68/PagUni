@extends('layouts.dashboard')

@section('title', 'Detalle de Materia')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalle de Materia</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.materias.edit', $materia) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.materias.index') }}" class="btn btn-secondary">
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
                                    <span class="badge bg-primary fs-6">{{ $materia->codigo }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre:</label>
                                <p class="form-control-plaintext">{{ $materia->nombre }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Carrera:</label>
                                <p class="form-control-plaintext">{{ $materia->carrera->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Facultad:</label>
                                <p class="form-control-plaintext">{{ $materia->carrera->facultad->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Semestre:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info fs-6">{{ $materia->semestre }}° Semestre</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Créditos:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-success fs-6">{{ $materia->creditos }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Total Horas:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-warning text-dark fs-6">
                                        {{ ($materia->horas_teoricas ?? 0) + ($materia->horas_practicas ?? 0) }} hrs
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Horas Teóricas:</label>
                                <p class="form-control-plaintext">{{ $materia->horas_teoricas ?? 0 }} horas</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Horas Prácticas:</label>
                                <p class="form-control-plaintext">{{ $materia->horas_practicas ?? 0 }} horas</p>
                            </div>
                        </div>
                    </div>

                    @if($materia->descripcion)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción:</label>
                        <p class="form-control-plaintext">{{ $materia->descripcion }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Grupos:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-info fs-6">{{ $materia->grupos->count() }} grupos</span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Inscripciones:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-success fs-6">{{ $materia->inscripciones->count() }} estudiantes</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Registro:</label>
                        <p class="form-control-plaintext">{{ $materia->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Última Actualización:</label>
                        <p class="form-control-plaintext">{{ $materia->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-danger">Zona de Peligro</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                    <form method="POST" action="{{ route('admin.materias.destroy', $materia) }}" 
                          onsubmit="return confirm('¿Está seguro de eliminar esta materia? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash"></i> Eliminar Materia
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection