<!--  -->@extends('layouts.dashboard')

@section('title', 'Editar Horario')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <h1 class="h2 mb-0">
                <i class="fas fa-edit"></i> Editar Horario
            </h1>
            <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
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
                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Información de la Materia</h6>
                <span class="badge bg-light text-dark">
                    @php
                        $horariosMateria = \App\Models\Horario::where('carga_academica_id', $horario->carga_academica_id)
                            ->with(['aula'])
                            ->orderBy('hora_inicio')
                            ->get();
                    @endphp
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

<!-- Información del Horario Actual -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Información del Horario</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>📚 Materia:</strong><br>
                        <span class="badge bg-info fs-6">{{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>👨‍🏫 Profesor:</strong><br>
                        <span class="badge bg-success fs-6">{{ $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>👥 Grupo:</strong><br>
                        <span class="badge bg-secondary fs-6">{{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>🏢 Aula Actual:</strong><br>
                        <span class="badge bg-warning text-dark fs-6">{{ $horario->aula->codigo_aula ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario de Edición -->
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-cogs"></i> Modificar Horario
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
            </div>

            <!-- Configuración de Múltiples Días -->
            <div class="card border-info mt-4" id="seccion-multiples-dias">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-calendar-plus"></i> Configuración de Múltiples Días por Semana</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="usar_configuracion_por_dia" 
                               name="usar_configuracion_por_dia" onchange="toggleConfiguracionMultipleDias()">
                        <label class="form-check-label text-white" for="usar_configuracion_por_dia">
                            Activar Múltiples Días
                        </label>
                    </div>
                </div>
                <div class="card-body" id="configuracion-multiples-dias" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Configuración Avanzada:</strong> Permite que esta materia tenga clases en múltiples días de la semana, 
                        cada uno con su propia aula y tipo de clase. Por ejemplo: Lunes y Miércoles en Aula A101 (Teórica), 
                        Viernes en LAB-B205 (Laboratorio).
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label"><strong>Seleccionar Días de la Semana:</strong></label>
                            <div class="row">
                                @php $diasSemana = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo']; @endphp
                                @foreach($diasSemana as $numero => $nombre)
                                <div class="col-md-3 col-6">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" 
                                               id="dia_{{ $numero }}" value="{{ $numero }}" 
                                               onchange="actualizarConfiguracionDias()">
                                        <label class="form-check-label" for="dia_{{ $numero }}">
                                            {{ $nombre }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div id="configuraciones-por-dia">
                        <!-- Las configuraciones por día se generarán dinámicamente aquí -->
                    </div>
                    
                    <div class="alert alert-warning mt-3" id="alerta-multiples-dias" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Importante:</strong> Cada día seleccionado debe tener su aula y tipo de clase configurados.
                        El sistema validará que no haya conflictos en ninguno de los días seleccionados.
                    </div>
                </div>
            </div>

            <!-- Botones de Validación y Guardado -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                
                <!-- Verificar Cambios -->
                <button type="button" class="btn btn-warning me-md-2" id="btn-verificar-cambios" onclick="verificarCambios()">
                    <i class="fas fa-search"></i> Verificar Cambios
                </button>
                
                <!-- Guardar Cambios -->
                <button type="submit" class="btn btn-success" id="btn-guardar-cambios" disabled>
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>

            <!-- Panel de Validación -->
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
                                <p class="mt-2">Verificando disponibilidad...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Panel de Sugerencias -->
<div class="row mt-4" id="panel-sugerencias" style="display: none;">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb"></i> Sugerencias Inteligentes
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

<!-- TODOS LOS HORARIOS DEL SISTEMA - Para Comparar Disponibilidad -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-calendar-check"></i> TODOS LOS HORARIOS DEL SISTEMA - Comparar Disponibilidad</h6>
                <div>
                    <button type="button" class="btn btn-sm btn-warning" onclick="verificarEnTabla()" title="Verificar conflictos en la tabla">
                        <i class="fas fa-search"></i> Verificar en Tabla
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="filtrarPorAula()">
                        <i class="fas fa-filter"></i> Filtrar por Aula
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="mostrarTodos()">
                        <i class="fas fa-eye"></i> Mostrar Todos
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarResaltados()">
                        <i class="fas fa-eraser"></i> Limpiar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Instrucciones:</strong> Usa esta tabla para verificar manualmente si un aula está ocupada en el horario que deseas usar. 
                    El horario que estás editando (ID: {{ $horario->id }}) está marcado en <span class="badge bg-success">VERDE</span>.
                </div>
                
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Filtrar por Aula:</label>
                        <select class="form-select form-select-sm" id="filtro-aula" onchange="aplicarFiltros()">
                            <option value="">Todas las aulas</option>
                            @php
                                $aulasUnicas = \App\Models\Aula::orderBy('codigo_aula')->get();
                            @endphp
                            @foreach($aulasUnicas as $aulaUnica)
                                <option value="{{ $aulaUnica->id }}">{{ $aulaUnica->codigo_aula }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filtrar por Día:</label>
                        <select class="form-select form-select-sm" id="filtro-dia" onchange="aplicarFiltros()">
                            <option value="">Todos los días</option>
                            <option value="1">Lunes</option>
                            <option value="2">Martes</option>
                            <option value="3">Miércoles</option>
                            <option value="4">Jueves</option>
                            <option value="5">Viernes</option>
                            <option value="6">Sábado</option>
                            <option value="7">Domingo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filtrar por Profesor:</label>
                        <select class="form-select form-select-sm" id="filtro-profesor" onchange="aplicarFiltros()">
                            <option value="">Todos los profesores</option>
                            @php
                                $profesoresUnicos = \App\Models\Profesor::orderBy('nombre')->orderBy('apellido')->get();
                            @endphp
                            @foreach($profesoresUnicos as $profesorUnico)
                                <option value="{{ $profesorUnico->id }}">{{ $profesorUnico->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Buscar:</label>
                        <input type="text" class="form-control form-control-sm" id="buscar-texto" placeholder="Buscar..." onkeyup="aplicarFiltros()">
                    </div>
                </div>
                
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-sm table-hover" id="tabla-horarios-completa">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>ID</th>
                                <th>Día</th>
                                <th>Horario</th>
                                <th>Aula</th>
                                <th>Profesor</th>
                                <th>Materia</th>
                                <th>Grupo</th>
                                <th>Período</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $todosLosHorarios = \App\Models\Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
                                    ->where('periodo_academico', $horario->periodo_academico)
                                    ->orderBy('hora_inicio')
                                    ->orderBy('aula_id')
                                    ->get();
                                    
                                $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                $diasCortos = ['', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                            @endphp
                            @forelse($todosLosHorarios as $horarioExistente)
                            <tr class="fila-horario" 
                                data-aula-id="{{ $horarioExistente->aula_id }}"
                                data-dia="{{ $horarioExistente->dia_semana }}"
                                data-profesor-id="{{ $horarioExistente->cargaAcademica->profesor->id ?? '' }}"
                                data-texto="{{ strtolower($horarioExistente->cargaAcademica->grupo->materia->nombre ?? '') }} {{ strtolower($horarioExistente->cargaAcademica->profesor->nombre_completo ?? '') }} {{ strtolower($horarioExistente->aula->codigo_aula ?? '') }}"
                                @if($horarioExistente->id == $horario->id) style="background-color: #d4edda; border-left: 4px solid #28a745;" @endif>
                                <td>
                                    @if($horarioExistente->id == $horario->id)
                                        <span class="badge bg-success">{{ $horarioExistente->id }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $horarioExistente->id }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $diasCortos[$horarioExistente->dia_semana] ?? 'N/A' }}</span>
                                    <br><small class="text-muted">{{ $dias[$horarioExistente->dia_semana] ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <strong>{{ $horarioExistente->hora_inicio }}</strong>
                                    <br><small class="text-muted">{{ $horarioExistente->hora_fin }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info fs-6">{{ $horarioExistente->aula->codigo_aula ?? 'N/A' }}</span>
                                    <br><small class="text-muted">ID: {{ $horarioExistente->aula_id }}</small>
                                </td>
                                <td>
                                    <strong>{{ $horarioExistente->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $horarioExistente->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $horarioExistente->cargaAcademica->grupo->nombre ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $horarioExistente->periodo_academico }}</span>
                                </td>
                                <td>
                                    @if($horarioExistente->id == $horario->id)
                                        <span class="badge bg-success"><i class="fas fa-edit"></i> EDITANDO</span>
                                    @else
                                        <span class="badge bg-light text-dark"><i class="fas fa-check"></i> Ocupado</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No hay horarios registrados en el sistema</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Total de horarios mostrados: <span id="contador-horarios">{{ $todosLosHorarios->count() }}</span> | 
                        Período académico: <strong>{{ $horario->periodo_academico }}</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función simple para verificar cambios
// Función de Verificación de Cambios
function verificarCambios() {
    console.log('🔍 Iniciando verificación de cambios...');
    
    // Validar configuración de múltiples días primero
    if (!validarConfiguracionMultipleDias()) {
        return;
    }
    
    // Obtener datos del formulario
    const usarMultiplesDias = document.getElementById('usar_configuracion_por_dia').checked;
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    const cargaAcademicaId = document.getElementById('carga_academica_id').value;
    
    let aulaId, diaSemana, tipoClase;
    
    if (usarMultiplesDias) {
        // En modo múltiples días, validar cada día por separado
        const diasSeleccionados = document.querySelectorAll('.dia-checkbox:checked');
        
        if (diasSeleccionados.length === 0) {
            alert('Selecciona al menos un día de la semana.');
            return;
        }
        
        // Para la validación inicial, usar el primer día seleccionado
        const primerDia = diasSeleccionados[0].value;
        aulaId = document.querySelector(`select[name="config_dias[${primerDia}][aula_id]"]`).value;
        diaSemana = primerDia;
        tipoClase = document.querySelector(`select[name="config_dias[${primerDia}][tipo_clase]"]`).value;
        
        console.log('🗓️ Modo múltiples días activado - validando primer día:', {
            dia: primerDia,
            aula: aulaId,
            tipo: tipoClase,
            total_dias: diasSeleccionados.length
        });
        
    } else {
        // Modo simple
        aulaId = document.getElementById('aula_id').value;
        diaSemana = document.getElementById('dia_semana').value;
        tipoClase = document.getElementById('tipo_clase').value;
    }
    
    // Validar campos obligatorios
    if (!aulaId || !diaSemana || !horaInicio || !horaFin || !cargaAcademicaId) {
        alert('Por favor complete todos los campos antes de verificar.');
        return;
    }
    
    // Mostrar panel de validación con información detallada
    const panel = document.getElementById('panel-validacion-cu12');
    if (panel) {
        panel.style.display = 'block';
        document.getElementById('titulo-validacion').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ejecutando CU-11: Validación de Cruces...';
        document.getElementById('body-validacion').innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Validando...</span>
                </div>
                <p class="mt-2">Ejecutando CU-11: Validación de Cruces...</p>
                <small class="text-muted">Verificando nueva tripleta: Docente + Aula + Franja Horaria</small>
            </div>
        `;
    }
    
    // Obtener información de la nueva tripleta para mostrar
    const aulaSelect = document.getElementById('aula_id');
    const aulaTexto = aulaSelect.options[aulaSelect.selectedIndex]?.text || 'N/A';
    const dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    const diaTexto = dias[parseInt(diaSemana)] || 'N/A';
    
    // Preparar datos para CU-11
    const datos = {
        carga_academica_id: cargaAcademicaId,
        aula_id: aulaId,
        dia_semana: diaSemana,
        hora_inicio: horaInicio,
        hora_fin: horaFin,
        tipo_clase: tipoClase,
        periodo_academico: document.getElementById('periodo_academico')?.value || '{{ $horario->periodo_academico }}',
        excluir_id: {{ $horario->id }}
    };
    
    console.log('📋 Nueva tripleta a validar:', {
        docente: 'Desde carga académica',
        aula: aulaTexto,
        franja: `${diaTexto} ${horaInicio}-${horaFin}`
    });
    
    // Ejecutar CU-11: Validación de Disponibilidad
    fetch('{{ route("admin.horarios.validar-disponibilidad") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(datos)
    })
    .then(response => {
        console.log('📡 Respuesta de CU-11:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('✅ Resultado de CU-11:', data);
        
        // ALERTA VISIBLE PARA DEBUG
        const estadoTexto = data.disponible ? '✅ DISPONIBLE' : '❌ OCUPADA';
        const aulaTexto = document.getElementById('aula_id').options[document.getElementById('aula_id').selectedIndex]?.text || 'N/A';
        const diaTexto = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][parseInt(document.getElementById('dia_semana').value)] || 'N/A';
        const horaTexto = document.getElementById('hora_inicio').value + '-' + document.getElementById('hora_fin').value;
        
        console.log(`🔍 RESULTADO VERIFICACIÓN: ${estadoTexto}`);
        console.log(`🏢 Aula: ${aulaTexto}`);
        console.log(`📅 Día: ${diaTexto}`);
        console.log(`⏰ Horario: ${horaTexto}`);
        
        if (!data.disponible) {
            console.log('🚫 RAZÓN DEL CONFLICTO:', data.mensaje);
            
            // Mostrar alerta detallada de conflicto
            let detallesConflicto = '';
            if (data.conflictos) {
                if (data.conflictos.aula) {
                    detallesConflicto += `<br><strong>Conflicto de Aula:</strong> ${data.conflictos.aula.detalle_especifico}`;
                }
                if (data.conflictos.profesor) {
                    detallesConflicto += `<br><strong>Conflicto de Profesor:</strong> ${data.conflictos.profesor.detalle_especifico}`;
                }
            }
            
            window.mostrarAlertaDebug(`
                <strong>❌ CONFLICTO DETECTADO</strong><br>
                <strong>Aula:</strong> ${aulaTexto}<br>
                <strong>Día:</strong> ${diaTexto}<br>
                <strong>Horario:</strong> ${horaTexto}<br>
                <strong>Datos enviados:</strong><br>
                - Aula ID: ${aulaId}<br>
                - Día: ${diaSemana}<br>
                - Excluir ID: {{ $horario->id }}<br>
                ${detallesConflicto}
            `, 'danger');
            
            // Resaltar conflictos en la tabla
            resaltarConflictosEnTabla();
            
            // Filtrar automáticamente por el aula conflictiva
            document.getElementById('filtro-aula').value = aulaId;
            aplicarFiltros();
            
        } else {
            // Mostrar alerta detallada de disponibilidad
            window.mostrarAlertaDebug(`
                <strong>✅ HORARIO DISPONIBLE</strong><br>
                <strong>Aula:</strong> ${aulaTexto}<br>
                <strong>Día:</strong> ${diaTexto}<br>
                <strong>Horario:</strong> ${horaTexto}<br>
                <strong>Datos enviados:</strong><br>
                - Aula ID: ${aulaId}<br>
                - Día: ${diaSemana}<br>
                - Excluir ID: {{ $horario->id }}<br>
                <strong>✅ Sin conflictos encontrados</strong>
            `, 'success');
            
            // Limpiar resaltados previos
            limpiarResaltados();
        }
        
        if (data.disponible) {
            // Sin conflictos - Permitir guardar
            mostrarResultadoValidacion('success', 'Validación Exitosa: Sin Conflictos', 
                `La nueva asignación está completamente libre.<br>
                <strong>Nueva Tripleta Validada:</strong><br>
                • Docente: ${data.mensaje || 'Disponible'}<br>
                • Aula: ${aulaTexto}<br>
                • Franja: ${diaTexto} ${horaInicio}-${horaFin}`,
                data
            );
            
            // Habilitar botón de guardar y cambiar apariencia
            const btnGuardar = document.getElementById('btn-guardar-cambios');
            if (btnGuardar) {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios Validados';
                btnGuardar.classList.remove('btn-danger', 'btn-secondary');
                btnGuardar.classList.add('btn-success');
                btnGuardar.title = 'Los cambios han sido validados y se pueden guardar';
            }
            
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
                <strong>💡 Solución:</strong> Ajuste la hora o elija otro recurso.`,
                data
            );
            
            // Mantener botón de guardar deshabilitado y cambiar apariencia
            const btnGuardar = document.getElementById('btn-guardar-cambios');
            if (btnGuardar) {
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<i class="fas fa-ban"></i> No se puede Guardar (Hay Conflictos)';
                btnGuardar.classList.remove('btn-success', 'btn-primary');
                btnGuardar.classList.add('btn-danger');
                btnGuardar.title = 'Resuelve los conflictos antes de guardar';
            }
        }
    })
    .catch(error => {
        console.error('❌ Error en CU-11:', error);
        mostrarResultadoValidacion('error', 'Error de Validación', 
            `Error al conectar con el servidor: ${error.message}`);
    });
}

// Función para mostrar resultados detallados de validación
function mostrarResultadoValidacion(tipo, titulo, mensaje, data = null) {
    const panel = document.getElementById('panel-validacion-cu12');
    const card = document.getElementById('card-validacion');
    const header = document.getElementById('header-validacion');
    const tituloEl = document.getElementById('titulo-validacion');
    const body = document.getElementById('body-validacion');
    
    if (!panel) {
        alert(titulo + ': ' + mensaje.replace(/<[^>]*>/g, ''));
        return;
    }
    
    panel.style.display = 'block';
    
    if (tipo === 'success') {
        card.className = 'card border-success';
        header.className = 'card-header bg-success text-white';
        tituloEl.innerHTML = '<i class="fas fa-check-circle"></i> ' + titulo;
        body.innerHTML = `
            <div class="alert alert-success mb-0">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-2">${mensaje}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <small class="text-muted">CU-11 Ejecutado</small><br>
                        <span class="badge bg-success">✓ Validado</span>
                    </div>
                </div>
            </div>
        `;
    } else {
        card.className = 'card border-danger';
        header.className = 'card-header bg-danger text-white';
        tituloEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + titulo;
        
        let detallesHTML = '';
        if (data && data.conflictos) {
            detallesHTML = '<div class="mt-3">';
            
            if (data.conflictos.profesor) {
                const prof = data.conflictos.profesor;
                detallesHTML += `
                    <div class="card border-warning mb-2">
                        <div class="card-header bg-warning text-dark py-2">
                            <strong><i class="fas fa-user"></i> CONFLICTO DE DOCENTE</strong>
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>📚 Materia en conflicto:</strong><br>
                                    <span class="badge bg-danger">${prof.materia_conflicto || 'N/A'}</span>
                                    ${prof.grupo_conflicto ? '<span class="badge bg-secondary ms-1">Grupo ' + prof.grupo_conflicto + '</span>' : ''}
                                </div>
                                <div class="col-md-6">
                                    <strong>🏢 Aula ocupada:</strong><br>
                                    <span class="badge bg-info">${prof.aula_conflicto || 'N/A'}</span>
                                    <br><strong>⏰ Horario:</strong> ${prof.horario_conflicto || 'N/A'}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            if (data.conflictos.aula) {
                const aula = data.conflictos.aula;
                detallesHTML += `
                    <div class="card border-danger mb-2">
                        <div class="card-header bg-danger text-white py-2">
                            <strong><i class="fas fa-door-open"></i> CONFLICTO DE AULA</strong>
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>📚 Materia que ocupa:</strong><br>
                                    <span class="badge bg-danger">${aula.materia_conflicto || 'N/A'}</span>
                                    ${aula.grupo_conflicto ? '<span class="badge bg-secondary ms-1">Grupo ' + aula.grupo_conflicto + '</span>' : ''}
                                </div>
                                <div class="col-md-6">
                                    <strong>👨‍🏫 Profesor que ocupa:</strong><br>
                                    <span class="badge bg-warning text-dark">${aula.profesor_conflicto || 'N/A'}</span>
                                    <br><strong>⏰ Horario:</strong> ${aula.horario_conflicto || 'N/A'}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            detallesHTML += '</div>';
        }
        
        body.innerHTML = `
            <div class="alert alert-danger mb-0">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-2">${mensaje}</p>
                        ${detallesHTML}
                    </div>
                    <div class="col-md-4 text-end">
                        <small class="text-muted">CU-11 Ejecutado</small><br>
                        <span class="badge bg-danger">✗ Conflicto</span>
                        ${data && data.total_conflictos ? `<br><small class="text-muted">${data.total_conflictos} conflicto(s)</small>` : ''}
                    </div>
                </div>
            </div>
        `;
    }
}

// Función para mostrar resultados (compatibilidad)
function mostrarResultado(tipo, titulo, mensaje) {
    const panel = document.getElementById('panel-validacion-cu12');
    const card = document.getElementById('card-validacion');
    const header = document.getElementById('header-validacion');
    const tituloEl = document.getElementById('titulo-validacion');
    const body = document.getElementById('body-validacion');
    
    if (!panel) {
        alert(titulo + ': ' + mensaje);
        return;
    }
    
    panel.style.display = 'block';
    
    if (tipo === 'success') {
        card.className = 'card border-success';
        header.className = 'card-header bg-success text-white';
        tituloEl.innerHTML = '<i class="fas fa-check-circle"></i> ' + titulo;
        body.innerHTML = '<div class="alert alert-success mb-0">' + mensaje + '</div>';
    } else {
        card.className = 'card border-danger';
        header.className = 'card-header bg-danger text-white';
        tituloEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + titulo;
        body.innerHTML = '<div class="alert alert-danger mb-0">' + mensaje + '</div>';
    }
}

// Función para sugerir alternativas
function mostrarSugerencias() {
    console.log('💡 Mostrando sugerencias...');
    const panel = document.getElementById('panel-sugerencias');
    if (panel) {
        panel.style.display = 'block';
        const container = document.getElementById('sugerencias-container');
        if (container) {
            container.innerHTML = '<div class="text-center"><div class="spinner-border"></div><p>Cargando sugerencias...</p></div>';
            
            // Hacer petición para sugerencias
            fetch('{{ route("admin.horarios.sugerencias-get", $horario) }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let html = '<div class="alert alert-info">Se encontraron alternativas disponibles.</div>';
                    if (data.sugerencias && data.sugerencias.aulas) {
                        html += '<h6>Aulas Alternativas:</h6><ul>';
                        data.sugerencias.aulas.forEach(aula => {
                            html += '<li>' + (aula.codigo || 'N/A') + ' - ' + (aula.nombre || 'Sin nombre') + '</li>';
                        });
                        html += '</ul>';
                    }
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="alert alert-warning">No se encontraron sugerencias disponibles.</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div class="alert alert-danger">Error al cargar sugerencias.</div>';
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 CU-12: DOM cargado, inicializando elementos...');
    console.log('📋 Contexto: Modificando registro específico de carga académica multiregistro');
    
    // Configurar botón de sugerencias
    const btnSugerir = document.getElementById('btn-sugerir-alternativas');
    if (btnSugerir) {
        btnSugerir.addEventListener('click', function(e) {
            e.preventDefault();
            mostrarSugerencias();
        });
    }
    
    // Función para calcular duración
    function calcularDuracion() {
        const horaInicio = document.getElementById('hora_inicio');
        const horaFin = document.getElementById('hora_fin');
        const duracionDisplay = document.getElementById('duracion_display');
        const duracionHoras = document.getElementById('duracion_horas');
        
        if (horaInicio && horaFin && horaInicio.value && horaFin.value) {
            const inicio = new Date('2000-01-01 ' + horaInicio.value);
            const fin = new Date('2000-01-01 ' + horaFin.value);
            
            if (fin > inicio) {
                const diferencia = (fin - inicio) / (1000 * 60 * 60);
                if (duracionDisplay) duracionDisplay.value = diferencia.toFixed(2) + ' horas';
                if (duracionHoras) duracionHoras.value = diferencia.toFixed(2);
            }
        }
    }
    
    // Event listeners para calcular duración
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    if (horaInicio) horaInicio.addEventListener('change', calcularDuracion);
    if (horaFin) horaFin.addEventListener('change', calcularDuracion);
    
    // Calcular duración inicial
    calcularDuracion();
    
    console.log('✅ Inicialización completada');
    
    // Función para mostrar alerta visual en la página
    window.mostrarAlertaDebug = function(mensaje, tipo = 'info') {
        const alertaExistente = document.getElementById('alerta-debug');
        if (alertaExistente) {
            alertaExistente.remove();
        }
        
        const alerta = document.createElement('div');
        alerta.id = 'alerta-debug';
        alerta.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
        alerta.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        alerta.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alerta);
        
        // Auto-remover después de 8 segundos
        setTimeout(() => {
            if (alerta && alerta.parentNode) {
                alerta.remove();
            }
        }, 8000);
    };
    
    // Función de debug global
    window.debugValidacion = function() {
        const aulaId = document.getElementById('aula_id').value;
        const aulaTexto = document.getElementById('aula_id').options[document.getElementById('aula_id').selectedIndex]?.text;
        const dia = document.getElementById('dia_semana').value;
        const horaInicio = document.getElementById('hora_inicio').value;
        const horaFin = document.getElementById('hora_fin').value;
        
        const mensaje = `
            <strong>🔍 DEBUG VALIDACIÓN</strong><br>
            <strong>Aula:</strong> ${aulaTexto} (ID: ${aulaId})<br>
            <strong>Día:</strong> ${dia}<br>
            <strong>Horario:</strong> ${horaInicio}-${horaFin}<br>
            <strong>Excluir ID:</strong> {{ $horario->id }}
        `;
        
        mostrarAlertaDebug(mensaje, 'info');
        
        console.log('🔍 DEBUG: Estado actual del formulario');
        console.log('- Aula ID:', aulaId);
        console.log('- Aula texto:', aulaTexto);
        console.log('- Día:', dia);
        console.log('- Hora inicio:', horaInicio);
        console.log('- Hora fin:', horaFin);
        console.log('- Horario ID a excluir:', {{ $horario->id }});
        
        // Ejecutar verificación
        verificarCambios();
    };
    
    console.log('💡 Usa window.debugValidacion() en la consola para debuggear');
    console.log('💡 Usa window.mostrarAlertaDebug("mensaje", "tipo") para mostrar alertas');
    console.log('💡 Usa window.verLogsValidacion() para ver los logs del servidor');
    console.log('💡 Usa window.probarGuardado() para probar el guardado directamente');
    
    // Inicializar configuración de múltiples días si ya está activada
    const horarioActual = {
        dia_semana: {{ $horario->dia_semana }},
        aula_id: {{ $horario->aula_id }},
        tipo_clase: '{{ $horario->tipo_clase }}',
        usar_configuracion_por_dia: {{ $horario->usar_configuracion_por_dia ? 'true' : 'false' }},
        configuracion_dias: @json($horario->configuracion_dias ?? [])
    };
    
    if (horarioActual.usar_configuracion_por_dia) {
        document.getElementById('usar_configuracion_por_dia').checked = true;
        toggleConfiguracionMultipleDias();
        cargarConfiguracionExistente();
    }
    
    // Función para probar el guardado directamente
    window.probarGuardado = function() {
        const aulaId = document.getElementById('aula_id').value;
        const aulaTexto = document.getElementById('aula_id').options[document.getElementById('aula_id').selectedIndex]?.text;
        const dia = document.getElementById('dia_semana').value;
        const horaInicio = document.getElementById('hora_inicio').value;
        const horaFin = document.getElementById('hora_fin').value;
        const cargaAcademicaId = document.getElementById('carga_academica_id').value;
        
        if (!aulaId || !dia || !horaInicio || !horaFin || !cargaAcademicaId) {
            window.mostrarAlertaDebug(`
                <strong>⚠️ CAMPOS INCOMPLETOS</strong><br>
                Por favor completa todos los campos antes de probar el guardado
            `, 'warning');
            return;
        }
        
        const dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        const diaTexto = dias[parseInt(dia)] || 'N/A';
        
        console.log('🧪 PROBANDO GUARDADO DIRECTO');
        console.log('Datos que se enviarían:', {
            carga_academica_id: cargaAcademicaId,
            aula_id: aulaId,
            dia_semana: dia,
            hora_inicio: horaInicio,
            hora_fin: horaFin,
            tipo_clase: document.getElementById('tipo_clase').value,
            duracion_horas: document.getElementById('duracion_horas').value,
            periodo_academico: document.getElementById('periodo_academico').value
        });
        
        window.mostrarAlertaDebug(`
            <strong>🧪 PRUEBA DE GUARDADO</strong><br>
            <strong>Aula:</strong> ${aulaTexto} (ID: ${aulaId})<br>
            <strong>Día:</strong> ${diaTexto}<br>
            <strong>Horario:</strong> ${horaInicio}-${horaFin}<br>
            <strong>Acción:</strong> Haz clic en "Guardar Cambios" para probar<br>
            <small>Los datos están listos para enviar</small>
        `, 'info');
        
        // Habilitar el botón de guardar si está deshabilitado
        const btnGuardar = document.getElementById('btn-guardar-cambios');
        if (btnGuardar) {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios (Listo para Probar)';
            btnGuardar.classList.remove('btn-secondary');
            btnGuardar.classList.add('btn-success');
        }
    };
    
    // Función para ver logs de validación
    window.verLogsValidacion = function() {
        fetch('/admin/horarios/logs-validacion', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('📋 LOGS DE VALIDACIÓN DEL SERVIDOR:');
            console.log(data);
            
            if (data.logs && data.logs.length > 0) {
                data.logs.forEach((log, index) => {
                    console.log(`${index + 1}. ${log.timestamp}: ${log.message}`);
                    if (log.context) {
                        console.log('   Contexto:', log.context);
                    }
                });
            } else {
                console.log('No se encontraron logs recientes');
            }
        })
        .catch(error => {
            console.error('Error al obtener logs:', error);
        });
    };
});

// Funciones para filtrar la tabla de horarios
function aplicarFiltros() {
    const filtroAula = document.getElementById('filtro-aula').value;
    const filtroDia = document.getElementById('filtro-dia').value;
    const filtroProfesor = document.getElementById('filtro-profesor').value;
    const buscarTexto = document.getElementById('buscar-texto').value.toLowerCase();
    
    const filas = document.querySelectorAll('.fila-horario');
    let contador = 0;
    
    filas.forEach(fila => {
        let mostrar = true;
        
        // Filtro por aula
        if (filtroAula && fila.dataset.aulaId !== filtroAula) {
            mostrar = false;
        }
        
        // Filtro por día
        if (filtroDia && fila.dataset.dia !== filtroDia) {
            mostrar = false;
        }
        
        // Filtro por profesor
        if (filtroProfesor && fila.dataset.profesorId !== filtroProfesor) {
            mostrar = false;
        }
        
        // Filtro por texto
        if (buscarTexto && !fila.dataset.texto.includes(buscarTexto)) {
            mostrar = false;
        }
        
        if (mostrar) {
            fila.style.display = '';
            contador++;
        } else {
            fila.style.display = 'none';
        }
    });
    
    document.getElementById('contador-horarios').textContent = contador;
}

function filtrarPorAula() {
    const aulaActual = document.getElementById('aula_id').value;
    if (aulaActual) {
        document.getElementById('filtro-aula').value = aulaActual;
        aplicarFiltros();
        
        // Mostrar alerta
        const aulaTexto = document.getElementById('aula_id').options[document.getElementById('aula_id').selectedIndex]?.text || 'N/A';
        window.mostrarAlertaDebug(`
            <strong>🔍 FILTRADO POR AULA</strong><br>
            Mostrando solo horarios del aula: <strong>${aulaTexto}</strong><br>
            <small>Usa esto para ver si el aula está ocupada en otros horarios</small>
        `, 'info');
    } else {
        alert('Primero selecciona un aula en el formulario');
    }
}

function mostrarTodos() {
    document.getElementById('filtro-aula').value = '';
    document.getElementById('filtro-dia').value = '';
    document.getElementById('filtro-profesor').value = '';
    document.getElementById('buscar-texto').value = '';
    aplicarFiltros();
    limpiarResaltados();
    
    window.mostrarAlertaDebug(`
        <strong>👁️ MOSTRANDO TODOS</strong><br>
        Se han limpiado todos los filtros<br>
        <small>Ahora puedes ver todos los horarios del sistema</small>
    `, 'success');
}

function verificarEnTabla() {
    const aulaId = document.getElementById('aula_id').value;
    const aulaTexto = document.getElementById('aula_id').options[document.getElementById('aula_id').selectedIndex]?.text || 'N/A';
    const dia = document.getElementById('dia_semana').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    
    if (!aulaId || !dia || !horaInicio || !horaFin) {
        window.mostrarAlertaDebug(`
            <strong>⚠️ CAMPOS INCOMPLETOS</strong><br>
            Por favor completa todos los campos:<br>
            <small>Aula, Día, Hora Inicio y Hora Fin</small>
        `, 'warning');
        return;
    }
    
    // Limpiar filtros y mostrar todos
    document.getElementById('filtro-aula').value = '';
    document.getElementById('filtro-dia').value = '';
    document.getElementById('filtro-profesor').value = '';
    document.getElementById('buscar-texto').value = '';
    aplicarFiltros();
    
    // Resaltar conflictos
    const conflictos = resaltarConflictosEnTabla();
    
    const dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    const diaTexto = dias[parseInt(dia)] || 'N/A';
    
    if (conflictos > 0) {
        window.mostrarAlertaDebug(`
            <strong>❌ CONFLICTOS ENCONTRADOS</strong><br>
            <strong>Aula:</strong> ${aulaTexto}<br>
            <strong>Horario:</strong> ${diaTexto} ${horaInicio}-${horaFin}<br>
            <strong>Conflictos:</strong> ${conflictos} horario(s)<br>
            <small>Las filas en ROJO muestran los conflictos</small>
        `, 'danger');
        
        // Filtrar por aula para mostrar solo los conflictos
        setTimeout(() => {
            document.getElementById('filtro-aula').value = aulaId;
            document.getElementById('filtro-dia').value = dia;
            aplicarFiltros();
        }, 1000);
        
    } else {
        window.mostrarAlertaDebug(`
            <strong>✅ HORARIO LIBRE</strong><br>
            <strong>Aula:</strong> ${aulaTexto}<br>
            <strong>Horario:</strong> ${diaTexto} ${horaInicio}-${horaFin}<br>
            <small>No se encontraron conflictos en la tabla</small>
        `, 'success');
    }
}

// Función para limpiar todos los resaltados
function limpiarResaltados() {
    const filas = document.querySelectorAll('.fila-horario');
    filas.forEach(fila => {
        fila.classList.remove('table-danger', 'table-warning');
        fila.style.backgroundColor = '';
        fila.style.borderLeft = '';
        
        // Restaurar color original del horario que se está editando
        if (fila.querySelector('.badge.bg-success')) {
            fila.style.backgroundColor = '#d4edda';
            fila.style.borderLeft = '4px solid #28a745';
        }
    });
}

// Función para resaltar conflictos específicos después de verificación
function resaltarConflictosEnTabla() {
    const aulaSeleccionada = document.getElementById('aula_id').value;
    const diaSeleccionado = document.getElementById('dia_semana').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    
    if (!aulaSeleccionada || !diaSeleccionado || !horaInicio || !horaFin) {
        return;
    }
    
    // Limpiar resaltados previos
    limpiarResaltados();
    
    const filas = document.querySelectorAll('.fila-horario');
    let conflictos = 0;
    
    filas.forEach(fila => {
        const aulaFila = fila.dataset.aulaId;
        const diaFila = fila.dataset.dia;
        
        // Si es la misma aula y día, verificar solapamiento de horarios
        if (aulaFila === aulaSeleccionada && diaFila === diaSeleccionado) {
            const celdaHorario = fila.children[2]; // Columna de horario
            const horarioTexto = celdaHorario.textContent.trim();
            const lineas = horarioTexto.split('\n');
            const inicioFila = lineas[0].trim();
            const finFila = lineas[1].trim();
            
            // Verificar solapamiento (misma lógica que el backend)
            if (horaInicio < finFila && horaFin > inicioFila) {
                fila.classList.add('table-danger');
                fila.style.backgroundColor = '#f8d7da';
                fila.style.borderLeft = '4px solid #dc3545';
                conflictos++;
                
                // Agregar badge de conflicto
                const estadoCell = fila.children[8]; // Columna de estado
                estadoCell.innerHTML = '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> CONFLICTO</span>';
            }
        }
    });
    
    // Scroll automático a la tabla
    document.getElementById('tabla-horarios-completa').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
    
    return conflictos;
}

// Función para resaltar conflictos potenciales (versión mejorada)
function resaltarConflictos() {
    const aulaSeleccionada = document.getElementById('aula_id').value;
    const diaSeleccionado = document.getElementById('dia_semana').value;
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;
    
    if (!aulaSeleccionada || !diaSeleccionado || !horaInicio || !horaFin) {
        return;
    }
    
    const filas = document.querySelectorAll('.fila-horario');
    let conflictos = 0;
    
    filas.forEach(fila => {
        // Limpiar resaltados previos
        fila.classList.remove('table-danger', 'table-warning');
        fila.style.backgroundColor = '';
        fila.style.borderLeft = '';
        
        // Restaurar color del horario que se está editando
        if (fila.querySelector('.badge.bg-success')) {
            fila.style.backgroundColor = '#d4edda';
            fila.style.borderLeft = '4px solid #28a745';
        }
        
        const aulaFila = fila.dataset.aulaId;
        const diaFila = fila.dataset.dia;
        
        // Si es la misma aula y día, verificar solapamiento de horarios
        if (aulaFila === aulaSeleccionada && diaFila === diaSeleccionado) {
            const celdaHorario = fila.children[2]; // Columna de horario
            const horarioTexto = celdaHorario.textContent.trim();
            const lineas = horarioTexto.split('\n');
            const inicioFila = lineas[0].trim();
            const finFila = lineas[1].trim();
            
            // Verificar solapamiento (misma lógica que el backend)
            if (horaInicio < finFila && horaFin > inicioFila) {
                fila.classList.add('table-danger');
                fila.style.backgroundColor = '#f8d7da';
                fila.style.borderLeft = '4px solid #dc3545';
                conflictos++;
                
                // Actualizar badge de estado
                const estadoCell = fila.children[8];
                if (!fila.querySelector('.badge.bg-success')) { // No es el horario que se está editando
                    estadoCell.innerHTML = '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> CONFLICTO</span>';
                }
            }
        }
    });
    
    if (conflictos > 0) {
        window.mostrarAlertaDebug(`
            <strong>⚠️ CONFLICTOS DETECTADOS</strong><br>
            Se encontraron <strong>${conflictos}</strong> conflictos potenciales<br>
            <small>Las filas en rojo muestran horarios que se solapan</small>
        `, 'warning');
    } else {
        window.mostrarAlertaDebug(`
            <strong>✅ SIN CONFLICTOS</strong><br>
            No se detectaron conflictos con la selección actual<br>
            <small>El horario parece estar libre</small>
        `, 'success');
    }
}

// Agregar evento para resaltar conflictos cuando cambien los campos
document.addEventListener('DOMContentLoaded', function() {
    const campos = ['aula_id', 'dia_semana', 'hora_inicio', 'hora_fin'];
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (elemento) {
            elemento.addEventListener('change', function() {
                // Resaltar conflictos visuales
                resaltarConflictos();
                
                // Deshabilitar botón de guardar hasta que se verifique
                const btnGuardar = document.getElementById('btn-guardar-cambios');
                if (btnGuardar) {
                    btnGuardar.disabled = true;
                    btnGuardar.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Verificar Cambios Primero';
                    btnGuardar.classList.remove('btn-success', 'btn-danger');
                    btnGuardar.classList.add('btn-warning');
                    btnGuardar.title = 'Haz clic en "Verificar Cambios" antes de guardar';
                }
                
                // Mostrar mensaje de que debe verificar
                window.mostrarAlertaDebug(`
                    <strong>⚠️ CAMBIOS DETECTADOS</strong><br>
                    Has modificado el formulario<br>
                    <strong>Acción requerida:</strong> Haz clic en "Verificar Cambios"<br>
                    <small>El botón "Guardar" se habilitará después de la verificación</small>
                `, 'warning');
            });
        }
    });
});

// ===== FUNCIONES PARA CONFIGURACIÓN DE MÚLTIPLES DÍAS =====

function toggleConfiguracionMultipleDias() {
    const checkbox = document.getElementById('usar_configuracion_por_dia');
    const configuracion = document.getElementById('configuracion-multiples-dias');
    const camposSimples = ['dia_semana', 'aula_id', 'tipo_clase'];
    
    if (checkbox.checked) {
        // Activar configuración múltiple
        configuracion.style.display = 'block';
        
        // Deshabilitar campos simples
        camposSimples.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento) {
                elemento.disabled = true;
                elemento.style.backgroundColor = '#f8f9fa';
            }
        });
        
        // Marcar el día actual por defecto
        const diaActual = {{ $horario->dia_semana }};
        const checkboxDia = document.getElementById(`dia_${diaActual}`);
        if (checkboxDia && !checkboxDia.checked) {
            checkboxDia.checked = true;
            actualizarConfiguracionDias();
        }
        
        window.mostrarAlertaDebug(`
            <strong>🗓️ MODO MÚLTIPLES DÍAS ACTIVADO</strong><br>
            Ahora puedes configurar diferentes días de la semana<br>
            <small>Selecciona los días y configura cada uno individualmente</small>
        `, 'info');
        
    } else {
        // Desactivar configuración múltiple
        configuracion.style.display = 'none';
        
        // Habilitar campos simples
        camposSimples.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento) {
                elemento.disabled = false;
                elemento.style.backgroundColor = '';
            }
        });
        
        // Limpiar checkboxes
        document.querySelectorAll('.dia-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('configuraciones-por-dia').innerHTML = '';
        
        window.mostrarAlertaDebug(`
            <strong>📅 MODO SIMPLE ACTIVADO</strong><br>
            Configuración de un solo día por semana<br>
            <small>Usa los campos principales del formulario</small>
        `, 'info');
    }
}

function actualizarConfiguracionDias() {
    const diasSeleccionados = [];
    document.querySelectorAll('.dia-checkbox:checked').forEach(checkbox => {
        diasSeleccionados.push(parseInt(checkbox.value));
    });
    
    const container = document.getElementById('configuraciones-por-dia');
    const alerta = document.getElementById('alerta-multiples-dias');
    
    if (diasSeleccionados.length === 0) {
        container.innerHTML = '<div class="alert alert-warning">Selecciona al menos un día de la semana.</div>';
        alerta.style.display = 'none';
        return;
    }
    
    alerta.style.display = 'block';
    
    const diasNombres = {1: 'Lunes', 2: 'Martes', 3: 'Miércoles', 4: 'Jueves', 5: 'Viernes', 6: 'Sábado', 7: 'Domingo'};
    
    let html = '<div class="row">';
    
    diasSeleccionados.forEach((dia, index) => {
        const nombreDia = diasNombres[dia];
        
        html += `
            <div class="col-md-6 mb-3">
                <div class="card border-secondary">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-calendar-day"></i> ${nombreDia}</h6>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="dias_semana[]" value="${dia}">
                        
                        <div class="mb-2">
                            <label class="form-label">Aula:</label>
                            <select class="form-select form-select-sm" name="config_dias[${dia}][aula_id]" required>
                                <option value="">Seleccionar aula...</option>
                                @foreach($aulas as $aula)
                                    <option value="{{ $aula->id }}">{{ $aula->codigo_aula }} - {{ $aula->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label">Tipo de Clase:</label>
                            <select class="form-select form-select-sm" name="config_dias[${dia}][tipo_clase]" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="teorica">Teórica</option>
                                <option value="practica">Práctica</option>
                                <option value="laboratorio">Laboratorio</option>
                            </select>
                        </div>
                        
                        <div class="text-muted small">
                            <i class="fas fa-clock"></i> Horario: Se usará el horario principal (${document.getElementById('hora_inicio').value || 'N/A'} - ${document.getElementById('hora_fin').value || 'N/A'})
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
    
    // Mostrar resumen
    window.mostrarAlertaDebug(`
        <strong>📋 CONFIGURACIÓN ACTUALIZADA</strong><br>
        Días seleccionados: <strong>${diasSeleccionados.length}</strong><br>
        <small>${diasSeleccionados.map(d => diasNombres[d]).join(', ')}</small><br>
        <small>Configura el aula y tipo de clase para cada día</small>
    `, 'info');
}

function cargarConfiguracionExistente() {
    if (horarioActual.configuracion_dias && horarioActual.configuracion_dias.length > 0) {
        // Marcar los días existentes
        horarioActual.configuracion_dias.forEach(config => {
            const checkbox = document.getElementById(`dia_${config.dia}`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
        
        // Generar las configuraciones
        actualizarConfiguracionDias();
        
        // Llenar los valores existentes
        setTimeout(() => {
            horarioActual.configuracion_dias.forEach(config => {
                const aulaSelect = document.querySelector(`select[name="config_dias[${config.dia}][aula_id]"]`);
                const tipoSelect = document.querySelector(`select[name="config_dias[${config.dia}][tipo_clase]"]`);
                
                if (aulaSelect) aulaSelect.value = config.aula_id;
                if (tipoSelect) tipoSelect.value = config.tipo_clase;
            });
        }, 100);
    } else {
        // Si no hay configuración existente pero está activado, usar el día actual
        const checkboxDiaActual = document.getElementById(`dia_${horarioActual.dia_semana}`);
        if (checkboxDiaActual) {
            checkboxDiaActual.checked = true;
            actualizarConfiguracionDias();
            
            // Llenar con los valores actuales
            setTimeout(() => {
                const aulaSelect = document.querySelector(`select[name="config_dias[${horarioActual.dia_semana}][aula_id]"]`);
                const tipoSelect = document.querySelector(`select[name="config_dias[${horarioActual.dia_semana}][tipo_clase]"]`);
                
                if (aulaSelect) aulaSelect.value = horarioActual.aula_id;
                if (tipoSelect) tipoSelect.value = horarioActual.tipo_clase;
            }, 100);
        }
    }
}

// Función para validar configuración de múltiples días
function validarConfiguracionMultipleDias() {
    const checkbox = document.getElementById('usar_configuracion_por_dia');
    
    if (!checkbox.checked) {
        return true; // Modo simple, usar validación normal
    }
    
    const diasSeleccionados = document.querySelectorAll('.dia-checkbox:checked');
    
    if (diasSeleccionados.length === 0) {
        window.mostrarAlertaDebug(`
            <strong>⚠️ ERROR DE CONFIGURACIÓN</strong><br>
            Debes seleccionar al menos un día de la semana<br>
            <small>O desactiva el modo "Múltiples Días"</small>
        `, 'danger');
        return false;
    }
    
    let configuracionCompleta = true;
    
    diasSeleccionados.forEach(checkbox => {
        const dia = checkbox.value;
        const aulaSelect = document.querySelector(`select[name="config_dias[${dia}][aula_id]"]`);
        const tipoSelect = document.querySelector(`select[name="config_dias[${dia}][tipo_clase]"]`);
        
        if (!aulaSelect || !aulaSelect.value || !tipoSelect || !tipoSelect.value) {
            configuracionCompleta = false;
        }
    });
    
    if (!configuracionCompleta) {
        window.mostrarAlertaDebug(`
            <strong>⚠️ CONFIGURACIÓN INCOMPLETA</strong><br>
            Todos los días seleccionados deben tener:<br>
            • Aula asignada<br>
            • Tipo de clase definido
        `, 'danger');
        return false;
    }
    
    return true;
}

</script>

<style>
.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.card-header.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.card-body.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.badge {
    font-size: 0.8em;
}

#panel-validacion-cu12 .card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

#panel-validacion-cu12 .alert {
    border-radius: 0.375rem;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.border-warning {
    border-color: #ffc107 !important;
}

.border-danger {
    border-color: #dc3545 !important;
}

.bg-warning.text-dark {
    background-color: #ffc107 !important;
    color: #212529 !important;
}
</style>

@endsection