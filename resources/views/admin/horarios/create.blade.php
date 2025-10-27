@extends('layouts.dashboard')

@section('title', 'Nuevo Horario')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Nuevo Horario</h1>
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
            <form method="POST" action="{{ route('admin.horarios.store') }}" id="horarioForm">
                @csrf
                
                <!-- Información básica -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="carga_academica_id" class="form-label">Carga Académica <span class="text-danger">*</span></label>
                            <select class="form-select @error('carga_academica_id') is-invalid @enderror" 
                                    id="carga_academica_id" name="carga_academica_id" required>
                                <option value="">Seleccionar carga académica...</option>
                                @foreach($cargasAcademicas as $carga)
                                    <option value="{{ $carga->id }}" {{ old('carga_academica_id') == $carga->id ? 'selected' : '' }}>
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
                                    <option value="{{ $aula->id }}" {{ old('aula_id') == $aula->id ? 'selected' : '' }}>
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

                <!-- Selección de días múltiples -->
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Días de la Semana <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="1" id="dia1" name="dias_semana[]">
                                        <label class="form-check-label" for="dia1">Lunes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="2" id="dia2" name="dias_semana[]">
                                        <label class="form-check-label" for="dia2">Martes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="3" id="dia3" name="dias_semana[]">
                                        <label class="form-check-label" for="dia3">Miércoles</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="4" id="dia4" name="dias_semana[]">
                                        <label class="form-check-label" for="dia4">Jueves</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="5" id="dia5" name="dias_semana[]">
                                        <label class="form-check-label" for="dia5">Viernes</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input class="form-check-input dia-checkbox" type="checkbox" value="6" id="dia6" name="dias_semana[]">
                                        <label class="form-check-label" for="dia6">Sábado</label>
                                    </div>
                                </div>
                            </div>
                            @error('dias_semana')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Horarios y duración -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="hora_inicio" class="form-label">Hora de Inicio <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror" 
                                   id="hora_inicio" name="hora_inicio" value="{{ old('hora_inicio') }}" required>
                            @error('hora_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="hora_fin" class="form-label">Hora de Fin <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('hora_fin') is-invalid @enderror" 
                                   id="hora_fin" name="hora_fin" value="{{ old('hora_fin') }}" required>
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
                                       placeholder="0 horas" style="background-color: #f8f9fa;">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            </div>
                            <input type="hidden" name="duracion_horas" id="duracion_horas">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="tipo_clase" class="form-label">Tipo de Clase <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo_clase') is-invalid @enderror" 
                                    id="tipo_clase" name="tipo_clase" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="teorica" {{ old('tipo_clase') == 'teorica' ? 'selected' : '' }}>Teórica</option>
                                <option value="practica" {{ old('tipo_clase') == 'practica' ? 'selected' : '' }}>Práctica</option>
                                <option value="laboratorio" {{ old('tipo_clase') == 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                            </select>
                            @error('tipo_clase')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Configuración del período académico -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="periodo_academico" class="form-label">Período Académico <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('periodo_academico') is-invalid @enderror" 
                                   id="periodo_academico" name="periodo_academico" 
                                   value="{{ old('periodo_academico', '2024-2') }}" 
                                   placeholder="Ej: 2024-2, 2025-1" required>
                            @error('periodo_academico')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="es_semestral" name="es_semestral" 
                                       value="1" {{ old('es_semestral', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="es_semestral">
                                    <strong>Horario Semestral</strong>
                                </label>
                            </div>
                            <small class="text-muted">Si está marcado, el horario durará todo el semestre</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="semanas_duracion" class="form-label">Semanas de Duración</label>
                            <input type="number" class="form-control @error('semanas_duracion') is-invalid @enderror" 
                                   id="semanas_duracion" name="semanas_duracion" 
                                   value="{{ old('semanas_duracion', 16) }}" min="1" max="20">
                            @error('semanas_duracion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Fechas específicas (opcional para horarios no semestrales) -->
                <div class="row" id="fechas-especificas" style="display: none;">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                   id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" 
                                   id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}">
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Horarios Existentes -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Horarios Existentes - Período Actual</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="filtro_periodo" class="form-label">Filtrar por Período:</label>
                                        <select class="form-select" id="filtro_periodo">
                                            <option value="">Todos los períodos</option>
                                            <option value="2024-2" selected>2024-2</option>
                                            <option value="2025-1">2025-1</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="filtro_aula" class="form-label">Filtrar por Aula:</label>
                                        <select class="form-select" id="filtro_aula">
                                            <option value="">Todas las aulas</option>
                                            @foreach($aulas as $aula)
                                                <option value="{{ $aula->id }}">{{ $aula->codigo_aula }} - {{ $aula->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-sm table-striped" id="tabla-horarios-existentes">
                                            <thead class="table-dark sticky-top">
                                                <tr>
                                                    <th>Día</th>
                                                    <th>Hora</th>
                                                    <th>Materia</th>
                                                    <th>Profesor</th>
                                                    <th>Aula</th>
                                                    <th>Período</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($horariosExistentes as $horario)
                                                <tr data-periodo="{{ $horario->periodo_academico }}" 
                                                    data-aula="{{ $horario->aula_id }}"
                                                    data-dia="{{ $horario->dia_semana }}"
                                                    data-inicio="{{ $horario->hora_inicio }}"
                                                    data-fin="{{ $horario->hora_fin }}">
                                                    <td>
                                                        @php
                                                            $dias = ['', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                                                        @endphp
                                                        <span class="badge bg-primary">{{ $dias[$horario->dia_semana] ?? 'N/A' }}</span>
                                                    </td>
                                                    <td>
                                                        <small>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</small>
                                                    </td>
                                                    <td>
                                                        <small>{{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        <small>{{ $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $horario->aula->codigo_aula ?? 'N/A' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $horario->periodo_academico }}</span>
                                                    </td>
                                                    <td>
                                                        @if($horario->es_semestral)
                                                            <span class="badge bg-success">Semestral</span>
                                                        @else
                                                            <span class="badge bg-warning">Específico</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No hay horarios registrados</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Los horarios en <span class="text-danger">rojo</span> indican conflictos potenciales con tu selección.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen de horarios a crear -->
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-calendar-check"></i> Resumen de Horarios y Carga Académica</h6>
                                <div id="resumen-horarios">
                                    <p class="text-muted">Selecciona los días y horarios para ver el resumen</p>
                                </div>
                                <div id="carga-horaria" class="mt-3" style="display: none;">
                                    <h6 class="text-primary"><i class="fas fa-chart-bar"></i> Cálculo de Carga Horaria</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card border-primary">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title text-primary">Semanal</h5>
                                                    <h4 id="horas-semanales" class="text-primary">0 hrs</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-info">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title text-info">Mensual</h5>
                                                    <h4 id="horas-mensuales" class="text-info">0 hrs</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-success">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title text-success">Semestral</h5>
                                                    <h4 id="horas-semestrales" class="text-success">0 hrs</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-warning">
                                                <div class="card-body text-center">
                                                    <h5 class="card-title text-warning">Total</h5>
                                                    <h4 id="horas-totales" class="text-warning">0 hrs</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                    <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary" id="guardarBtn" disabled>
                        <i class="fas fa-save"></i> Guardar Horarios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    const duracionDisplay = document.getElementById('duracion_display');
    const duracionHoras = document.getElementById('duracion_horas');
    const diasCheckboxes = document.querySelectorAll('.dia-checkbox');
    const resumenDiv = document.getElementById('resumen-horarios');
    const cargaHorariaDiv = document.getElementById('carga-horaria');
    const guardarBtn = document.getElementById('guardarBtn');
    const esSemestralCheckbox = document.getElementById('es_semestral');
    const fechasEspecificas = document.getElementById('fechas-especificas');
    const semanasDuracion = document.getElementById('semanas_duracion');
    const filtroPeriodo = document.getElementById('filtro_periodo');
    const filtroAula = document.getElementById('filtro_aula');
    const tablaHorarios = document.getElementById('tabla-horarios-existentes');

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
            
            actualizarResumen();
            return diferencia;
        } else {
            duracionDisplay.value = '';
            duracionHoras.value = '';
            return 0;
        }
    }

    // Función para actualizar resumen y cálculos
    function actualizarResumen() {
        const diasSeleccionados = Array.from(diasCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.nextElementSibling.textContent);
        
        const duracion = parseFloat(duracionHoras.value) || 0;
        const esSemestral = esSemestralCheckbox.checked;
        const semanas = parseInt(semanasDuracion.value) || 16;
        
        if (diasSeleccionados.length > 0 && horaInicio.value && horaFin.value && duracion > 0) {
            const horasSemanal = duracion * diasSeleccionados.length;
            const horasMensual = horasSemanal * 4;
            const horasSemestral = esSemestral ? horasSemanal * semanas : horasSemanal;
            const horasTotal = horasSemestral;
            
            resumenDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Días seleccionados:</strong><br>
                        ${diasSeleccionados.map(dia => `<span class="badge bg-primary me-1">${dia}</span>`).join('')}
                    </div>
                    <div class="col-md-6">
                        <strong>Horario:</strong> ${horaInicio.value} - ${horaFin.value}<br>
                        <strong>Duración por clase:</strong> ${duracion.toFixed(2)} horas<br>
                        <strong>Tipo:</strong> ${esSemestral ? 'Semestral' : 'Específico'}
                    </div>
                </div>
            `;

            // Actualizar cálculos de carga horaria
            document.getElementById('horas-semanales').textContent = horasSemanal.toFixed(2) + ' hrs';
            document.getElementById('horas-mensuales').textContent = horasMensual.toFixed(2) + ' hrs';
            document.getElementById('horas-semestrales').textContent = horasSemestral.toFixed(2) + ' hrs';
            document.getElementById('horas-totales').textContent = horasTotal.toFixed(2) + ' hrs';
            
            cargaHorariaDiv.style.display = 'block';
            guardarBtn.disabled = false;
        } else {
            resumenDiv.innerHTML = '<p class="text-muted">Selecciona los días y horarios para ver el resumen</p>';
            cargaHorariaDiv.style.display = 'none';
            guardarBtn.disabled = true;
        }
    }

    // Función para mostrar/ocultar fechas específicas
    function toggleFechasEspecificas() {
        if (esSemestralCheckbox.checked) {
            fechasEspecificas.style.display = 'none';
            semanasDuracion.disabled = false;
        } else {
            fechasEspecificas.style.display = 'block';
            semanasDuracion.disabled = true;
        }
        actualizarResumen();
    }

    // Función para filtrar horarios existentes
    function filtrarHorarios() {
        const periodoSeleccionado = filtroPeriodo.value;
        const aulaSeleccionada = filtroAula.value;
        const filas = tablaHorarios.querySelectorAll('tbody tr[data-periodo]');

        filas.forEach(fila => {
            const periodo = fila.getAttribute('data-periodo');
            const aula = fila.getAttribute('data-aula');
            
            let mostrar = true;
            
            if (periodoSeleccionado && periodo !== periodoSeleccionado) {
                mostrar = false;
            }
            
            if (aulaSeleccionada && aula !== aulaSeleccionada) {
                mostrar = false;
            }
            
            fila.style.display = mostrar ? '' : 'none';
        });
    }

    // Función para resaltar conflictos
    function resaltarConflictos() {
        const diasSeleccionados = Array.from(diasCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        const aulaSeleccionada = document.getElementById('aula_id').value;
        const periodoSeleccionado = document.getElementById('periodo_academico').value;
        const horaInicioSeleccionada = horaInicio.value;
        const horaFinSeleccionada = horaFin.value;
        
        const filas = tablaHorarios.querySelectorAll('tbody tr[data-periodo]');
        
        filas.forEach(fila => {
            const diaHorario = fila.getAttribute('data-dia');
            const aulaHorario = fila.getAttribute('data-aula');
            const periodoHorario = fila.getAttribute('data-periodo');
            const inicioHorario = fila.getAttribute('data-inicio');
            const finHorario = fila.getAttribute('data-fin');
            
            let esConflicto = false;
            
            // Verificar si hay conflicto
            if (diasSeleccionados.includes(diaHorario) && 
                aulaSeleccionada === aulaHorario && 
                periodoSeleccionado === periodoHorario &&
                horaInicioSeleccionada && horaFinSeleccionada) {
                
                // Verificar solapamiento de horarios
                if ((horaInicioSeleccionada >= inicioHorario && horaInicioSeleccionada < finHorario) ||
                    (horaFinSeleccionada > inicioHorario && horaFinSeleccionada <= finHorario) ||
                    (horaInicioSeleccionada <= inicioHorario && horaFinSeleccionada >= finHorario)) {
                    esConflicto = true;
                }
            }
            
            // Aplicar estilo de conflicto
            if (esConflicto) {
                fila.classList.add('table-danger');
                fila.style.fontWeight = 'bold';
            } else {
                fila.classList.remove('table-danger');
                fila.style.fontWeight = 'normal';
            }
        });
    }

    // Event listeners
    horaInicio.addEventListener('change', () => {
        calcularDuracion();
        resaltarConflictos();
    });
    horaFin.addEventListener('change', () => {
        calcularDuracion();
        resaltarConflictos();
    });
    diasCheckboxes.forEach(cb => cb.addEventListener('change', () => {
        actualizarResumen();
        resaltarConflictos();
    }));
    esSemestralCheckbox.addEventListener('change', toggleFechasEspecificas);
    semanasDuracion.addEventListener('input', actualizarResumen);
    filtroPeriodo.addEventListener('change', filtrarHorarios);
    filtroAula.addEventListener('change', filtrarHorarios);
    
    // Event listeners para detectar conflictos
    document.getElementById('aula_id').addEventListener('change', resaltarConflictos);
    document.getElementById('periodo_academico').addEventListener('input', resaltarConflictos);

    // Inicializar estado
    toggleFechasEspecificas();
    filtrarHorarios();

    // Validación del formulario
    document.getElementById('horarioForm').addEventListener('submit', function(e) {
        const diasSeleccionados = Array.from(diasCheckboxes).filter(cb => cb.checked);
        
        if (diasSeleccionados.length === 0) {
            e.preventDefault();
            alert('Debes seleccionar al menos un día de la semana');
            return false;
        }

        if (!duracionHoras.value || parseFloat(duracionHoras.value) <= 0) {
            e.preventDefault();
            alert('La duración debe ser mayor a 0 horas');
            return false;
        }
    });
});
</script>

<style>
.table-danger {
    --bs-table-bg: #f8d7da !important;
    animation: pulse-red 2s infinite;
}

@keyframes pulse-red {
    0% { background-color: #f8d7da; }
    50% { background-color: #f5c2c7; }
    100% { background-color: #f8d7da; }
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}

.table-responsive {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}
</style>
@endsection