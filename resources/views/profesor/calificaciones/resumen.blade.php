@extends('layouts.dashboard')

@section('title', 'Resumen de Calificaciones')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-table"></i> Resumen: {{ $grupo->materia->nombre }} (Grupo {{ $grupo->identificador }})
        </h1>
        <a href="{{ route('profesor.calificaciones.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver a Grupos
        </a>
    </div>

    <!-- Tabla de Resumen -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Sábana de Notas</h6>
            <a href="{{ route('profesor.calificaciones.gestion', $grupo->id) }}" class="btn btn-light btn-sm text-primary font-weight-bold">
                <i class="fas fa-edit"></i> Editar Notas
            </a>
            <a href="{{ route('profesor.calificaciones.resumen.pdf', $grupo->id) }}" class="btn btn-light btn-sm text-danger font-weight-bold ml-2">
                <i class="fas fa-file-pdf"></i> Descargar Reporte PDF
            </a>
        </div>
        <div class="card-body">
            @if(empty($datos))
                <div class="alert alert-info text-center">
                    No hay estudiantes inscritos en este grupo o no están activos.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th rowspan="2" class="align-middle">N°</th>
                                <th rowspan="2" class="align-middle text-start">Estudiante</th>
                                <th rowspan="2" class="align-middle">Código</th>
                                <th colspan="{{ $tiposEvaluacion->count() }}">Evaluaciones</th>
                                <th rowspan="2" class="align-middle bg-warning text-dark">Promedio</th>
                                <th rowspan="2" class="align-middle">Estado</th>
                            </tr>
                            <tr>
                                @foreach($tiposEvaluacion as $tipo)
                                    <th>
                                        <small class="d-block font-weight-bold">{{ $tipo->nombre }}</small>
                                        <span class="badge bg-info text-white">{{ $tipo->ponderacion }}%</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datos as $index => $dato)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $dato['estudiante']->apellido }}</strong>, {{ $dato['estudiante']->nombre }}
                                    </td>
                                    <td class="text-center text-muted small">{{ $dato['codigo'] }}</td>
                                    
                                    @foreach($tiposEvaluacion as $tipo)
                                        <td class="text-center">
                                            @if($dato['notas'][$tipo->id] !== '-')
                                                {{ $dato['notas'][$tipo->id] }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    
                                    <td class="text-center font-weight-bold {{ $dato['promedio'] >= 51 ? 'text-success' : 'text-danger' }}">
                                        {{ $dato['promedio'] }}
                                    </td>
                                    <td class="text-center">
                                        @if($dato['estado'] == 'Aprobado')
                                            <span class="badge bg-success">Aprobado</span>
                                        @else
                                            <span class="badge bg-danger">Reprobado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
