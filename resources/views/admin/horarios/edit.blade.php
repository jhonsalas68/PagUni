@extends('layouts.dashboard')

@section('title', 'Editar Horario')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Editar Horario</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">
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
            <h6 class="m-0 font-weight-bold text-primary">Información del Horario</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.horarios.update', $horario) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="carga_academica_id" class="form-label">Carga Académica <span class="text-danger">*</span></label>
                            <select class="form-select @error('carga_academica_id') is-invalid @enderror" 
                                    id="carga_academica_id" name="carga_academica_id" required>
                                <option value="">Seleccionar carga académica...</option>
                                @foreach($cargasAcademicas as $carga)
                                    <option value="{{ $carga->id }}" 
                                            {{ old('carga_academica_id', $horario->carga_academica_id) == $carga->id ? 'selected' : '' }}>
                                        {{ $carga->grupo->materia->nombre ?? 'N/A' }} - 
                                        {{ $carga->profesor->nombre_completo ?? 'N/A' }} - 
                                        Grupo {{ $carga->grupo->identificador ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('carga_academica_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="aula_id" class="form-label">Aula <span class="text-danger">*</span></label>
                            <select class="form-select @error('aula_id') is-invalid @enderror" 
                                    id="aula_id" name="aula_id" required>
                                <option value="">Seleccionar aula...</option>
                                @foreach($aulas as $aula)
                                    <option value="{{ $aula->id }}" 
                                            {{ old('aula_id', $horario->aula_id) == $aula->id ? 'selected' : '' }}>
                                        {{ $aula->codigo_aula }} - {{ $aula->nombre }} ({{ $aula->capacidad }} personas)
                                    </option>
                                @endforeach
                            </select>
                            @error('aula_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="dia_semana" class="form-label">Día de la Semana <span class="text-danger">*</span></label>
                            <select class="form-select @error('dia_semana') is-invalid @enderror" 
                                    id="dia_semana" name="dia_semana" required>
                                <option value="">Seleccionar día...</option>
                                <option value="1" {{ old('dia_semana', $horario->dia_semana) == '1' ? 'selected' : '' }}>Lunes</option>
                                <option value="2" {{ old('dia_semana', $horario->dia_semana) == '2' ? 'selected' : '' }}>Martes</option>
                                <option value="3" {{ old('dia_semana', $horario->dia_semana) == '3' ? 'selected' : '' }}>Miércoles</option>
                                <option value="4" {{ old('dia_semana', $horario->dia_semana) == '4' ? 'selected' : '' }}>Jueves</option>
                                <option value="5" {{ old('dia_semana', $horario->dia_semana) == '5' ? 'selected' : '' }}>Viernes</option>
                                <option value="6" {{ old('dia_semana', $horario->dia_semana) == '6' ? 'selected' : '' }}>Sábado</option>
                                <option value="7" {{ old('dia_semana', $horario->dia_semana) == '7' ? 'selected' : '' }}>Domingo</option>
                            </select>
                            @error('dia_semana')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="hora_inicio" class="form-label">Hora de Inicio <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror" 
                                   id="hora_inicio" name="hora_inicio" 
                                   value="{{ old('hora_inicio', $horario->hora_inicio) }}" required>
                            @error('hora_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="hora_fin" class="form-label">Hora de Fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('hora_fin') is-invalid @enderror" 
                                   id="hora_fin" name="hora_fin" 
                                   value="{{ old('hora_fin', $horario->hora_fin) }}" required>
                            @error('hora_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="duracion_display" class="form-label">Duración Calculada</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="duracion_display" readonly 
                                       value="{{ $horario->duracion_horas ? number_format($horario->duracion_horas, 2) . ' horas' : '' }}"
                                       style="background-color: #f8f9fa;">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            </div>
                            <input type="hidden" name="duracion_horas" id="duracion_horas" value="{{ $horario->duracion_horas }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tipo_clase" class="form-label">Tipo de Clase <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo_clase') is-invalid @enderror" 
                                    id="tipo_clase" name="tipo_clase" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="teorica" {{ old('tipo_clase', $horario->tipo_clase) == 'teorica' ? 'selected' : '' }}>Teórica</option>
                                <option value="practica" {{ old('tipo_clase', $horario->tipo_clase) == 'practica' ? 'selected' : '' }}>Práctica</option>
                                <option value="laboratorio" {{ old('tipo_clase', $horario->tipo_clase) == 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                            </select>
                            @error('tipo_clase')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Horario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Horarios Existentes -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Otros Horarios Existentes</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-striped">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>Día</th>
                                <th>Hora</th>
                                <th>Materia</th>
                                <th>Profesor</th>
                                <th>Aula</th>
                                <th>Período</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($horariosExistentes as $horarioExistente)
                            <tr>
                                <td>
                                    @php
                                        $dias = ['', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                                    @endphp
                                    <span class="badge bg-primary">{{ $dias[$horarioExistente->dia_semana] ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <small>{{ $horarioExistente->hora_inicio }} - {{ $horarioExistente->hora_fin }}</small>
                                </td>
                                <td>
                                    <small>{{ $horarioExistente->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <small>{{ $horarioExistente->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $horarioExistente->aula->codigo_aula ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $horarioExistente->periodo_academico }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay otros horarios registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    const duracionDisplay = document.getElementById('duracion_display');
    const duracionHoras = document.getElementById('duracion_horas');

    // Función para calcular duración
    function calcularDuracion() {
        if (horaInicio.value && horaFin.value) {
            const inicio = new Date('2000-01-01 ' + horaInicio.value);
            const fin = new Date('2000-01-01 ' + horaFin.value);
            
            if (fin <= inicio) {
                duracionDisplay.value = 'Hora inválida';
                duracionDisplay.style.color = 'red';
                duracionHoras.value = '';
                return 0;
            }
            
            const diferencia = (fin - inicio) / (1000 * 60 * 60); // Diferencia en horas
            duracionDisplay.value = diferencia.toFixed(2) + ' horas';
            duracionDisplay.style.color = 'green';
            duracionHoras.value = diferencia.toFixed(2);
            
            return diferencia;
        } else {
            duracionDisplay.value = '';
            duracionHoras.value = '';
            return 0;
        }
    }

    // Event listeners
    horaInicio.addEventListener('change', calcularDuracion);
    horaFin.addEventListener('change', calcularDuracion);
});
</script>
@endsection