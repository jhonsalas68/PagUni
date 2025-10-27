@extends('layouts.dashboard')

@section('title', 'Editar Carrera')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Editar Carrera</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.carreras.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Información de la Carrera</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.carreras.update', $carrera) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                   id="codigo" name="codigo" value="{{ old('codigo', $carrera->codigo) }}" required>
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" name="nombre" value="{{ old('nombre', $carrera->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="facultad_id" class="form-label">Facultad <span class="text-danger">*</span></label>
                            <select class="form-select @error('facultad_id') is-invalid @enderror" 
                                    id="facultad_id" name="facultad_id" required>
                                <option value="">Seleccionar facultad...</option>
                                @foreach($facultades as $facultad)
                                    <option value="{{ $facultad->id }}" 
                                            {{ old('facultad_id', $carrera->facultad_id) == $facultad->id ? 'selected' : '' }}>
                                        {{ $facultad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('facultad_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="duracion_semestres" class="form-label">Duración (Semestres) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('duracion_semestres') is-invalid @enderror" 
                                   id="duracion_semestres" name="duracion_semestres" 
                                   value="{{ old('duracion_semestres', $carrera->duracion_semestres) }}" min="1" required>
                            @error('duracion_semestres')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $carrera->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.carreras.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Carrera</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection