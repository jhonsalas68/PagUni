@extends('layouts.dashboard')

@section('title', 'Nuevo Grupo')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Nuevo Grupo</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.grupos.index') }}" class="btn btn-secondary">
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
            <h6 class="m-0 font-weight-bold text-primary">Información del Grupo</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.grupos.store') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="identificador" class="form-label">Identificador <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('identificador') is-invalid @enderror" 
                                   id="identificador" name="identificador" value="{{ old('identificador') }}" 
                                   placeholder="Ej: A, B, 01, 02" required>
                            @error('identificador')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="materia_id" class="form-label">Materia <span class="text-danger">*</span></label>
                            <select class="form-select @error('materia_id') is-invalid @enderror" 
                                    id="materia_id" name="materia_id" required>
                                <option value="">Seleccionar materia...</option>
                                @foreach($materias as $materia)
                                    <option value="{{ $materia->id }}" 
                                        {{ (old('materia_id', $materiaSeleccionada ?? '') == $materia->id) ? 'selected' : '' }}>
                                        {{ $materia->codigo }} - {{ $materia->nombre }} ({{ $materia->carrera->nombre ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('materia_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="capacidad_maxima" class="form-label">Capacidad Máxima <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('capacidad_maxima') is-invalid @enderror" 
                                   id="capacidad_maxima" name="capacidad_maxima" value="{{ old('capacidad_maxima') }}" 
                                   min="1" required>
                            @error('capacidad_maxima')
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
                                <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.grupos.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Grupo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection