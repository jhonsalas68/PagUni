@extends('layouts.dashboard')

@section('title', 'Nueva Carga Académica')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Nueva Carga Académica</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.cargas-academicas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    @if($errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Información de la Carga Académica</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cargas-academicas.store') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="profesor_id" class="form-label">Profesor <span class="text-danger">*</span></label>
                            <select class="form-select @error('profesor_id') is-invalid @enderror" 
                                    id="profesor_id" name="profesor_id" required>
                                <option value="">Seleccionar profesor...</option>
                                @foreach($profesores as $profesor)
                                    <option value="{{ $profesor->id }}" {{ old('profesor_id') == $profesor->id ? 'selected' : '' }}>
                                        {{ $profesor->nombre_completo }} - {{ $profesor->especialidad }}
                                    </option>
                                @endforeach
                            </select>
                            @error('profesor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="grupo_id" class="form-label">Grupo <span class="text-danger">*</span></label>
                            <select class="form-select @error('grupo_id') is-invalid @enderror" 
                                    id="grupo_id" name="grupo_id" required>
                                <option value="">Seleccionar grupo...</option>
                                @foreach($grupos as $grupo)
                                    <option value="{{ $grupo->id }}" {{ old('grupo_id') == $grupo->id ? 'selected' : '' }}>
                                        {{ $grupo->materia->nombre ?? 'N/A' }} - Grupo {{ $grupo->identificador }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grupo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="periodo" class="form-label">Período <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('periodo') is-invalid @enderror" 
                                   id="periodo" name="periodo" value="{{ old('periodo') }}" 
                                   placeholder="Ej: 2024-1, 2024-2" required>
                            @error('periodo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select @error('estado') is-invalid @enderror" 
                                    id="estado" name="estado" required>
                                <option value="">Seleccionar estado...</option>
                                <option value="asignado" {{ old('estado') == 'asignado' ? 'selected' : '' }}>Asignado</option>
                                <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="completado" {{ old('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                                <option value="cancelado" {{ old('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.cargas-academicas.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Carga Académica</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection