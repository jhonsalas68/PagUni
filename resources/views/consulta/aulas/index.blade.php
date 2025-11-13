@extends('layouts.dashboard')

@section('title', 'Consulta de Aulas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-door-open"></i> Consulta de Horarios de Aulas
        </h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Selecciona un aula</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Selecciona un aula para consultar su horario de ocupación y disponibilidad.
                    </p>

                    <div class="row">
                        @foreach($aulas as $aula)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border-left-info h-100">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            {{ $aula->codigo_aula }}
                                        </h6>
                                        
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-building"></i> {{ $aula->nombre }}
                                            </small>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-users"></i> Capacidad: {{ $aula->capacidad }} personas
                                            </small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($aula->tipo) }}
                                            </span>
                                        </div>

                                        <div class="d-grid">
                                            <a href="{{ route('consulta.aulas.horario', $aula->id) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-calendar-alt"></i> Ver Horario
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($aulas->count() === 0)
                        <div class="text-center py-5">
                            <i class="fas fa-door-closed fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay aulas registradas</h4>
                            <p class="text-muted">Contacta al administrador para registrar aulas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection