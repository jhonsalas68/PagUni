@extends('layouts.dashboard')

@section('title', 'Generador Automático de Cargas Académicas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-magic"></i> Generador Automático de Cargas Académicas
        </h1>
        <a href="{{ route('admin.cargas-academicas.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver a Cargas
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('conflictos'))
        <div class="card shadow mb-4 border-left-warning">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">Conflictos Encontrados</h6>
            </div>
            <div class="card-body">
                <p>Las siguientes asignaciones no pudieron completarse:</p>
                <ul>
                    @foreach(session('conflictos') as $conflicto)
                        <li class="text-danger">{{ $conflicto }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if(session('asignaciones'))
        <div class="card shadow mb-4 border-left-success">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Cargas Generadas Exitosamente</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Materia</th>
                                <th>Grupo</th>
                                <th>Profesor Asignado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('asignaciones') as $asignacion)
                                <tr>
                                    <td>{{ $asignacion['materia'] }}</td>
                                    <td><span class="badge bg-primary">{{ $asignacion['grupo'] }}</span></td>
                                    <td>{{ $asignacion['profesor'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Generación Automática</h6>
                </div>
                <div class="card-body">
                    <p class="mb-4">
                        Este proceso creará automáticamente <strong>Cargas Académicas</strong> para todas las materias activas del periodo seleccionado.
                    </p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>¿Qué hace este generador?</strong>
                        <ul class="mb-0 mt-2">
                            <li>Crea un grupo (Grupo A) para cada materia activa</li>
                            <li>Asigna un profesor activo a cada grupo</li>
                            <li>Evita duplicados si ya existen cargas</li>
                            <li>Distribuye profesores equitativamente</li>
                        </ul>
                    </div>
                    
                    <form action="{{ route('admin.cargas-academicas.generador.store') }}" method="POST" onsubmit="return confirm('¿Está seguro de generar las cargas automáticamente? Este proceso creará grupos y asignaciones.');">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="periodo" class="form-label font-weight-bold">Periodo Académico:</label>
                            <select name="periodo" id="periodo" class="form-select" required>
                                <option value="">-- Seleccione un Periodo --</option>
                                @foreach($periodos as $periodo)
                                    <option value="{{ $periodo }}">{{ $periodo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-cogs"></i> Generar Cargas Automáticamente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
