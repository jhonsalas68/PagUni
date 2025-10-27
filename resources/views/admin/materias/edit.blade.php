@extends('layouts.dashboard')

@section('title', 'Editar Materia')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Editar Materia</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.materias.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Información de la Materia</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.materias.update', $materia) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                   id="codigo" name="codigo" value="{{ old('codigo', $materia->codigo) }}" required>
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" name="nombre" value="{{ old('nombre', $materia->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="carrera_id" class="form-label">Carrera <span class="text-danger">*</span></label>
                            <select class="form-select @error('carrera_id') is-invalid @enderror" 
                                    id="carrera_id" name="carrera_id" required>
                                <option value="">Seleccionar carrera...</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{ $carrera->id }}" 
                                            {{ old('carrera_id', $materia->carrera_id) == $carrera->id ? 'selected' : '' }}>
                                        {{ $carrera->nombre }} - {{ $carrera->facultad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('carrera_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="semestre" class="form-label">Semestre <span class="text-danger">*</span></label>
                            <select class="form-select @error('semestre') is-invalid @enderror" 
                                    id="semestre" name="semestre" required>
                                <option value="">Seleccionar...</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" 
                                            {{ old('semestre', $materia->semestre) == $i ? 'selected' : '' }}>
                                        {{ $i }}° Semestre
                                    </option>
                                @endfor
                            </select>
                            @error('semestre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="creditos" class="form-label">Créditos <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('creditos') is-invalid @enderror" 
                                   id="creditos" name="creditos" value="{{ old('creditos', $materia->creditos) }}" min="1" required>
                            @error('creditos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="horas_teoricas" class="form-label">Horas Teóricas <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('horas_teoricas') is-invalid @enderror" 
                                   id="horas_teoricas" name="horas_teoricas" 
                                   value="{{ old('horas_teoricas', $materia->horas_teoricas ?? 0) }}" min="0" required>
                            @error('horas_teoricas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="horas_practicas" class="form-label">Horas Prácticas <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('horas_practicas') is-invalid @enderror" 
                                   id="horas_practicas" name="horas_practicas" 
                                   value="{{ old('horas_practicas', $materia->horas_practicas ?? 0) }}" min="0" required>
                            @error('horas_practicas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                              id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $materia->descripcion) }}</textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.materias.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Materia</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection