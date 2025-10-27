@extends('layouts.dashboard')

@section('title', 'Detalle de Estudiante')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detalle de Estudiante</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.estudiantes.edit', $estudiante) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.estudiantes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Código Estudiante:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-primary fs-6">{{ $estudiante->codigo_estudiante }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Cédula:</label>
                                <p class="form-control-plaintext">{{ $estudiante->cedula }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre Completo:</label>
                                <p class="form-control-plaintext">{{ $estudiante->nombre_completo }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email:</label>
                                <p class="form-control-plaintext">{{ $estudiante->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Teléfono:</label>
                                <p class="form-control-plaintext">{{ $estudiante->telefono ?? 'No registrado' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fecha de Nacimiento:</label>
                                <p class="form-control-plaintext">{{ $estudiante->fecha_nacimiento?->format('d/m/Y') ?? 'No registrada' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($estudiante->direccion)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dirección:</label>
                        <p class="form-control-plaintext">{{ $estudiante->direccion }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Información Académica</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Carrera:</label>
                                <p class="form-control-plaintext">{{ $estudiante->carrera->nombre ?? 'Sin carrera asignada' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Facultad:</label>
                                <p class="form-control-plaintext">{{ $estudiante->carrera->facultad->nombre ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Semestre Actual:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info fs-6">{{ $estudiante->semestre_actual }}° Semestre</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado:</label>
                                <p class="form-control-plaintext">
                                    @php
                                        $estadoColors = [
                                            'activo' => 'success',
                                            'inactivo' => 'danger',
                                            'graduado' => 'info',
                                            'retirado' => 'warning'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $estadoColors[$estudiante->estado] ?? 'secondary' }} fs-6">
                                        {{ ucfirst($estudiante->estado) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($estudiante->inscripciones->count() > 0)
            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Inscripciones</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Materia</th>
                                    <th>Código</th>
                                    <th>Semestre</th>
                                    <th>Fecha Inscripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estudiante->inscripciones as $inscripcion)
                                <tr>
                                    <td>{{ $inscripcion->materia->nombre ?? 'N/A' }}</td>
                                    <td><span class="badge bg-secondary">{{ $inscripcion->materia->codigo ?? 'N/A' }}</span></td>
                                    <td><span class="badge bg-info">{{ $inscripcion->materia->semestre ?? 'N/A' }}°</span></td>
                                    <td>{{ $inscripcion->created_at->format('d/m/Y') }}</td>
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
                        <label class="form-label fw-bold">Total de Inscripciones:</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-success fs-6">{{ $estudiante->inscripciones->count() }} materias</span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Edad:</label>
                        <p class="form-control-plaintext">
                            @if($estudiante->fecha_nacimiento)
                                {{ $estudiante->fecha_nacimiento->age }} años
                            @else
                                No registrada
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Registro:</label>
                        <p class="form-control-plaintext">{{ $estudiante->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Última Actualización:</label>
                        <p class="form-control-plaintext">{{ $estudiante->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-danger">Zona de Peligro</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Esta acción no se puede deshacer.</p>
                    <form method="POST" action="{{ route('admin.estudiantes.destroy', $estudiante) }}" 
                          onsubmit="return confirm('¿Está seguro de eliminar este estudiante? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash"></i> Eliminar Estudiante
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection