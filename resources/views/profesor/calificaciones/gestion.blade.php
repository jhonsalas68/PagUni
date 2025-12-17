@extends('layouts.dashboard')

@section('title', 'Ingreso de Notas')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> Ingreso de Notas: {{ $grupo->materia->nombre }} - Grupo {{ $grupo->identificador }}
        </h1>
        <a href="{{ route('profesor.calificaciones.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver a Grupos
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Configuración de Criterios -->
    <div class="card shadow mb-4 border-left-info">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Configuración de Evaluación</h6>
            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#configPanel" aria-expanded="false" aria-controls="configPanel">
                <i class="fas fa-cog"></i> Mostrar/Ocultar
            </button>
        </div>
        <div class="collapse show" id="configPanel">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="font-weight-bold mb-3">Criterios Actuales</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th class="text-center" width="150">Ponderación (%)</th>
                                        <th class="text-center" width="150">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalPonderacion = 0; @endphp
                                    @foreach($tiposEvaluacion as $tipo)
                                        @php $totalPonderacion += $tipo->ponderacion; @endphp
                                        <tr>
                                            <td>{{ $tipo->nombre }}</td>
                                            <td class="text-center">{{ $tipo->ponderacion }}%</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $tipo->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('profesor.calificaciones.criterios.destroy', $tipo->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que desea eliminar este criterio? Se borrarán las notas asociadas.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Modal Editar -->
                                        <div class="modal fade" id="editModal{{ $tipo->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('profesor.calificaciones.criterios.update', $tipo->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Editar Criterio</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Nombre</label>
                                                                <input type="text" name="nombre" class="form-control" value="{{ $tipo->nombre }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Ponderación (%)</label>
                                                                <input type="number" name="ponderacion" class="form-control" value="{{ $tipo->ponderacion }}" min="0" max="100" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <tr class="table-{{ $totalPonderacion == 100 ? 'success' : 'warning' }}">
                                        <td class="font-weight-bold text-end">Total:</td>
                                        <td class="font-weight-bold text-center">{{ $totalPonderacion }}%</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4 border-left">
                        <h6 class="font-weight-bold mb-3">Nuevo Criterio</h6>
                        <form action="{{ route('profesor.calificaciones.criterios.store', $grupo->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Proyecto Final" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ponderación (%)</label>
                                <input type="number" name="ponderacion" class="form-control" placeholder="Ej: 30" min="0" max="100" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario simplificado -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Ingresar Calificaciones</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('profesor.calificaciones.store') }}" method="POST">
                @csrf
                <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tipo_evaluacion" class="form-label fw-bold">Seleccionar Evaluación:</label>
                        <select name="tipo_evaluacion_id" id="tipo_evaluacion" class="form-select" required onchange="filtrarNotas(this.value)">
                            <option value="">-- Seleccione --</option>
                            @foreach($tiposEvaluacion as $tipo)
                                <option value="{{ $tipo->id }}" data-ponderacion="{{ $tipo->ponderacion }}">
                                    {{ $tipo->nombre }} ({{ $tipo->ponderacion }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Estado del Formulario:</label>
                        <div class="alert alert-info mb-0" id="form-status">
                            <i class="fas fa-info-circle"></i> Seleccione una evaluación para comenzar
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Estudiante</th>
                                <th>Nota (0-100)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inscripciones as $inscripcion)
                                <tr>
                                    <td>
                                        {{ $inscripcion->estudiante->apellido }}, {{ $inscripcion->estudiante->nombre }}
                                        <br>
                                        <small class="text-muted">{{ $inscripcion->estudiante->codigo_estudiante }}</small>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               step="0.01" 
                                               min="0" 
                                               max="100" 
                                               name="notas[{{ $inscripcion->id }}]" 
                                               class="form-control nota-input" 
                                               data-inscripcion="{{ $inscripcion->id }}"
                                               placeholder="Ingresar nota (0-100)">
                                        <div class="invalid-feedback">
                                            La nota debe estar entre 0 y 100 puntos.
                                        </div>
                                        <div class="valid-feedback">
                                            Nota válida.
                                        </div>
                                        <!-- Campo oculto para almacenar los valores existentes cargados dinámicamente -->
                                        @foreach($inscripcion->calificaciones as $cal)
                                            <input type="hidden" 
                                                   class="existing-grade" 
                                                   data-inscripcion="{{ $inscripcion->id }}" 
                                                   data-tipo="{{ $cal->tipo_evaluacion_id }}" 
                                                   value="{{ $cal->nota }}">
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> Guardar Notas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Debug: Mostrar información en consola
    console.log('=== DEBUG CALIFICACIONES ===');
    console.log('Grupo ID:', {{ $grupo->id }});
    console.log('Tipos de evaluación:', @json($tiposEvaluacion->pluck('nombre', 'id')));
    console.log('Inscripciones:', @json($inscripciones->pluck('estudiante.codigo_estudiante', 'id')));

    function filtrarNotas(tipoId) {
        console.log('Filtrando notas para tipo:', tipoId);
        
        const statusDiv = document.getElementById('form-status');
        
        // Limpiar inputs
        document.querySelectorAll('.nota-input').forEach(input => {
            input.value = '';
            input.classList.remove('is-valid', 'is-invalid');
        });

        if (!tipoId) {
            statusDiv.className = 'alert alert-info mb-0';
            statusDiv.innerHTML = '<i class="fas fa-info-circle"></i> Seleccione una evaluación para comenzar';
            return;
        }

        // Obtener nombre del tipo de evaluación
        const selectElement = document.getElementById('tipo_evaluacion');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const tipoNombre = selectedOption.text;

        // Cargar notas existentes si las hay
        let notasCargadas = 0;
        document.querySelectorAll('.existing-grade').forEach(hiddenInput => {
            if (hiddenInput.dataset.tipo == tipoId) {
                const inscripcionId = hiddenInput.dataset.inscripcion;
                const inputVisible = document.querySelector(`.nota-input[data-inscripcion="${inscripcionId}"]`);
                if (inputVisible) {
                    inputVisible.value = hiddenInput.value;
                    inputVisible.classList.add('is-valid');
                    notasCargadas++;
                    console.log(`Nota cargada: Inscripción ${inscripcionId} = ${hiddenInput.value}`);
                }
            }
        });
        
        // Actualizar estado
        if (notasCargadas > 0) {
            statusDiv.className = 'alert alert-success mb-0';
            statusDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${tipoNombre}: ${notasCargadas} nota(s) existente(s) cargada(s)`;
        } else {
            statusDiv.className = 'alert alert-warning mb-0';
            statusDiv.innerHTML = `<i class="fas fa-edit"></i> ${tipoNombre}: Listo para ingresar notas nuevas`;
        }
        
        console.log(`Total notas cargadas: ${notasCargadas}`);
        actualizarContadorNotas();
    }

    function actualizarContadorNotas() {
        const notasInputs = document.querySelectorAll('.nota-input');
        const statusDiv = document.getElementById('form-status');
        let notasIngresadas = 0;
        let notasValidas = 0;
        
        notasInputs.forEach(input => {
            if (input.value && input.value.trim() !== '') {
                notasIngresadas++;
                if (input.classList.contains('is-valid')) {
                    notasValidas++;
                }
            }
        });
        
        if (notasIngresadas > 0) {
            const tipoSelect = document.getElementById('tipo_evaluacion');
            const tipoNombre = tipoSelect.options[tipoSelect.selectedIndex].text;
            
            if (notasValidas === notasIngresadas) {
                statusDiv.className = 'alert alert-success mb-0';
                statusDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${tipoNombre}: ${notasValidas} nota(s) válida(s) lista(s) para guardar`;
            } else {
                statusDiv.className = 'alert alert-warning mb-0';
                statusDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${tipoNombre}: ${notasValidas}/${notasIngresadas} notas válidas`;
            }
        }
    }

    // Validación del formulario antes del envío
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action="{{ route('profesor.calificaciones.store') }}"]');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            console.log('=== ENVIANDO FORMULARIO ===');
            
            const tipoEvaluacion = document.getElementById('tipo_evaluacion').value;
            const notasInputs = document.querySelectorAll('.nota-input');
            
            console.log('Tipo de evaluación seleccionado:', tipoEvaluacion);
            
            if (!tipoEvaluacion) {
                e.preventDefault();
                alert('Por favor seleccione un tipo de evaluación');
                return false;
            }
            
            // Contar notas ingresadas
            let notasIngresadas = 0;
            const datosNotas = {};
            
            notasInputs.forEach(input => {
                const inscripcionId = input.dataset.inscripcion;
                const nota = input.value;
                
                if (nota && nota.trim() !== '') {
                    notasIngresadas++;
                    datosNotas[inscripcionId] = nota;
                    console.log(`Nota a enviar: Inscripción ${inscripcionId} = ${nota}`);
                }
            });
            
            console.log(`Total notas a enviar: ${notasIngresadas}`);
            console.log('Datos del formulario:', {
                grupo_id: {{ $grupo->id }},
                tipo_evaluacion_id: tipoEvaluacion,
                notas: datosNotas
            });
            
            if (notasIngresadas === 0) {
                e.preventDefault();
                alert('Por favor ingrese al menos una nota');
                return false;
            }
            
            // Deshabilitar botón para evitar doble envío
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            
            // Mostrar mensaje de carga
            const loadingAlert = document.createElement('div');
            loadingAlert.className = 'alert alert-info';
            loadingAlert.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando calificaciones, por favor espere...';
            form.insertBefore(loadingAlert, form.firstChild);
            
            console.log('Formulario enviado correctamente');
        });
        
        // Re-habilitar botón si hay error
        window.addEventListener('pageshow', function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Notas';
        });
    });

    // Validación en tiempo real de las notas
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('nota-input')) {
            const nota = parseFloat(e.target.value);
            const input = e.target;
            
            // Remover clases anteriores
            input.classList.remove('is-valid', 'is-invalid');
            
            if (input.value === '') {
                // Vacío es válido
                actualizarContadorNotas();
                return;
            }
            
            if (isNaN(nota) || nota < 0 || nota > 100) {
                input.classList.add('is-invalid');
                console.warn(`Nota inválida: ${input.value} (debe ser entre 0-100)`);
            } else {
                input.classList.add('is-valid');
                console.log(`Nota válida: ${nota}`);
            }
            
            actualizarContadorNotas();
        }
    });
</script>
@endsection
