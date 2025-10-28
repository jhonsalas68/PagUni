@extends('layouts.dashboard')

@section('title', 'Editar Horario')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-edit"></i> Modificar/Reasignar Horario
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-info" id="btn-sugerir-alternativas">
                    <i class="fas fa-lightbulb"></i> Sugerir Alternativas
                </button>
            </div>
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

    <!-- CU-12: Contexto Completo de la Carga Académica -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> CU-12: Modificación de Carga Académica Multiregistro</h6>
                    <span class="badge bg-light text-dark">
                        {{ $horariosMateria->count() }} registros
                    </span>
                </div>
                <div class="card-body">
                    <!-- Información de la Carga Académica -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>📚 Materia:</strong><br>
                            <span class="badge bg-info fs-6">{{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>👨‍🏫 Profesor:</strong><br>
                            <span class="badge bg-success fs-6">{{ $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>👥 Grupo:</strong><br>
                            <span class="badge bg-secondary fs-6">{{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Patrón Completo de la Materia -->
                    <div class="alert alert-light border-info">
                        <div class="row">
                            <div class="col-md-8">
                                <strong>🗓️ Patrón Completo de Horarios:</strong>
                                <h5 class="text-primary mb-0 mt-1">
                                    @php
                                        $horariosMateria = \App\Models\Horario::where('carga_academica_id', $horario->carga_academica_id)
                                            ->with(['aula'])
                                            ->orderBy('dia_semana')
                                            ->get();
                                        
                                        $patronCompleto = [];
                                        foreach($horariosMateria as $h) {
                                            $dias = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];
                                            $diaTexto = $dias[$h->dia_semana] ?? 'N/A';
                                            $aulaTexto = $h->aula->codigo_aula ?? 'N/A';
                                            if($h->aula->tipo_aula === 'laboratorio' || stripos($h->aula->codigo_aula, 'lab') !== false) {
                                                $aulaTexto = 'Lab ' . str_replace(['Lab', 'LAB', 'Laboratorio'], '', $aulaTexto);
                                            }
                                            $patronCompleto[] = $diaTexto . ' ' . $aulaTexto;
                                        }
                                        $patronTexto = implode(' - ', $patronCompleto);
                                    @endphp
                                    {{ $patronTexto }}
                                </h5>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted">Horas semanales:</small><br>
                                <span class="badge bg-warning text-dark">{{ $horariosMateria->sum('duracion_horas') }}h</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Registros Múltiples -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th width="15%">Registro</th>
                                    <th width="15%">Día</th>
                                    <th width="20%">Horario</th>
                                    <th width="25%">Aula/Laboratorio</th>
                                    <th width="15%">Tipo</th>
                                    <th width="10%">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($horariosMateria as $index => $h)
                                <tr class="{{ $h->id === $horario->id ? 'table-warning' : '' }}">
                                    <td>
                                        <strong>Registro {{ $index + 1 }}</strong>
                                        @if($h->id === $horario->id)
                                            <br><span class="badge bg-warning text-dark">Editando</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                                        @endphp
                                        <span class="badge bg-primary">{{ $dias[$h->dia_semana] ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $h->hora_inicio }} - {{ $h->hora_fin }}</strong>
                                        <br><small class="text-success">{{ number_format($h->duracion_horas, 1) }}h</small>
                                    </td>
                                    <td>
                                        <strong>{{ $h->aula->codigo_aula ?? 'N/A' }}</strong>
                                        @if($h->aula->tipo_aula === 'laboratorio')
                                            <span class="badge bg-success ms-1">Lab</span>
                                        @endif
                                        <br><small class="text-muted">{{ $h->aula->nombre ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($h->tipo_clase) }}</span>
                                    </td>
                                    <td>
                                        @if($h->id === $horario->id)
                                            <i class="fas fa-edit text-warning" title="Editando este registro"></i>
                                        @else
                                            <a href="{{ route('admin.horarios.edit', $h) }}" class="btn btn-sm btn-outline-primary" title="Editar este registro">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Modificación Componente por Componente -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-cogs"></i> Modificación Componente por Componente - Registro Actual</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>🎯 Registro a Modificar:</strong><br>
                            <span class="badge bg-info fs-6">{{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Profesor:</strong><br>
                            <span class="badge bg-success fs-6">{{ $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Horario Actual:</strong><br>
                            @php
                                $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                            @endphp
                            <span class="badge bg-warning text-dark fs-6">{{ $dias[$horario->dia_semana] ?? 'N/A' }} {{ $horario->hora_inicio }}-{{ $horario->hora_fin }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Aula Actual:</strong><br>
                            <span class="badge bg-secondary fs-6">{{ $horario->aula->codigo_aula ?? 'N/A' }} - {{ $horario->aula->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-cogs"></i> Modificar Asignación de Recursos
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.horarios.update', $horario) }}" id="editarHorarioForm">
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
                            <label for="aula_id" class="form-label">
                                Aula <span class="text-danger">*</span>
                                <span id="indicador-disponibilidad-aula" class="badge ms-2" style="display: none;"></span>
                            </label>
                            <select class="form-select @error('aula_id') is-invalid @enderror" 
                                    id="aula_id" name="aula_id" required>
                                <option value="">Seleccionar aula...</option>
                                @foreach($aulas as $aula)
                                    <option value="{{ $aula->id }}" 
                                            data-capacidad="{{ $aula->capacidad }}"
                                            data-tipo="{{ $aula->tipo_aula ?? 'general' }}"
                                            {{ old('aula_id', $horario->aula_id) == $aula->id ? 'selected' : '' }}>
                                        {{ $aula->codigo_aula }} - {{ $aula->nombre }} ({{ $aula->capacidad }} personas)
                                        @if($aula->tipo_aula) - {{ ucfirst($aula->tipo_aula) }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('aula_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Se validará disponibilidad automáticamente
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Configuración avanzada por día -->
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="usar_configuracion_por_dia" 
                                       name="usar_configuracion_por_dia" value="1"
                                       {{ old('usar_configuracion_por_dia', $horario->usar_configuracion_por_dia) ? 'checked' : '' }}>
                                <label class="form-check-label" for="usar_configuracion_por_dia">
                                    <strong>Configuración Avanzada por Día</strong>
                                </label>
                                <small class="text-muted d-block">Permite asignar aulas y tipos de clase diferentes para cada día</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración simple (día único) -->
                <div class="row" id="configuracion-simple">
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
                    
                    <!-- Información de carga horaria -->
                    <div class="col-md-9">
                        <div class="mb-3">
                            <label class="form-label">Información de Carga Horaria</label>
                            <div class="card border-info">
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <small class="text-muted">Horas Semanales:</small>
                                            <div id="horas-semanales-info" class="fw-bold text-primary">
                                                {{ $horario->cargaAcademica->grupo->materia->horas_semanales ?? 'N/A' }} hrs
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Días Actuales:</small>
                                            <div id="dias-actuales-info" class="fw-bold text-success">1 día</div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Horas por Día:</small>
                                            <div id="horas-por-dia-info" class="fw-bold text-warning">
                                                {{ $horario->duracion_horas ?? 'N/A' }} hrs
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Distribución:</small>
                                            <div id="distribucion-info" class="fw-bold text-info">Manual</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                    <div class="col-md-4">
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
                            <small class="text-muted">Tipo por defecto (se puede personalizar por día)</small>
                        </div>
                    </div>
                </div>

                <!-- Configuración múltiple por día -->
                <div class="row" id="configuracion-multiple" style="display: none;">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Días de la Semana <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="1" id="dia1_multi" name="dias_semana[]">
                                        <label class="form-check-label" for="dia1_multi">Lunes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="2" id="dia2_multi" name="dias_semana[]">
                                        <label class="form-check-label" for="dia2_multi">Martes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="3" id="dia3_multi" name="dias_semana[]">
                                        <label class="form-check-label" for="dia3_multi">Miércoles</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="4" id="dia4_multi" name="dias_semana[]">
                                        <label class="form-check-label" for="dia4_multi">Jueves</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="5" id="dia5_multi" name="dias_semana[]">
                                        <label class="form-check-label" for="dia5_multi">Viernes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="6" id="dia6_multi" name="dias_semana[]">
                                        <label class="form-check-label" for="dia6_multi">Sábado</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración específica por día -->
                <div class="row" id="configuracion-por-dia" style="display: none;">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-cogs"></i> Configuración Específica por Día</h6>
                            </div>
                            <div class="card-body">
                                <div id="config-dias-container">
                                    <!-- Se generará dinámicamente con JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="periodo_academico" class="form-label">Período Académico</label>
                            <input type="text" class="form-control @error('periodo_academico') is-invalid @enderror" 
                                   id="periodo_academico" name="periodo_academico" 
                                   value="{{ old('periodo_academico', $horario->periodo_academico) }}" 
                                   placeholder="Ej: 2024-2">
                            @error('periodo_academico')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Estado del Horario</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="es_semestral" name="es_semestral" 
                                       value="1" {{ old('es_semestral', $horario->es_semestral) ? 'checked' : '' }}>
                                <label class="form-check-label" for="es_semestral">
                                    Horario Semestral
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CU-12: Botones de Validación y Guardado -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                    
                    <!-- Paso 1: Verificar Cambios (CU-11 Validación) -->
                    <button type="button" class="btn btn-warning me-md-2" id="btn-verificar-cambios" onclick="verificarCambiosCU12()">
                        <i class="fas fa-search"></i> Verificar Cambios
                    </button>
                    
                    <!-- Paso 2: Guardar (Solo habilitado después de verificación exitosa) -->
                    <button type="submit" class="btn btn-success" id="btn-guardar-cambios" disabled>
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>

                <!-- Panel de Validación CU-12 -->
                <div class="row mt-3" id="panel-validacion-cu12" style="display: none;">
                    <div class="col-12">
                        <div class="card" id="card-validacion">
                            <div class="card-header" id="header-validacion">
                                <h6 class="mb-0" id="titulo-validacion">
                                    <i class="fas fa-spinner fa-spin"></i> Validando Cambios...
                                </h6>
                            </div>
                            <div class="card-body" id="body-validacion">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Validando...</span>
                                    </div>
                                    <p class="mt-2">Ejecutando CU-11: Validación de Cruces...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Panel de Sugerencias Inteligentes -->
    <div class="row mt-4" id="panel-sugerencias" style="display: none;">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb"></i> Sugerencias Inteligentes de Reasignación
                    </h6>
                </div>
                <div class="card-body">
                    <div id="sugerencias-container">
                        <div class="text-center">
                            <div class="spinner-border text-info" role="status">
                                <span class="visually-hidden">Cargando sugerencias...</span>
                            </div>
                            <p class="mt-2">Analizando alternativas disponibles...</p>
                        </div>
                    </div>
                </div>
            </div>
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
    console.log('🚀 DOM cargado, inicializando elementos...');
    
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    const duracionDisplay = document.getElementById('duracion_display');
    const duracionHoras = document.getElementById('duracion_horas');
    const aulaSelect = document.getElementById('aula_id');
    const diaSelect = document.getElementById('dia_semana');
    const btnSugerir = document.getElementById('btn-sugerir-alternativas');

    const panelSugerencias = document.getElementById('panel-sugerencias');
    const panelReasignacion = document.getElementById('panel-reasignacion');
    const usarConfigPorDia = document.getElementById('usar_configuracion_por_dia');
    const configSimple = document.getElementById('configuracion-simple');
    const configMultiple = document.getElementById('configuracion-multiple');
    const configPorDiaDiv = document.getElementById('configuracion-por-dia');
    const configDiasContainer = document.getElementById('config-dias-container');
    const diasCheckboxes = document.querySelectorAll('.dia-checkbox');
    
    // Debug de elementos críticos
    console.log('🔍 Verificando elementos críticos:');
    console.log('- btnSugerir:', btnSugerir ? '✅' : '❌');
    console.log('- panelSugerencias:', panelSugerencias ? '✅' : '❌');
    console.log('- horaInicio:', horaInicio ? '✅' : '❌');
    console.log('- aulaSelect:', aulaSelect ? '✅' : '❌');
    


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

    // Función para validar disponibilidad en tiempo real
    async function validarDisponibilidad() {
        if (!aulaSelect.value || !diaSelect.value || !horaInicio.value || !horaFin.value) {
            return;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('Token CSRF no encontrado');
                return;
            }

            const response = await fetch('{{ route("admin.horarios.validar-disponibilidad") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify({
                    carga_academica_id: document.getElementById('carga_academica_id').value,
                    aula_id: aulaSelect.value,
                    dia_semana: diaSelect.value,
                    hora_inicio: horaInicio.value,
                    hora_fin: horaFin.value,
                    periodo_academico: document.getElementById('periodo_academico').value || '{{ $horario->periodo_academico }}',
                    excluir_id: {{ $horario->id }}
                })
            });

            const data = await response.json();
            const indicador = document.getElementById('indicador-disponibilidad-aula');
            
            if (data.disponible) {
                indicador.className = 'badge bg-success ms-2';
                indicador.textContent = 'Disponible';
                indicador.style.display = 'inline';
            } else {
                indicador.className = 'badge bg-danger ms-2';
                indicador.textContent = 'Conflicto';
                indicador.style.display = 'inline';
            }
        } catch (error) {
            console.error('Error validando disponibilidad:', error);
        }
    }

    // Función para mostrar sugerencias inteligentes
    window.mostrarSugerencias = async function() {
        console.log('🚀 Iniciando mostrarSugerencias...');
        
        try {
            // Buscar elementos dinámicamente
            const panelSugerencias = document.getElementById('panel-sugerencias');
            const sugerenciasContainer = document.getElementById('sugerencias-container');
            
            if (!panelSugerencias) {
                console.error('❌ Panel de sugerencias no encontrado');
                alert('Error: Panel de sugerencias no encontrado. Verifica que el HTML esté completo.');
                return;
            }

            if (!sugerenciasContainer) {
                console.error('❌ Container de sugerencias no encontrado');
                alert('Error: Container de sugerencias no encontrado. Verifica que el HTML esté completo.');
                return;
            }

            console.log('✅ Elementos encontrados, mostrando panel...');
            panelSugerencias.style.display = 'block';

            // Mostrar loading
            sugerenciasContainer.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Cargando sugerencias...</span>
                    </div>
                    <p class="mt-2">Analizando alternativas disponibles...</p>
                </div>
            `;

            console.log('🌐 Preparando petición...');
            const url = `{{ route("admin.horarios.sugerencias-get", $horario) }}`;
            console.log('🔗 URL:', url);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            console.log('📡 Respuesta:', response.status, response.statusText);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Error del servidor:', errorText);
                throw new Error(`Error del servidor: ${response.status} - ${response.statusText}`);
            }

            const data = await response.json();
            console.log('✅ Datos recibidos:', data);
            
            if (data.success) {
                console.log('🎯 Procesando sugerencias...');
                window.ultimaRespuestaSugerencias = data;
                mostrarSugerenciasHTML(data.sugerencias);
                console.log('✨ Sugerencias mostradas correctamente');
            } else {
                console.error('❌ Error en respuesta:', data.error);
                sugerenciasContainer.innerHTML = 
                    `<div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> Error del servidor</h6>
                        <p>${data.error || 'Error desconocido'}</p>
                        <button class="btn btn-sm btn-primary mt-2" onclick="testRutaSimple()">
                            <i class="fas fa-flask"></i> Probar Conexión
                        </button>
                    </div>`;
            }
        } catch (error) {
            console.error('💥 Error completo:', error);
            const sugerenciasContainer = document.getElementById('sugerencias-container');
            if (sugerenciasContainer) {
                sugerenciasContainer.innerHTML = 
                    `<div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Error de conexión</h6>
                        <p><strong>Detalles:</strong> ${error.message}</p>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                                <i class="fas fa-refresh"></i> Recargar Página
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="testRutaSimple()">
                                <i class="fas fa-flask"></i> Test Conexión
                            </button>
                        </div>
                    </div>`;
            } else {
                alert('Error crítico: No se puede mostrar el error. Recarga la página.');
            }
        }
    }

    function mostrarSugerenciasHTML(sugerencias) {
        let html = '';
        let tieneSugerencias = false;

        // Mostrar contexto completo de la materia primero
        html += mostrarContextoCompleto(window.ultimaRespuestaSugerencias);
        
        // Crear pestañas para organizar mejor las sugerencias
        html += `
            <ul class="nav nav-tabs" id="sugerenciasTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="aulas-tab" data-bs-toggle="tab" data-bs-target="#aulas" type="button" role="tab">
                        <i class="fas fa-door-open"></i> Aulas (${sugerencias.aulas?.length || 0})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="horarios-tab" data-bs-toggle="tab" data-bs-target="#horarios" type="button" role="tab">
                        <i class="fas fa-clock"></i> Horarios (${sugerencias.horarios?.length || 0})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="distribuciones-tab" data-bs-toggle="tab" data-bs-target="#distribuciones" type="button" role="tab">
                        <i class="fas fa-calendar-alt"></i> Distribuciones (${sugerencias.distribuciones?.length || 0})
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="sugerenciasTabContent">
        `;

        // Pestaña de Aulas
        html += '<div class="tab-pane fade show active" id="aulas" role="tabpanel">';
        if (sugerencias.aulas && sugerencias.aulas.length > 0) {
            tieneSugerencias = true;
            html += '<div class="row mt-3">';
            
            sugerencias.aulas.forEach(aula => {
                const badgeClass = aula.compatibilidad >= 80 ? 'bg-success' : 
                                 aula.compatibilidad >= 60 ? 'bg-warning text-dark' : 'bg-secondary';
                
                // Iconos según el tipo de sugerencia
                let categoriaIcon, tipoTexto, borderClass;
                switch (aula.tipo_sugerencia) {
                    case 'intercambio':
                        categoriaIcon = '🔄';
                        tipoTexto = 'Intercambio';
                        borderClass = 'border-warning';
                        break;
                    case 'cambio_tipo':
                        categoriaIcon = '🎯';
                        tipoTexto = 'Cambio de tipo';
                        borderClass = 'border-info';
                        break;
                    case 'simple':
                    default:
                        categoriaIcon = aula.categoria === 'utilizada' ? '🔄' : 
                                       aula.categoria === 'nueva' ? '🆕' : '✅';
                        tipoTexto = 'Cambio simple';
                        borderClass = aula.categoria === 'utilizada' ? 'border-primary' : '';
                        break;
                }
                
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card ${borderClass}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">
                                            ${categoriaIcon} <strong>${aula.codigo || 'N/A'}</strong>
                                            <span class="badge bg-secondary ms-1">${tipoTexto}</span>
                                        </h6>
                                        <p class="card-text mb-2">${aula.nombre || 'Sin nombre'}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-users"></i> ${aula.capacidad || 0} personas | 
                                            <i class="fas fa-tag"></i> ${aula.tipo || 'general'}
                                        </small>
                                        <div class="mt-2">
                                            <small class="text-primary"><strong>${aula.descripcion || 'Cambio de aula'}</strong></small><br>
                                            <small class="text-info"><i class="fas fa-info-circle"></i> ${aula.razon || 'Disponible'}</small><br>
                                            <small class="text-muted">Impacto: ${aula.impacto || 'Mínimo'}</small><br>
                                            ${mostrarImpactoCompleto(aula)}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge ${badgeClass} mb-2">${aula.compatibilidad || 0}%</span><br>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="aplicarSugerenciaAulaInteligente(${JSON.stringify(aula).replace(/"/g, '&quot;')})">
                                            <i class="fas fa-check"></i> Aplicar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
        } else {
            html += '<div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> No hay aulas alternativas disponibles.</div>';
        }
        html += '</div>';

        // Pestaña de Horarios
        html += '<div class="tab-pane fade" id="horarios" role="tabpanel">';
        if (sugerencias.horarios && sugerencias.horarios.length > 0) {
            tieneSugerencias = true;
            html += '<div class="row mt-3">';
            
            sugerencias.horarios.forEach((horario, index) => {
                const badgeClass = horario.preferencia >= 80 ? 'bg-success' : 
                                 horario.preferencia >= 60 ? 'bg-warning text-dark' : 'bg-secondary';
                
                // Iconos y estilos según el tipo de sugerencia
                let categoriaIcon, tipoTexto, borderClass;
                switch (horario.tipo_sugerencia) {
                    case 'mismo_patron':
                        categoriaIcon = '⏰';
                        tipoTexto = 'Mismo patrón';
                        borderClass = 'border-success';
                        break;
                    case 'intercambio_dias':
                        categoriaIcon = '🔄';
                        tipoTexto = 'Intercambio';
                        borderClass = 'border-warning';
                        break;
                    case 'patron_alternativo':
                        categoriaIcon = '🆕';
                        tipoTexto = 'Patrón alternativo';
                        borderClass = 'border-info';
                        break;
                    default:
                        categoriaIcon = horario.categoria === 'consistente' ? '🔄' : 
                                       horario.categoria === 'mismo_horario' ? '⏰' : '🆕';
                        tipoTexto = 'Alternativo';
                        borderClass = '';
                        break;
                }
                
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card ${borderClass}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">
                                            ${categoriaIcon} <strong>${horario.dia_nombre || 'N/A'}</strong>
                                            <span class="badge bg-secondary ms-1">${tipoTexto}</span>
                                        </h6>
                                        <p class="card-text mb-2">
                                            <i class="fas fa-clock"></i> ${horario.hora_inicio || ''} - ${horario.hora_fin || ''}
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-door-open"></i> ${horario.aulas_disponibles || 0} aulas disponibles
                                        </small>
                                        <div class="mt-2">
                                            <small class="text-primary"><strong>${horario.razon_preferencia || 'Horario disponible'}</strong></small><br>
                                            <small class="text-muted">Impacto: ${horario.impacto || 'Medio'}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge ${badgeClass} mb-2">${horario.preferencia || 0}%</span><br>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="aplicarSugerenciaHorarioInteligente(${JSON.stringify(horario).replace(/"/g, '&quot;')})">
                                            <i class="fas fa-check"></i> Aplicar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
        } else {
            html += '<div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> No hay horarios alternativos disponibles.</div>';
        }
        html += '</div>';

        // Pestaña de Distribuciones
        html += '<div class="tab-pane fade" id="distribuciones" role="tabpanel">';
        if (sugerencias.distribuciones && sugerencias.distribuciones.length > 0) {
            tieneSugerencias = true;
            html += '<div class="row mt-3">';
            
            sugerencias.distribuciones.forEach((dist, index) => {
                const badgeClass = dist.preferencia >= 80 ? 'bg-success' : 
                                 dist.preferencia >= 60 ? 'bg-warning text-dark' : 'bg-secondary';
                
                const tipoIcon = dist.tipo === 'optimizacion' ? '⚡' : 
                               dist.tipo === 'concentracion' ? '📦' : 
                               dist.tipo === 'distribucion' ? '📊' : 
                               dist.tipo === 'tematica' ? '🎯' : '📋';
                
                html += `
                    <div class="col-md-12 mb-3">
                        <div class="card border-info">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">${tipoIcon} ${dist.titulo}</h6>
                                    <span class="badge ${badgeClass}">${dist.preferencia}%</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card-text">${dist.descripcion}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Configuración:</strong><br>
                                        <small class="text-muted">
                                            📅 ${dist.dias_count} días: ${dist.dias_nombres.join(', ')}<br>
                                            ⏱️ ${dist.horas_por_dia}h por día
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Ventajas:</strong><br>
                                        <small class="text-success">
                                            ${dist.ventajas.map(v => '✓ ' + v).join('<br>')}
                                        </small>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-outline-success" 
                                            onclick="aplicarDistribucion('${JSON.stringify(dist).replace(/'/g, '\\\'').replace(/"/g, '&quot;')}')">
                                        <i class="fas fa-magic"></i> Aplicar Distribución
                                    </button>
                                    <button class="btn btn-sm btn-outline-info ms-2" 
                                            onclick="verDetallesDistribucion('${JSON.stringify(dist).replace(/'/g, '\\\'').replace(/"/g, '&quot;')}')">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
        } else {
            html += '<div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> No hay distribuciones alternativas disponibles.</div>';
        }
        html += '</div>';

        html += '</div>'; // Cerrar tab-content

        if (!tieneSugerencias) {
            html = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No se encontraron alternativas disponibles en este momento.</div>';
        }

        document.getElementById('sugerencias-container').innerHTML = html;
    }





    // Función para mostrar panel de reasignación masiva
    async function mostrarReasignacion() {
        panelReasignacion.style.display = 'block';
        panelSugerencias.style.display = 'none';

        // Cargar horarios relacionados por materia por defecto
        cargarHorariosRelacionados('materia');
    }

    async function cargarHorariosRelacionados(tipo) {
        try {
            const response = await fetch(`{{ route("admin.horarios.horarios-relacionados", $horario) }}?tipo=${tipo}`);
            const data = await response.json();
            
            if (data.success) {
                mostrarHorariosRelacionados(data.horarios, tipo);
            }
        } catch (error) {
            console.error('Error cargando horarios relacionados:', error);
        }
    }

    function mostrarHorariosRelacionados(horarios, tipo) {
        let html = `
            <div class="mb-3">
                <label class="form-label">Filtrar por:</label>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm ${tipo === 'materia' ? 'btn-primary' : 'btn-outline-primary'}" 
                            onclick="cargarHorariosRelacionados('materia')">Materia</button>
                    <button type="button" class="btn btn-sm ${tipo === 'profesor' ? 'btn-primary' : 'btn-outline-primary'}" 
                            onclick="cargarHorariosRelacionados('profesor')">Profesor</button>
                    <button type="button" class="btn btn-sm ${tipo === 'aula' ? 'btn-primary' : 'btn-outline-primary'}" 
                            onclick="cargarHorariosRelacionados('aula')">Aula</button>
                </div>
            </div>
        `;

        if (horarios.length > 0) {
            html += `
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Profesor</th>
                                <th>Aula</th>
                                <th>Día</th>
                                <th>Horario</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            horarios.forEach(h => {
                html += `
                    <tr>
                        <td><small>${h.materia}</small></td>
                        <td><small>${h.profesor}</small></td>
                        <td><small>${h.aula}</small></td>
                        <td><span class="badge bg-primary">${h.dia}</span></td>
                        <td><small>${h.horario}</small></td>
                        <td><span class="badge bg-info">${h.tipo_clase}</span></td>
                        <td>
                            <button class="btn btn-xs btn-outline-warning" onclick="intercambiarHorarios(${h.id})">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
        } else {
            html += '<div class="alert alert-info">No hay horarios relacionados</div>';
        }

        document.getElementById('horarios-relacionados').innerHTML = html;
    }

    // Función para mostrar contexto completo de la materia
    function mostrarContextoCompleto(data) {
        if (!data || !data.horarios_completos) return '';
        
        let html = `
            <div class="card border-info mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Contexto Completo de la Materia</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <strong>📚 ${data.horario_actual.materia}</strong><br>
                            <small class="text-muted">👨‍🏫 ${data.horario_actual.profesor}</small>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-primary">${data.contexto_materia.horarios_totales} horarios</span>
                            <span class="badge bg-success">${data.contexto_materia.horas_semanales_actuales}h semanales</span>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <strong>📅 Patrón completo de días y aulas:</strong>
                        <div class="alert alert-light mt-2 mb-0">
                            <div class="text-center">
                                <h5 class="text-primary mb-0">
        `;
        
        // Crear el patrón completo como "Martes Aula 24 - Jueves Lab 3"
        const patronCompleto = data.horarios_completos.map(h => {
            let aulaTexto = h.aula_codigo;
            if (h.tipo_clase === 'laboratorio' || (h.aula_nombre && h.aula_nombre.toLowerCase().includes('lab'))) {
                aulaTexto = 'Lab ' + aulaTexto.replace(/lab|laboratorio/gi, '').trim();
            }
            return `${h.dia_nombre} ${aulaTexto}`;
        }).join(' - ');
        
        html += patronCompleto;
        
        html += `
                                </h5>
                                <small class="text-muted">Patrón actual de la materia</small>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
        `;
        
        data.horarios_completos.forEach(h => {
            const esActual = h.es_actual;
            const cardClass = esActual ? 'border-warning bg-warning bg-opacity-10' : 'border-secondary';
            const badgeClass = esActual ? 'bg-warning text-dark' : 'bg-primary';
            
            html += `
                <div class="col-md-4 mb-2">
                    <div class="card ${cardClass}">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge ${badgeClass}">${h.dia_nombre}</span>
                                    <strong class="ms-1">${h.aula_codigo}</strong>
                                    ${esActual ? '<i class="fas fa-arrow-left text-warning ms-1" title="Horario actual"></i>' : ''}
                                </div>
                                <small class="text-muted">${h.hora_inicio}-${h.hora_fin}</small>
                            </div>
                            <small class="text-muted">${h.tipo_clase} • ${h.aula_nombre}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        return html;
    }

    // Función para mostrar impacto completo de una sugerencia
    function mostrarImpactoCompleto(sugerencia) {
        if (!window.ultimaRespuestaSugerencias || !window.ultimaRespuestaSugerencias.horarios_completos) {
            return '<small class="text-muted">Impacto: ' + (sugerencia.impacto || 'Mínimo') + '</small>';
        }
        
        const horariosCompletos = window.ultimaRespuestaSugerencias.horarios_completos;
        const horarioActual = window.ultimaRespuestaSugerencias.horario_actual;
        
        // Simular el cambio para mostrar el nuevo patrón
        let nuevoPatron = horariosCompletos.map(h => {
            if (h.es_actual && sugerencia.aula_id) {
                // Cambiar solo el aula del horario actual
                let aulaTexto = sugerencia.codigo;
                if (sugerencia.tipo === 'laboratorio' || sugerencia.codigo.toLowerCase().includes('lab')) {
                    aulaTexto = 'Lab ' + aulaTexto.replace(/lab|laboratorio/gi, '').trim();
                }
                return `${h.dia_nombre} ${aulaTexto}`;
            } else {
                let aulaTexto = h.aula_codigo;
                if (h.tipo_clase === 'laboratorio' || (h.aula_nombre && h.aula_nombre.toLowerCase().includes('lab'))) {
                    aulaTexto = 'Lab ' + aulaTexto.replace(/lab|laboratorio/gi, '').trim();
                }
                return `${h.dia_nombre} ${aulaTexto}`;
            }
        }).join(' - ');
        
        return `
            <small class="text-success">
                <strong>Nuevo patrón:</strong> ${nuevoPatron}
            </small>
        `;
    }

    // Funciones para aplicar sugerencias con información completa
    window.aplicarSugerenciaAulaInteligente = function(sugerencia) {
        console.log('🏢 Aplicando sugerencia de aula inteligente:', sugerencia);
        
        const aulaSelect = document.getElementById('aula_id');
        aulaSelect.value = sugerencia.aula_id;
        
        // Verificar que se aplicó correctamente
        if (aulaSelect.value == sugerencia.aula_id) {
            console.log('✅ Aula aplicada correctamente');
            
            // Mostrar información completa del cambio
            const patronAnterior = window.ultimaRespuestaSugerencias ? 
                window.ultimaRespuestaSugerencias.horarios_completos.map(h => {
                    let aulaTexto = h.aula_codigo;
                    if (h.tipo_clase === 'laboratorio' || (h.aula_nombre && h.aula_nombre.toLowerCase().includes('lab'))) {
                        aulaTexto = 'Lab ' + aulaTexto.replace(/lab|laboratorio/gi, '').trim();
                    }
                    return `${h.dia_nombre} ${aulaTexto}`;
                }).join(' - ') : 'N/A';
            
            // Calcular nuevo patrón
            const nuevoPatron = window.ultimaRespuestaSugerencias ? 
                window.ultimaRespuestaSugerencias.horarios_completos.map(h => {
                    if (h.es_actual) {
                        let aulaTexto = sugerencia.codigo;
                        if (sugerencia.tipo === 'laboratorio' || sugerencia.codigo.toLowerCase().includes('lab')) {
                            aulaTexto = 'Lab ' + aulaTexto.replace(/lab|laboratorio/gi, '').trim();
                        }
                        return `${h.dia_nombre} ${aulaTexto}`;
                    } else {
                        let aulaTexto = h.aula_codigo;
                        if (h.tipo_clase === 'laboratorio' || (h.aula_nombre && h.aula_nombre.toLowerCase().includes('lab'))) {
                            aulaTexto = 'Lab ' + aulaTexto.replace(/lab|laboratorio/gi, '').trim();
                        }
                        return `${h.dia_nombre} ${aulaTexto}`;
                    }
                }).join(' - ') : 'N/A';
            
            const mensajeCompleto = `
                <div class="text-center mb-3">
                    <h6><i class="fas fa-check-circle text-success"></i> Aula Cambiada Exitosamente</h6>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="alert alert-light">
                            <strong>📋 Cambio aplicado:</strong><br>
                            <span class="text-muted">Anterior:</span> ${patronAnterior}<br>
                            <span class="text-success"><strong>Nuevo:</strong> ${nuevoPatron}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <strong>🏢 Nueva aula:</strong><br>
                        <span class="badge bg-primary">${sugerencia.codigo}</span>
                        <small class="text-muted d-block">${sugerencia.nombre}</small>
                        <small class="text-muted">Capacidad: ${sugerencia.capacidad} personas</small>
                    </div>
                    
                    <div class="col-md-6">
                        <strong>📊 Compatibilidad:</strong><br>
                        <span class="badge bg-success">${sugerencia.compatibilidad}%</span>
                        <small class="text-muted d-block">${sugerencia.razon}</small>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <small><i class="fas fa-info-circle"></i> Los cambios se aplicarán cuando guardes el formulario.</small>
                </div>
            `;
            
            mostrarNotificacionModal(mensajeCompleto, 'success', 'Cambio Aplicado');
            validarDisponibilidad();
            actualizarInfoCargaHoraria();
        } else {
            console.error('❌ Error aplicando aula');
            mostrarNotificacionModal('❌ Error al cambiar aula. Inténtalo de nuevo.', 'error');
        }
    };

    window.aplicarSugerenciaHorarioInteligente = function(sugerencia) {
        console.log('⏰ Aplicando sugerencia de horario inteligente:', sugerencia);
        
        // Aplicar cambios al formulario
        document.getElementById('dia_semana').value = sugerencia.dia;
        document.getElementById('hora_inicio').value = sugerencia.hora_inicio;
        document.getElementById('hora_fin').value = sugerencia.hora_fin;
        
        // Calcular duración
        calcularDuracion();
        
        // Buscar aula disponible automáticamente
        buscarYAplicarAulaDisponible(sugerencia.dia, sugerencia.hora_inicio, sugerencia.hora_fin).then(aulaEncontrada => {
            const patronAnterior = window.ultimaRespuestaSugerencias ? 
                window.ultimaRespuestaSugerencias.horarios_completos.map(h => {
                    let aulaTexto = h.aula_codigo;
                    if (h.tipo_clase === 'laboratorio' || (h.aula_nombre && h.aula_nombre.toLowerCase().includes('lab'))) {
                        aulaTexto = 'Lab ' + aulaTexto.replace(/lab|laboratorio/gi, '').trim();
                    }
                    return `${h.dia_nombre} ${aulaTexto}`;
                }).join(' - ') : 'N/A';
            
            // Calcular nuevo patrón
            const aulaActual = document.getElementById('aula_id').selectedOptions[0]?.text || 'N/A';
            const nuevoPatron = window.ultimaRespuestaSugerencias ? 
                window.ultimaRespuestaSugerencias.horarios_completos.map(h => {
                    if (h.es_actual) {
                        return `${sugerencia.dia_nombre} ${aulaActual.split(' - ')[0]}`;
                    } else {
                        let aulaTexto = h.aula_codigo;
                        if (h.tipo_clase === 'laboratorio' || (h.aula_nombre && h.aula_nombre.toLowerCase().includes('lab'))) {
                            aulaTexto = 'Lab ' + aulaTexto.replace(/lab|laboratorio/gi, '').trim();
                        }
                        return `${h.dia_nombre} ${aulaTexto}`;
                    }
                }).join(' - ') : 'N/A';
            
            const mensajeCompleto = `
                <div class="text-center mb-3">
                    <h6><i class="fas fa-clock text-success"></i> Horario Cambiado Exitosamente</h6>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="alert alert-light">
                            <strong>📋 Cambio aplicado:</strong><br>
                            <span class="text-muted">Anterior:</span> ${patronAnterior}<br>
                            <span class="text-success"><strong>Nuevo:</strong> ${nuevoPatron}</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <strong>📅 Nuevo horario:</strong><br>
                        <span class="badge bg-primary">${sugerencia.dia_nombre}</span>
                        <span class="badge bg-info ms-1">${sugerencia.hora_inicio} - ${sugerencia.hora_fin}</span>
                    </div>
                    
                    <div class="col-md-6">
                        <strong>📊 Preferencia:</strong><br>
                        <span class="badge bg-success">${sugerencia.preferencia}%</span>
                        <small class="text-muted d-block">${sugerencia.razon_preferencia}</small>
                    </div>
                </div>
                
                ${aulaEncontrada ? 
                    '<div class="alert alert-success mt-3"><small><i class="fas fa-check"></i> Aula compatible encontrada automáticamente.</small></div>' :
                    '<div class="alert alert-warning mt-3"><small><i class="fas fa-exclamation-triangle"></i> Se mantiene el aula actual. Verifica disponibilidad.</small></div>'
                }
                
                <div class="alert alert-info mt-3">
                    <small><i class="fas fa-info-circle"></i> Los cambios se aplicarán cuando guardes el formulario.</small>
                </div>
            `;
            
            mostrarNotificacionModal(mensajeCompleto, 'success', 'Horario Actualizado');
        });
    };

    // Funciones para aplicar sugerencias simples (compatibilidad)
    window.aplicarSugerenciaAula = function(aulaId) {
        console.log('🏢 Aplicando aula:', aulaId);
        const aulaSelect = document.getElementById('aula_id');
        aulaSelect.value = aulaId;
        
        // Verificar que se aplicó correctamente
        if (aulaSelect.value == aulaId) {
            console.log('✅ Aula aplicada correctamente');
            mostrarNotificacionSimple('✅ Aula cambiada correctamente');
            validarDisponibilidad();
            actualizarInfoCargaHoraria();
        } else {
            console.error('❌ Error aplicando aula');
            mostrarNotificacionSimple('❌ Error al cambiar aula', 'error');
        }
    };



    // Actualizar información de carga horaria
    function actualizarInfoCargaHoraria() {
        try {
            const usarConfigPorDia = document.getElementById('usar_configuracion_por_dia').checked;
            const horasSemanales = {{ $horario->cargaAcademica->grupo->materia->horas_semanales ?? 4 }};
            
            if (usarConfigPorDia) {
                const diasSeleccionados = Array.from(document.querySelectorAll('.dia-checkbox:checked'));
                const diasCount = diasSeleccionados.length;
                const horasPorDia = diasCount > 0 ? (horasSemanales / diasCount).toFixed(1) : 0;
                
                document.getElementById('dias-actuales-info').textContent = `${diasCount} días`;
                document.getElementById('horas-por-dia-info').textContent = `${horasPorDia}h`;
                document.getElementById('distribucion-info').textContent = 'Automática';
            } else {
                document.getElementById('dias-actuales-info').textContent = '1 día';
                document.getElementById('horas-por-dia-info').textContent = `${horasSemanales}h`;
                document.getElementById('distribucion-info').textContent = 'Manual';
            }
        } catch (error) {
            console.error('Error actualizando info carga horaria:', error);
        }
    }

    // Función para buscar y aplicar aula disponible automáticamente
    async function buscarYAplicarAulaDisponible(dia, horaInicio, horaFin) {
        try {
            // Obtener todas las aulas disponibles
            const aulas = @json($aulas);
            const cargaAcademicaId = document.getElementById('carga_academica_id').value;
            const periodoAcademico = document.getElementById('periodo_academico').value || '{{ $horario->periodo_academico }}';
            
            console.log('Buscando aula disponible para:', dia, horaInicio, horaFin);
            
            // Buscar la primera aula disponible para este nuevo horario
            for (const aula of aulas) {
                try {
                    const response = await fetch('{{ route("admin.horarios.validar-disponibilidad") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            carga_academica_id: cargaAcademicaId,
                            aula_id: aula.id,
                            dia_semana: dia,
                            hora_inicio: horaInicio,
                            hora_fin: horaFin,
                            periodo_academico: periodoAcademico,
                            excluir_id: {{ $horario->id }}
                        })
                    });

                    const data = await response.json();
                    
                    if (data.disponible) {
                        // Encontramos un aula disponible, aplicarla automáticamente
                        const aulaSelect = document.getElementById('aula_id');
                        aulaSelect.value = aula.id;
                        
                        // Verificar que se aplicó correctamente
                        if (aulaSelect.value == aula.id) {
                            console.log(`✅ Aula ${aula.codigo_aula} aplicada automáticamente`);
                            mostrarNotificacionSimple(`✅ Horario y aula cambiados: ${getDiaNombre(dia)} ${horaInicio}-${horaFin} en ${aula.codigo_aula}`);
                            return true; // Éxito
                        } else {
                            console.error('Error: No se pudo aplicar el aula al select');
                        }
                    }
                } catch (error) {
                    console.log(`Error validando aula ${aula.codigo_aula}:`, error);
                    continue; // Continuar con la siguiente aula
                }
            }
            
            // Si no se encontró ninguna aula disponible, mantener la actual
            console.warn('No se encontró aula disponible, manteniendo aula actual');
            mostrarNotificacionSimple(`⚠️ Horario cambiado. No se encontró aula disponible, se mantiene la actual.`, 'warning');
            return false;
            
        } catch (error) {
            console.error('Error buscando aula disponible:', error);
            mostrarNotificacionSimple(`⚠️ Error buscando aula. Se mantiene la configuración actual.`, 'warning');
            return false;
        }
    }

    // Función auxiliar para obtener nombre del día
    function getDiaNombre(dia) {
        const dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        return dias[dia] || 'N/A';
    }

    // Función para mostrar notificaciones simples (toast-style)
    function mostrarNotificacionSimple(mensaje, tipo = 'success') {
        // Crear elemento de notificación
        const notificacion = document.createElement('div');
        notificacion.className = `alert alert-${tipo === 'error' ? 'danger' : tipo} alert-dismissible fade show position-fixed`;
        notificacion.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;';
        
        const iconClass = tipo === 'success' ? 'fas fa-check-circle' : 
                         tipo === 'error' ? 'fas fa-exclamation-triangle' : 
                         tipo === 'warning' ? 'fas fa-exclamation-circle' : 'fas fa-info-circle';
        
        notificacion.innerHTML = `
            <i class="${iconClass}"></i> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notificacion);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notificacion.parentNode) {
                notificacion.remove();
            }
        }, 5000);
    }

    // Función para mostrar modal de confirmación
    function mostrarConfirmacionModal(mensaje, callback) {
        const modal = document.getElementById('confirmacionModal');
        const modalMessage = document.getElementById('confirmacion-message');
        const confirmBtn = document.getElementById('confirmacion-btn');
        
        modalMessage.innerHTML = mensaje;
        
        // Limpiar event listeners anteriores
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        // Agregar nuevo event listener
        newConfirmBtn.addEventListener('click', () => {
            callback();
            bootstrap.Modal.getInstance(modal).hide();
        });
        
        // Mostrar modal
        new bootstrap.Modal(modal).show();
    }

    // Función para mostrar notificaciones con modal
    function mostrarNotificacionModal(mensaje, tipo = 'success', titulo = null, accion = null) {
        const modal = document.getElementById('notificacionModal');
        const modalHeader = document.getElementById('modal-header');
        const modalIcon = document.getElementById('modal-icon');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalActionBtn = document.getElementById('modal-action-btn');
        
        // Configurar según el tipo
        let iconClass, headerClass, tituloTexto;
        
        switch (tipo) {
            case 'success':
                iconClass = 'fas fa-check-circle text-success';
                headerClass = 'bg-success text-white';
                tituloTexto = titulo || 'Éxito';
                break;
            case 'error':
                iconClass = 'fas fa-exclamation-triangle text-danger';
                headerClass = 'bg-danger text-white';
                tituloTexto = titulo || 'Error';
                break;
            case 'warning':
                iconClass = 'fas fa-exclamation-circle text-warning';
                headerClass = 'bg-warning text-dark';
                tituloTexto = titulo || 'Advertencia';
                break;
            case 'info':
            default:
                iconClass = 'fas fa-info-circle text-info';
                headerClass = 'bg-info text-white';
                tituloTexto = titulo || 'Información';
                break;
        }
        
        // Aplicar estilos
        modalHeader.className = `modal-header ${headerClass}`;
        modalIcon.className = iconClass;
        modalTitle.textContent = tituloTexto;
        modalMessage.innerHTML = mensaje;
        
        // Configurar botón de acción si se proporciona
        if (accion) {
            modalActionBtn.style.display = 'inline-block';
            modalActionBtn.textContent = accion.texto || 'Acción';
            modalActionBtn.onclick = accion.callback || function() {};
        } else {
            modalActionBtn.style.display = 'none';
        }
        
        // Mostrar modal
        new bootstrap.Modal(modal).show();
    }
        
        // Configurar botón de acción si existe
        if (accion && typeof accion === 'function') {
            modalActionBtn.style.display = 'inline-block';
            modalActionBtn.onclick = () => {
                accion();
                bootstrap.Modal.getInstance(modal).hide();
            };
        } else {
            modalActionBtn.style.display = 'none';
        }
        
        // Mostrar modal
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }

    // Función para mostrar confirmaciones con modal
    function mostrarConfirmacionModal(mensaje, onConfirm, onCancel = null) {
        const modal = document.getElementById('confirmacionModal');
        const modalMessage = document.getElementById('confirmacion-message');
        const confirmBtn = document.getElementById('confirmacion-btn');
        
        modalMessage.innerHTML = mensaje;
        
        // Configurar botón de confirmación
        confirmBtn.onclick = () => {
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
            bootstrap.Modal.getInstance(modal).hide();
        };
        
        // Configurar cancelación si existe
        if (onCancel && typeof onCancel === 'function') {
            modal.addEventListener('hidden.bs.modal', onCancel, { once: true });
        }
        
        // Mostrar modal
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }

    // Función de compatibilidad (mantener el nombre anterior)
    function mostrarNotificacionSimple(mensaje, tipo = 'success') {
        mostrarNotificacionModal(mensaje, tipo);
    }

    window.aplicarSugerenciaHorario = async function(dia, horaInicio, horaFin) {
        // Cambiar día y horario primero
        document.getElementById('dia_semana').value = dia;
        document.getElementById('hora_inicio').value = horaInicio;
        document.getElementById('hora_fin').value = horaFin;
        
        // Calcular duración
        calcularDuracion();
        
        // Buscar y aplicar aula disponible (esperar a que termine)
        const aulaEncontrada = await buscarYAplicarAulaDisponible(dia, horaInicio, horaFin);
        
        // Validar disponibilidad después de que se haya aplicado el aula
        validarDisponibilidad();
        
        console.log('Sugerencia de horario aplicada completamente');
    };

    // Nueva función para aplicar sugerencias inteligentes de aulas
    window.aplicarSugerenciaAulaInteligente = function(aulaData) {
        try {
            console.log('🏢 Aplicando sugerencia inteligente de aula:', aulaData);
            
            switch (aulaData.tipo_sugerencia) {
                case 'intercambio':
                    aplicarIntercambioAulas(aulaData);
                    break;
                case 'cambio_tipo':
                    aplicarCambioTipoClase(aulaData);
                    break;
                case 'simple':
                default:
                    aplicarCambioSimpleAula(aulaData);
                    break;
            }
        } catch (error) {
            console.error('❌ Error aplicando sugerencia de aula:', error);
            mostrarNotificacionModal('❌ Error al aplicar sugerencia de aula', 'error');
        }
    };

    // Nueva función para aplicar sugerencias inteligentes de horarios
    window.aplicarSugerenciaHorarioInteligente = function(horarioData) {
        try {
            console.log('⏰ Aplicando sugerencia inteligente de horario:', horarioData);
            
            switch (horarioData.tipo_sugerencia) {
                case 'intercambio_dias':
                    aplicarIntercambioDias(horarioData);
                    break;
                case 'patron_alternativo':
                    aplicarPatronAlternativo(horarioData);
                    break;
                case 'mismo_patron':
                default:
                    aplicarMismoPatron(horarioData);
                    break;
            }
        } catch (error) {
            console.error('❌ Error aplicando sugerencia de horario:', error);
            mostrarNotificacionModal('❌ Error al aplicar sugerencia de horario', 'error');
        }
    };

    // Funciones específicas para cada tipo de aplicación
    function aplicarCambioSimpleAula(aulaData) {
        document.getElementById('aula_id').value = aulaData.aula_id;
        validarDisponibilidad();
        
        // Actualizar contexto visual inmediatamente
        actualizarContextoVisual();
        
        mostrarNotificacionModal(
            `✅ ${aulaData.descripcion}<br><small class="text-muted">${aulaData.impacto}</small>`,
            'success',
            'Aula Cambiada'
        );
    }

    function aplicarIntercambioAulas(aulaData) {
        const mensaje = `
            <div class="text-center">
                <h6>🔄 Intercambio de Aulas</h6>
                <p>${aulaData.descripcion}</p>
                <div class="alert alert-info">
                    <small><i class="fas fa-info-circle"></i> ${aulaData.impacto}</small>
                </div>
                <p><strong>¿Confirmar intercambio?</strong></p>
            </div>
        `;
        
        mostrarConfirmacionModal(mensaje, () => {
            document.getElementById('aula_id').value = aulaData.aula_id;
            validarDisponibilidad();
            mostrarNotificacionModal('✅ Intercambio de aulas aplicado correctamente', 'success');
        });
    }

    function aplicarCambioTipoClase(aulaData) {
        const mensaje = `
            <div class="text-center">
                <h6>🎯 Cambio de Tipo de Clase</h6>
                <p>${aulaData.descripcion}</p>
                <div class="alert alert-warning">
                    <small><i class="fas fa-exclamation-triangle"></i> Esto cambiará el tipo de clase a <strong>${aulaData.nuevo_tipo}</strong></small>
                </div>
                <p><strong>¿Confirmar cambio?</strong></p>
            </div>
        `;
        
        mostrarConfirmacionModal(mensaje, () => {
            document.getElementById('aula_id').value = aulaData.aula_id;
            document.getElementById('tipo_clase').value = aulaData.nuevo_tipo;
            validarDisponibilidad();
            mostrarNotificacionModal('✅ Tipo de clase y aula cambiados correctamente', 'success');
        });
    }

    async function aplicarMismoPatron(horarioData) {
        document.getElementById('dia_semana').value = horarioData.dia;
        document.getElementById('hora_inicio').value = horarioData.hora_inicio;
        document.getElementById('hora_fin').value = horarioData.hora_fin;
        
        calcularDuracion();
        
        if (horarioData.mejor_aula && horarioData.mejor_aula.id) {
            document.getElementById('aula_id').value = horarioData.mejor_aula.id;
        } else {
            await buscarYAplicarAulaDisponible(horarioData.dia, horarioData.hora_inicio, horarioData.hora_fin);
        }
        
        validarDisponibilidad();
        mostrarNotificacionModal(
            `✅ Horario cambiado manteniendo el patrón<br><small class="text-muted">${horarioData.impacto}</small>`,
            'success'
        );
    }

    function aplicarPatronAlternativo(horarioData) {
        const mensaje = `
            <div class="text-center">
                <h6>🆕 Patrón Alternativo</h6>
                <p><strong>${horarioData.dia_nombre}</strong> ${horarioData.hora_inicio}-${horarioData.hora_fin}</p>
                <div class="alert alert-info">
                    <small><i class="fas fa-info-circle"></i> ${horarioData.razon_preferencia}</small>
                </div>
                <div class="alert alert-warning">
                    <small><i class="fas fa-exclamation-triangle"></i> ${horarioData.impacto}</small>
                </div>
                <p><strong>¿Aplicar nuevo patrón?</strong></p>
            </div>
        `;
        
        mostrarConfirmacionModal(mensaje, async () => {
            document.getElementById('dia_semana').value = horarioData.dia;
            document.getElementById('hora_inicio').value = horarioData.hora_inicio;
            document.getElementById('hora_fin').value = horarioData.hora_fin;
            
            calcularDuracion();
            await buscarYAplicarAulaDisponible(horarioData.dia, horarioData.hora_inicio, horarioData.hora_fin);
            validarDisponibilidad();
            
            mostrarNotificacionModal('✅ Patrón alternativo aplicado correctamente', 'success');
        });
    }

    function aplicarIntercambioDias(horarioData) {
        const mensaje = `
            <div class="text-center">
                <h6>🔄 Intercambio de Días</h6>
                <p>Intercambiar con <strong>${horarioData.dia_nombre}</strong></p>
                <p>${horarioData.hora_inicio}-${horarioData.hora_fin}</p>
                <div class="alert alert-warning">
                    <small><i class="fas fa-exclamation-triangle"></i> ${horarioData.impacto}</small>
                </div>
                <p><strong>¿Confirmar intercambio?</strong></p>
            </div>
        `;
        
        mostrarConfirmacionModal(mensaje, () => {
            document.getElementById('dia_semana').value = horarioData.dia;
            document.getElementById('hora_inicio').value = horarioData.hora_inicio;
            document.getElementById('hora_fin').value = horarioData.hora_fin;
            
            calcularDuracion();
            validarDisponibilidad();
            
            mostrarNotificacionModal('✅ Intercambio de días aplicado correctamente', 'success');
        });
    }

    // Función para actualizar el contexto visual inmediatamente
    function actualizarContextoVisual() {
        if (!window.ultimaRespuestaSugerencias || !window.ultimaRespuestaSugerencias.horarios_completos) {
            return;
        }

        // Obtener valores actuales del formulario
        const aulaActual = document.getElementById('aula_id');
        const diaActual = document.getElementById('dia_semana');
        const horaInicioActual = document.getElementById('hora_inicio');
        const horaFinActual = document.getElementById('hora_fin');
        const tipoClaseActual = document.getElementById('tipo_clase');

        if (!aulaActual || !diaActual || !horaInicioActual || !horaFinActual) {
            return;
        }

        // Buscar el aula seleccionada
        const aulas = @json($aulas);
        const aulaSeleccionada = aulas.find(a => a.id == aulaActual.value);
        
        if (!aulaSeleccionada) {
            return;
        }

        // Actualizar el contexto visual
        const horarios = window.ultimaRespuestaSugerencias.horarios_completos;
        const horarioActualIndex = horarios.findIndex(h => h.es_actual);
        
        if (horarioActualIndex !== -1) {
            // Actualizar los datos del horario actual
            horarios[horarioActualIndex].aula_codigo = aulaSeleccionada.codigo_aula;
            horarios[horarioActualIndex].aula_nombre = aulaSeleccionada.nombre;
            horarios[horarioActualIndex].hora_inicio = horaInicioActual.value;
            horarios[horarioActualIndex].hora_fin = horaFinActual.value;
            horarios[horarioActualIndex].tipo_clase = ucfirst(tipoClaseActual.value);
            
            // Regenerar el contexto visual
            const contextoContainer = document.querySelector('#sugerencias-container');
            if (contextoContainer) {
                const nuevoContexto = mostrarContextoCompleto(window.ultimaRespuestaSugerencias);
                
                // Buscar y reemplazar solo la sección del contexto
                const contextoActual = contextoContainer.querySelector('.card.border-info');
                if (contextoActual) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = nuevoContexto;
                    const nuevoContextoElement = tempDiv.querySelector('.card.border-info');
                    
                    if (nuevoContextoElement) {
                        contextoActual.replaceWith(nuevoContextoElement);
                    }
                }
            }
        }
    }

    // Función auxiliar para capitalizar primera letra
    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    window.cargarHorariosRelacionados = cargarHorariosRelacionados;

    // Función para mostrar el contexto completo de la materia
    function mostrarContextoCompleto(data) {
        if (!data || !data.horarios_completos) {
            return '';
        }

        let html = `
            <div class="card border-info mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar-week"></i> 
                        Horarios Actuales de la Materia: ${data.horario_actual.materia}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
        `;

        data.horarios_completos.forEach((horario, index) => {
            const esActual = horario.es_actual;
            const borderClass = esActual ? 'border-warning' : 'border-light';
            const bgClass = esActual ? 'bg-warning bg-opacity-10' : '';
            const badgeClass = esActual ? 'bg-warning text-dark' : 'bg-secondary';

            html += `
                <div class="col-md-4 mb-3">
                    <div class="card ${borderClass} ${bgClass}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1">
                                        <i class="fas fa-calendar-day text-primary"></i>
                                        <strong>${horario.dia_nombre}</strong>
                                        ${esActual ? '<span class="badge bg-warning text-dark ms-1">Editando</span>' : ''}
                                    </h6>
                                    <p class="card-text mb-2">
                                        <i class="fas fa-clock text-success"></i> 
                                        <strong>${horario.hora_inicio} - ${horario.hora_fin}</strong>
                                    </p>
                                    <p class="card-text mb-2">
                                        <i class="fas fa-door-open text-info"></i> 
                                        <strong>${horario.aula_codigo}</strong>
                                        <small class="text-muted">- ${horario.aula_nombre}</small>
                                    </p>
                                    <span class="badge ${badgeClass}">
                                        ${horario.tipo_clase}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            <strong>Contexto:</strong> Esta materia tiene ${data.horarios_completos.length} horario(s) por semana. 
                            Las sugerencias consideran todos estos horarios para mantener la coherencia.
                        </small>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    // Función para mostrar el impacto completo de una sugerencia
    function mostrarImpactoCompleto(sugerencia) {
        if (!window.ultimaRespuestaSugerencias || !window.ultimaRespuestaSugerencias.horarios_completos) {
            return '';
        }

        const horarios = window.ultimaRespuestaSugerencias.horarios_completos;
        let html = '<div class="mt-2"><small class="text-success"><strong>Resultado:</strong> ';

        if (sugerencia.tipo_sugerencia === 'intercambio' && sugerencia.horario_destino) {
            // Mostrar intercambio específico
            const horarioActual = horarios.find(h => h.es_actual);
            const horarioDestino = horarios.find(h => h.id === sugerencia.horario_destino);
            
            if (horarioActual && horarioDestino) {
                html += `${horarioActual.dia_nombre} → ${sugerencia.codigo} | ${horarioDestino.dia_nombre} → ${horarioActual.aula_codigo}`;
            }
        } else {
            // Mostrar cambio simple
            const horarioActual = horarios.find(h => h.es_actual);
            if (horarioActual) {
                html += `${horarioActual.dia_nombre} → ${sugerencia.codigo}`;
                
                // Mostrar otros días que no cambian
                const otrosHorarios = horarios.filter(h => !h.es_actual);
                if (otrosHorarios.length > 0) {
                    html += ' | ';
                    html += otrosHorarios.map(h => `${h.dia_nombre} → ${h.aula_codigo}`).join(' | ');
                }
            }
        }

        html += '</small></div>';
        return html;
    }

    // Funciones para manejar distribuciones
    window.aplicarDistribucion = function(distribucionJson) {
        try {
            const distribucion = JSON.parse(distribucionJson.replace(/&quot;/g, '"'));
            console.log('🎯 Aplicando distribución:', distribucion);
            
            // Mostrar modal de confirmación
            const mensajeConfirmacion = `
                <div class="text-center">
                    <h6><strong>${distribucion.titulo}</strong></h6>
                    <p class="mb-3">${distribucion.descripcion}</p>
                    
                    <div class="row text-start">
                        <div class="col-6">
                            <strong>📅 Configuración:</strong><br>
                            <small class="text-muted">
                                • ${distribucion.dias_count} días: ${distribucion.dias_nombres.join(', ')}<br>
                                • ${distribucion.horas_por_dia}h por día
                            </small>
                        </div>
                        <div class="col-6">
                            <strong>✅ Ventajas:</strong><br>
                            <small class="text-success">
                                ${distribucion.ventajas.map(v => '• ' + v).join('<br>')}
                            </small>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3 mb-0">
                        <small><i class="fas fa-exclamation-triangle"></i> Esto cambiará la configuración actual del horario.</small>
                    </div>
                </div>
            `;
            
            mostrarConfirmacionModal(mensajeConfirmacion, () => {
                aplicarDistribucionCompleta(distribucion);
            });
            
        } catch (error) {
            console.error('❌ Error aplicando distribución:', error);
            mostrarNotificacionModal('❌ Error al aplicar distribución: ' + error.message, 'error');
        }
    };

    window.verDetallesDistribucion = function(distribucionJson) {
        try {
            const distribucion = JSON.parse(distribucionJson.replace(/&quot;/g, '"'));
            
            let detallesHtml = `
                <div class="text-center mb-3">
                    <h5><i class="fas fa-calendar-alt text-primary"></i> ${distribucion.titulo}</h5>
                    <span class="badge bg-primary">${distribucion.preferencia}% de preferencia</span>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <strong>📝 Descripción:</strong><br>
                        <p class="text-muted">${distribucion.descripcion}</p>
                    </div>
                    
                    <div class="col-md-6">
                        <strong>📅 Configuración:</strong><br>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-calendar-day text-info"></i> Días: ${distribucion.dias_count} (${distribucion.dias_nombres.join(', ')})</li>
                            <li><i class="fas fa-clock text-info"></i> Horas por día: ${distribucion.horas_por_dia}h</li>
                            <li><i class="fas fa-tag text-info"></i> Tipo: ${distribucion.tipo}</li>
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <strong>✅ Ventajas:</strong><br>
                        <ul class="list-unstyled">
                            ${distribucion.ventajas.map(ventaja => `<li><i class="fas fa-check text-success"></i> ${ventaja}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            `;
            
            if (distribucion.franjas_sugeridas) {
                detallesHtml += `
                    <div class="mt-3">
                        <strong>⏰ Franjas horarias sugeridas:</strong><br>
                        <div class="row">
                            ${distribucion.franjas_sugeridas.map((franja, index) => `
                                <div class="col-md-4 mb-2">
                                    <div class="card border-primary">
                                        <div class="card-body p-2 text-center">
                                            <strong>${distribucion.dias_nombres[index]}</strong><br>
                                            <span class="text-primary">${franja[0]} - ${franja[1]}</span>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
            
            mostrarNotificacionModal(detallesHtml, 'info', 'Detalles de la Distribución');
            
        } catch (error) {
            console.error('❌ Error mostrando detalles:', error);
            mostrarNotificacionModal('Error al mostrar detalles de la distribución', 'error');
        }
    };

    function aplicarDistribucionCompleta(distribucion) {
        console.log('🚀 Iniciando aplicación de distribución completa...');
        
        try {
            // Para distribuciones simples, aplicar al horario actual
            if (distribucion.franjas_sugeridas && distribucion.franjas_sugeridas.length > 0) {
                const primeraFranja = distribucion.franjas_sugeridas[0];
                const primerDia = distribucion.dias[0];
                
                // Aplicar el primer horario de la distribución
                document.getElementById('dia_semana').value = primerDia;
                document.getElementById('hora_inicio').value = primeraFranja[0];
                document.getElementById('hora_fin').value = primeraFranja[1];
                
                // Calcular duración
                calcularDuracion();
                
                // Buscar aula disponible
                buscarYAplicarAulaDisponible(primerDia, primeraFranja[0], primeraFranja[1]);
                
                mostrarNotificacionSimple(
                    `✅ Distribución "${distribucion.titulo}" aplicada. ` +
                    `Configurado: ${distribucion.dias_nombres[0]} ${primeraFranja[0]}-${primeraFranja[1]}`,
                    'success'
                );
                
                // Mostrar información adicional
                setTimeout(() => {
                    const infoAdicionalHtml = `
                        <div class="text-center mb-3">
                            <h6><i class="fas fa-lightbulb text-warning"></i> Información Adicional</h6>
                        </div>
                        
                        <div class="alert alert-info">
                            <p><strong>Esta distribución sugiere ${distribucion.dias_count} días de clase.</strong></p>
                            <p>Se ha aplicado el primer horario. Para completar la distribución:</p>
                        </div>
                        
                        <div class="row">
                            ${distribucion.dias_nombres.map((dia, index) => {
                                const franja = distribucion.franjas_sugeridas[index];
                                return `
                                    <div class="col-md-6 mb-2">
                                        <div class="card border-secondary">
                                            <div class="card-body p-2">
                                                <strong>${dia}:</strong> 
                                                <span class="text-primary">${franja ? franja[0] + '-' + franja[1] : 'Por definir'}</span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                        
                        <div class="alert alert-warning mt-3">
                            <small><i class="fas fa-info-circle"></i> Puedes crear horarios adicionales desde "Crear Nuevo Horario".</small>
                        </div>
                        
                        <div class="text-center">
                            <p><strong>¿Quieres ir a crear los horarios adicionales?</strong></p>
                        </div>
                    `;
                    
                    mostrarConfirmacionModal(infoAdicionalHtml, () => {
                        // Aquí podrías redirigir o abrir modal para crear horarios adicionales
                        console.log('Usuario quiere crear horarios adicionales');
                        window.open('{{ route("admin.horarios.create") }}', '_blank');
                    });
                }, 2000);
            } else {
                mostrarNotificacionSimple('⚠️ Distribución aplicada parcialmente. Revisa la configuración.', 'warning');
            }
            
        } catch (error) {
            console.error('❌ Error en aplicarDistribucionCompleta:', error);
            mostrarNotificacionSimple('❌ Error aplicando distribución completa', 'error');
        }
    }

    // Función para mostrar/ocultar configuración por día
    function toggleConfiguracionPorDia() {
        if (usarConfigPorDia.checked) {
            configSimple.style.display = 'none';
            configMultiple.style.display = 'block';
            configPorDiaDiv.style.display = 'block';
            
            // Deshabilitar campos simples
            document.getElementById('dia_semana').disabled = true;
            document.getElementById('aula_id').disabled = true;
            document.getElementById('tipo_clase').disabled = true;
            
            // Cargar configuración existente si existe
            cargarConfiguracionExistente();
        } else {
            configSimple.style.display = 'block';
            configMultiple.style.display = 'none';
            configPorDiaDiv.style.display = 'none';
            
            // Habilitar campos simples
            document.getElementById('dia_semana').disabled = false;
            document.getElementById('aula_id').disabled = false;
            document.getElementById('tipo_clase').disabled = false;
        }
    }

    // Función para cargar configuración existente
    function cargarConfiguracionExistente() {
        @if($horario->usar_configuracion_por_dia && $horario->configuracion_dias)
            const configuracionExistente = @json($horario->configuracion_dias);
            console.log('Configuración existente:', configuracionExistente);
            
            // Marcar días seleccionados
            configuracionExistente.forEach(config => {
                const checkbox = document.getElementById(`dia${config.dia}_multi`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
            
            // Generar configuración
            generarConfiguracionDias();
            
            // Llenar valores existentes
            setTimeout(() => {
                configuracionExistente.forEach(config => {
                    const aulaSelect = document.querySelector(`select[name="config_dias[${config.dia}][aula_id]"]`);
                    const tipoSelect = document.querySelector(`select[name="config_dias[${config.dia}][tipo_clase]"]`);
                    
                    if (aulaSelect) aulaSelect.value = config.aula_id;
                    if (tipoSelect) tipoSelect.value = config.tipo_clase;
                });
            }, 100);
        @endif
    }

    // Función para generar configuración específica por día
    function generarConfiguracionDias() {
        const diasSeleccionados = Array.from(diasCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => ({ value: cb.value, name: cb.nextElementSibling.textContent }));

        if (diasSeleccionados.length === 0) {
            configDiasContainer.innerHTML = '<p class="text-muted">Selecciona los días para configurar</p>';
            return;
        }

        let html = '';
        diasSeleccionados.forEach(dia => {
            html += `
                <div class="row mb-3 border-bottom pb-3">
                    <div class="col-md-2">
                        <label class="form-label"><strong>${dia.name}</strong></label>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Aula</label>
                        <select class="form-select config-aula" name="config_dias[${dia.value}][aula_id]" required>
                            <option value="">Seleccionar aula...</option>
                            @foreach($aulas as $aula)
                                <option value="{{ $aula->id }}">{{ $aula->codigo_aula }} - {{ $aula->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Tipo de Clase</label>
                        <select class="form-select config-tipo" name="config_dias[${dia.value}][tipo_clase]" required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="teorica">Teórica</option>
                            <option value="practica">Práctica</option>
                            <option value="laboratorio">Laboratorio</option>
                        </select>
                    </div>
                </div>
            `;
        });

        configDiasContainer.innerHTML = html;

        // Agregar event listeners para validación en tiempo real
        document.querySelectorAll('.config-aula, .config-tipo').forEach(select => {
            select.addEventListener('change', validarDisponibilidad);
        });
    }

    // Función de prueba simple
    window.testRutaSimple = async function() {
        console.log('🧪 Iniciando test de conexión...');
        try {
            const url = `{{ route("admin.horarios.test-sugerencias", $horario) }}`;
            console.log('🔗 URL de test:', url);
            
            const response = await fetch(url);
            console.log('📡 Respuesta de test:', response.status);
            
            const data = await response.json();
            console.log('✅ Test exitoso:', data);
            
            alert(`✅ Test exitoso!\n\nEstado: ${response.status}\nMensaje: ${data.message || 'OK'}\n\nRevisa la consola para más detalles.`);
            
            // Si el test funciona, intentar sugerencias de nuevo
            if (data.success) {
                console.log('🔄 Test exitoso, reintentando sugerencias...');
                setTimeout(() => {
                    mostrarSugerencias();
                }, 1000);
            }
        } catch (error) {
            console.error('❌ Test falló:', error);
            alert(`❌ Test falló!\n\nError: ${error.message}\n\nVerifica que el servidor esté funcionando.`);
        }
    };

    // Función de debug para verificar elementos
    window.debugElementos = function() {
        console.log('🔍 Debug de elementos:');
        console.log('- Botón sugerir:', document.getElementById('btn-sugerir-alternativas'));
        console.log('- Panel sugerencias:', document.getElementById('panel-sugerencias'));
        console.log('- Container sugerencias:', document.getElementById('sugerencias-container'));
        console.log('- Horario ID:', {{ $horario->id }});
        
        const elementos = {
            boton: !!document.getElementById('btn-sugerir-alternativas'),
            panel: !!document.getElementById('panel-sugerencias'),
            container: !!document.getElementById('sugerencias-container')
        };
        
        alert(`🔍 Debug de elementos:\n\n${JSON.stringify(elementos, null, 2)}`);
    };

    // Event listeners
    console.log('🔧 Configurando event listeners...');
    
    if (horaInicio) {
        horaInicio.addEventListener('change', () => {
            calcularDuracion();
            validarDisponibilidad();
        });
        console.log('✅ Event listener hora inicio configurado');
    }
    
    if (horaFin) {
        horaFin.addEventListener('change', () => {
            calcularDuracion();
            validarDisponibilidad();
        });
        console.log('✅ Event listener hora fin configurado');
    }
    
    if (aulaSelect) {
        aulaSelect.addEventListener('change', validarDisponibilidad);
        console.log('✅ Event listener aula configurado');
    }
    
    if (diaSelect) {
        diaSelect.addEventListener('change', validarDisponibilidad);
        console.log('✅ Event listener día configurado');
    }
    
    if (btnSugerir) {
        btnSugerir.addEventListener('click', function() {
            console.log('🔥 Botón Sugerir Alternativas clickeado!');
            sugerirAlternativasSimple();
        });
        console.log('✅ Event listener botón sugerir configurado');
    } else {
        console.error('❌ Botón sugerir no encontrado!');
    }
    
    // Event listeners para configuración por día
    usarConfigPorDia.addEventListener('change', toggleConfiguracionPorDia);
    diasCheckboxes.forEach(cb => cb.addEventListener('change', () => {
        if (usarConfigPorDia.checked) {
            generarConfiguracionDias();
        }
    }));



    // Validación del formulario antes de enviar
    document.getElementById('editarHorarioForm').addEventListener('submit', function(e) {
        const aulaId = document.getElementById('aula_id').value;
        const diaId = document.getElementById('dia_semana').value;
        const horaInicio = document.getElementById('hora_inicio').value;
        const horaFin = document.getElementById('hora_fin').value;
        
        if (!aulaId) {
            e.preventDefault();
            alert('Error: Debe seleccionar un aula antes de guardar.');
            document.getElementById('aula_id').focus();
            return false;
        }
        
        if (!diaId || !horaInicio || !horaFin) {
            e.preventDefault();
            alert('Error: Debe completar todos los campos de horario.');
            return false;
        }
        
        console.log('Formulario válido, enviando...');
        return true;
    });

    // Inicializar estado
    toggleConfiguracionPorDia();
    
    // Validación inicial
    validarDisponibilidad();
});

// Función principal para sugerir alternativas
function sugerirAlternativasSimple() {
    console.log('🚀 Iniciando sugerencias de alternativas...');
    
    try {
        // Buscar elementos dinámicamente
        const panel = document.getElementById('panel-sugerencias');
        const container = document.getElementById('sugerencias-container');
        
        if (!panel) {
            console.error('❌ Panel de sugerencias no encontrado');
            alert('Error: Panel de sugerencias no encontrado. La página puede no estar completamente cargada.');
            return;
        }
        
        if (!container) {
            console.error('❌ Container de sugerencias no encontrado');
            alert('Error: Container de sugerencias no encontrado. La página puede no estar completamente cargada.');
            return;
        }
        
        console.log('✅ Elementos encontrados, mostrando panel...');
        
        // Mostrar panel con animación
        panel.style.display = 'block';
        panel.scrollIntoView({ behavior: 'smooth' });
        
        // Mostrar loading mejorado
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-info mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <h5 class="text-info">Analizando Alternativas Inteligentes</h5>
                <p class="text-muted">Buscando las mejores opciones para tu horario...</p>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: 100%"></div>
                </div>
            </div>
        `;
        
        // Hacer petición a la API
        const url = '{{ route("admin.horarios.sugerencias-get", $horario) }}';
        console.log('🌐 Haciendo petición a:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('📡 Respuesta recibida:', response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status} - ${response.statusText}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('✅ Datos procesados exitosamente:', data);
            
            if (data.success) {
                mostrarSugerenciasSimples(data);
            } else {
                throw new Error(data.error || 'Error desconocido del servidor');
            }
        })
        .catch(error => {
            console.error('❌ Error en sugerencias:', error);
            
            container.innerHTML = `
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Error de Conexión</h6>
                            <p class="mb-2">${error.message}</p>
                            <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                                <i class="fas fa-refresh"></i> Recargar Página
                            </button>
                            <button class="btn btn-sm btn-outline-info ms-2" onclick="sugerirAlternativasSimple()">
                                <i class="fas fa-retry"></i> Reintentar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
    } catch (error) {
        console.error('💥 Error crítico:', error);
        alert('Error crítico: ' + error.message + '\n\nRecarga la página e inténtalo de nuevo.');
    }
}

// Función para mostrar sugerencias de forma simple
function mostrarSugerenciasSimples(data) {
    const container = document.getElementById('sugerencias-container');
    
    let html = '<div class="row">';
    
    // Mostrar contexto de la materia
    if (data.horarios_completos && data.horarios_completos.length > 0) {
        const patron = data.horarios_completos.map(h => {
            let aulaTexto = h.aula_codigo;
            if (h.tipo_clase === 'laboratorio' || h.aula_nombre.toLowerCase().includes('lab')) {
                aulaTexto = 'Lab ' + aulaTexto.replace(/lab|laboratorio/gi, '').trim();
            }
            return h.dia_nombre + ' ' + aulaTexto;
        }).join(' - ');
        
        html += `
            <div class="col-12 mb-3">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Patrón actual de la materia</h6>
                    <h5 class="text-primary mb-0">${patron}</h5>
                </div>
            </div>
        `;
    }
    
    // Mostrar sugerencias de aulas
    if (data.sugerencias.aulas && data.sugerencias.aulas.length > 0) {
        html += '<div class="col-12"><h6><i class="fas fa-door-open"></i> Sugerencias de Aulas</h6></div>';
        
        data.sugerencias.aulas.forEach(aula => {
            html += `
                <div class="col-md-6 mb-3">
                    <div class="card border-primary">
                        <div class="card-body p-3">
                            <h6 class="card-title">
                                <strong>${aula.codigo}</strong>
                                <span class="badge bg-success ms-2">${aula.compatibilidad}%</span>
                            </h6>
                            <p class="card-text mb-2">${aula.nombre}</p>
                            <small class="text-muted">
                                <i class="fas fa-users"></i> ${aula.capacidad} personas | 
                                <i class="fas fa-tag"></i> ${aula.tipo}
                            </small>
                            <div class="mt-2">
                                <small class="text-primary"><strong>${aula.descripcion || 'Cambio de aula'}</strong></small><br>
                                <small class="text-info">${aula.razon}</small>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-primary" onclick="aplicarAulaSimple(${aula.aula_id}, '${aula.codigo}')">
                                    <i class="fas fa-check"></i> Aplicar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    // Mostrar sugerencias de horarios
    if (data.sugerencias.horarios && data.sugerencias.horarios.length > 0) {
        html += '<div class="col-12 mt-3"><h6><i class="fas fa-clock"></i> Sugerencias de Horarios</h6></div>';
        
        data.sugerencias.horarios.forEach(horario => {
            html += `
                <div class="col-md-6 mb-3">
                    <div class="card border-info">
                        <div class="card-body p-3">
                            <h6 class="card-title">
                                <strong>${horario.dia_nombre}</strong>
                                <span class="badge bg-info ms-2">${horario.preferencia}%</span>
                            </h6>
                            <p class="card-text">
                                <i class="fas fa-clock"></i> ${horario.hora_inicio} - ${horario.hora_fin}
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-door-open"></i> ${horario.aulas_disponibles} aulas disponibles
                            </small>
                            <div class="mt-2">
                                <small class="text-primary">${horario.razon_preferencia}</small>
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-info" onclick="aplicarHorarioSimple(${horario.dia}, '${horario.dia_nombre}', '${horario.hora_inicio}', '${horario.hora_fin}')">
                                    <i class="fas fa-check"></i> Aplicar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    html += '</div>';
    
    if (!data.sugerencias.aulas?.length && !data.sugerencias.horarios?.length) {
        html = '<div class="alert alert-info">No se encontraron alternativas disponibles en este momento.</div>';
    }
    
    container.innerHTML = html;
}

// Función para aplicar aula simple
function aplicarAulaSimple(aulaId, aulaCodigo) {
    const aulaSelect = document.getElementById('aula_id');
    aulaSelect.value = aulaId;
    
    if (aulaSelect.value == aulaId) {
        alert('✅ Aula cambiada a: ' + aulaCodigo);
    } else {
        alert('❌ Error al cambiar aula');
    }
}

// Función para aplicar horario simple
function aplicarHorarioSimple(dia, diaNombre, horaInicio, horaFin) {
    document.getElementById('dia_semana').value = dia;
    document.getElementById('hora_inicio').value = horaInicio;
    document.getElementById('hora_fin').value = horaFin;
    
    // Calcular duración
    const inicio = new Date('2000-01-01 ' + horaInicio);
    const fin = new Date('2000-01-01 ' + horaFin);
    const diferencia = (fin - inicio) / (1000 * 60 * 60);
    
    const duracionDisplay = document.getElementById('duracion_display');
    const duracionHoras = document.getElementById('duracion_horas');
    
    if (duracionDisplay && duracionHoras) {
        duracionDisplay.value = diferencia.toFixed(2) + ' horas';
        duracionHoras.value = diferencia.toFixed(2);
    }
    
    alert('✅ Horario cambiado a: ' + diaNombre + ' ' + horaInicio + '-' + horaFin);
}

// Funciones de debug eliminadas para limpiar la interfaz

// CU-12: Función de Verificación de Cambios
function verificarCambiosCU12() {
    console.log('🔍 CU-12: Iniciando verificación de cambios...');
    
    // Mostrar panel de validación
    const panel = document.getElementById('panel-validacion-cu12');
    const card = document.getElementById('card-validacion');
    const header = document.getElementById('header-validacion');
    const titulo = document.getElementById('titulo-validacion');
    const body = document.getElementById('body-validacion');
    
    panel.style.display = 'block';
    card.className = 'card border-warning';
    header.className = 'card-header bg-warning text-dark';
    titulo.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validando Cambios...';
    
    body.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Validando...</span>
            </div>
            <p class="mt-2">Ejecutando CU-11: Validación de Cruces...</p>
            <small class="text-muted">Verificando nueva tripleta: Docente + Aula + Franja Horaria</small>
        </div>
    `;
    
    // Obtener datos del formulario
    const datosFormulario = {
        carga_academica_id: document.getElementById('carga_academica_id').value,
        aula_id: document.getElementById('aula_id').value,
        dia_semana: document.getElementById('dia_semana').value,
        hora_inicio: document.getElementById('hora_inicio').value,
        hora_fin: document.getElementById('hora_fin').value,
        tipo_clase: document.getElementById('tipo_clase').value,
        periodo_academico: document.getElementById('periodo_academico').value || '{{ $horario->periodo_academico }}',
        excluir_id: {{ $horario->id }}
    };
    
    console.log('📋 Datos a validar:', datosFormulario);
    
    // Validar que todos los campos estén completos
    if (!datosFormulario.aula_id || !datosFormulario.dia_semana || !datosFormulario.hora_inicio || !datosFormulario.hora_fin) {
        mostrarResultadoValidacion('error', 'Campos Incompletos', 'Complete todos los campos antes de verificar los cambios.');
        return;
    }
    
    // Llamar a CU-11: Validación de Disponibilidad
    fetch('{{ route("admin.horarios.validar-disponibilidad") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(datosFormulario)
    })
    .then(response => {
        console.log('📡 Respuesta de validación:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('✅ Resultado de validación:', data);
        
        if (data.disponible) {
            // Sin conflictos - Permitir guardar
            mostrarResultadoValidacion('success', 'Validación Exitosa: Sin Conflictos', 
                `La nueva asignación está completamente libre.<br>
                <strong>Nueva Tripleta Validada:</strong><br>
                • Docente: ${data.mensaje || 'Disponible'}<br>
                • Aula: ${getAulaTexto()}<br>
                • Franja: ${getDiaTexto()} ${datosFormulario.hora_inicio}-${datosFormulario.hora_fin}`
            );
            
            // Habilitar botón de guardar
            document.getElementById('btn-guardar-cambios').disabled = false;
            document.getElementById('btn-guardar-cambios').innerHTML = '<i class="fas fa-save"></i> Guardar Cambios Validados';
            
        } else {
            // Conflictos detectados - Bloquear guardado
            let mensajesError = [];
            let tipoConflicto = '';
            
            if (data.conflictos && data.conflictos.profesor) {
                mensajesError.push(`🚫 <strong>Conflicto de Docente:</strong> ${data.conflictos.profesor.mensaje}`);
                tipoConflicto = 'Docente';
            }
            
            if (data.conflictos && data.conflictos.aula) {
                mensajesError.push(`🚫 <strong>Conflicto de Aula:</strong> ${data.conflictos.aula.mensaje}`);
                tipoConflicto = tipoConflicto ? 'Docente y Aula' : 'Aula';
            }
            
            const mensajeFinal = mensajesError.length > 0 ? mensajesError.join('<br>') : data.mensaje;
            
            mostrarResultadoValidacion('error', `Error: Conflicto de ${tipoConflicto}`, 
                `${mensajeFinal}<br><br>
                <strong>💡 Solución:</strong> Ajuste la hora o elija otro recurso.`
            );
            
            // Mantener botón de guardar deshabilitado
            document.getElementById('btn-guardar-cambios').disabled = true;
        }
    })
    .catch(error => {
        console.error('❌ Error en validación:', error);
        mostrarResultadoValidacion('error', 'Error de Validación', 
            `Error al conectar con el servidor: ${error.message}`
        );
    });
}

// Función auxiliar para mostrar resultado de validación
function mostrarResultadoValidacion(tipo, titulo, mensaje) {
    const card = document.getElementById('card-validacion');
    const header = document.getElementById('header-validacion');
    const tituloEl = document.getElementById('titulo-validacion');
    const body = document.getElementById('body-validacion');
    
    if (tipo === 'success') {
        card.className = 'card border-success';
        header.className = 'card-header bg-success text-white';
        tituloEl.innerHTML = `<i class="fas fa-check-circle"></i> ${titulo}`;
        body.innerHTML = `
            <div class="alert alert-success mb-0">
                <p class="mb-0">${mensaje}</p>
            </div>
        `;
    } else {
        card.className = 'card border-danger';
        header.className = 'card-header bg-danger text-white';
        tituloEl.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${titulo}`;
        body.innerHTML = `
            <div class="alert alert-danger mb-0">
                <p class="mb-0">${mensaje}</p>
            </div>
        `;
    }
}

// Funciones auxiliares para obtener texto legible
function getAulaTexto() {
    const aulaSelect = document.getElementById('aula_id');
    const selectedOption = aulaSelect.options[aulaSelect.selectedIndex];
    return selectedOption ? selectedOption.text : 'N/A';
}

function getDiaTexto() {
    const diaSelect = document.getElementById('dia_semana');
    const dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    return dias[parseInt(diaSelect.value)] || 'N/A';
}

// Función testRutaSimple eliminada para limpiar la interfaz
</script>

<style>
.list-group-item {
    border: 1px solid #dee2e6;
    margin-bottom: 5px;
    border-radius: 5px;
}

.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

.card-header {
    position: relative;
}

.card-header .btn-group {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
}

#panel-sugerencias, #panel-reasignacion {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.badge {
    font-size: 0.8em;
}

.table-responsive {
    max-height: 400px;
    overflow-y: auto;
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Estilos para tabla visual de horarios */
.celda-horario {
    height: 60px;
    vertical-align: middle;
    text-align: center;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.celda-horario:hover {
    background-color: #f8f9fa;
}

.celda-horario.ocupada {
    background-color: #e3f2fd;
    border: 2px solid #2196f3;
}

.celda-horario.actual {
    background-color: #fff3e0;
    border: 2px solid #ff9800;
}

.celda-horario.sugerencia {
    background-color: #e8f5e8;
    border: 2px solid #4caf50;
}

.horario-item {
    padding: 4px;
    border-radius: 4px;
    font-size: 0.8em;
    line-height: 1.2;
}

.horario-item.actual {
    background-color: rgba(255, 152, 0, 0.1);
    color: #e65100;
}

.horario-item.sugerencia {
    background-color: rgba(76, 175, 80, 0.1);
    color: #2e7d32;
}

.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
    line-height: 1.2;
    border-radius: 0.2rem;
}

#tabla-horarios-semanal {
    font-size: 0.9em;
}

#tabla-horarios-semanal th {
    text-align: center;
    font-weight: bold;
}

.table-bordered td {
    border: 1px solid #dee2e6;
}

/* Animaciones */
.horario-item {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<!-- Modal para notificaciones -->
<div class="modal fade" id="notificacionModal" tabindex="-1" aria-labelledby="notificacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="modal-header">
                <h5 class="modal-title" id="notificacionModalLabel">
                    <i id="modal-icon" class="fas fa-info-circle"></i>
                    <span id="modal-title">Notificación</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body">
                <p id="modal-message">Mensaje de notificación</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="modal-action-btn" style="display: none;">Acción</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmaciones -->
<div class="modal fade" id="confirmacionModal" tabindex="-1" aria-labelledby="confirmacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="confirmacionModalLabel">
                    <i class="fas fa-question-circle"></i> Confirmación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmacion-body">
                <p id="confirmacion-message">¿Está seguro de realizar esta acción?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmacion-btn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

@endsection