@extends('layouts.dashboard')

@section('title', 'Mis Calificaciones')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-star"></i> Mis Calificaciones
        </h1>
    </div>

    <div class="row">
        @foreach($inscripciones as $inscripcion)
            <div class="col-xl-6 col-md-12 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">{{ $inscripcion->grupo->materia->nombre }}</h6>
                        <span class="badge bg-light text-primary">Grupo {{ $inscripcion->grupo->identificador }}</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Evaluación</th>
                                        <th>Ponderación</th>
                                        <th>Nota</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inscripcion->calificaciones as $calificacion)
                                        <tr>
                                            <td>{{ $calificacion->tipoEvaluacion->nombre }}</td>
                                            <td>{{ $calificacion->tipoEvaluacion->ponderacion }}%</td>
                                            <td class="font-weight-bold {{ $calificacion->nota >= 11 ? 'text-success' : 'text-danger' }}">
                                                {{ $calificacion->nota }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($calificacion->fecha)->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No hay calificaciones registradas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">Promedio Ponderado:</td>
                                        <td colspan="2" class="fw-bold fs-5 {{ $inscripcion->promedio >= 11 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($inscripcion->promedio, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        @if($inscripciones->isEmpty())
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> No estás inscrito en ninguna materia activa.
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
