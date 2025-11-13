@extends('layouts.dashboard')

@section('title', 'Dashboard Profesor')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-chalkboard-teacher"></i> Dashboard Profesor
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="{{ route('profesor.historial-asistencias') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-history"></i> Historial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Clases Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['clases_hoy'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Asistidas Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['clases_asistidas_hoy'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['clases_pendientes_hoy'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Materias</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estadisticas['total_materias'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clases de Hoy -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-day"></i> Mis Clases de Hoy - {{ $hoy->format('d/m/Y') }}
                    </h6>
                    <div class="dropdown no-arrow">
                        <button class="btn btn-sm btn-outline-primary" onclick="actualizarAsistencias()">
                            <i class="fas fa-sync-alt"></i> Actualizar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($horariosHoy->count() > 0)
                        <div class="row">
                            @foreach($horariosHoy as $horario)
                                @php
                                    $asistencia = $asistenciasHoy->get($horario->id);
                                @endphp
                                
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card border-left-{{ $asistencia ? $asistencia->estado_color : 'primary' }} h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title text-primary mb-0">
                                                    {{ $horario->cargaAcademica->grupo->materia->nombre ?? 'Materia N/A' }}
                                                </h6>
                                                @if($asistencia)
                                                    <span class="badge bg-{{ $asistencia->estado_color }}">
                                                        {{ $asistencia->estado_texto }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Sin Registro</span>
                                                @endif
                                            </div>
                                            
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}<br>
                                                    <i class="fas fa-door-open"></i> {{ $horario->aula->codigo_aula ?? 'N/A' }}<br>
                                                    <i class="fas fa-users"></i> {{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}
                                                </small>
                                            </div>

                                            @if($asistencia)
                                                <div class="mb-3">
                                                    @if($asistencia->hora_entrada)
                                                        <small class="text-success">
                                                            <i class="fas fa-sign-in-alt"></i> Entrada: {{ $asistencia->hora_entrada }}
                                                            @if(!$asistencia->validado_en_horario)
                                                                <span class="text-warning">(Tardanza)</span>
                                                            @endif
                                                        </small><br>
                                                    @endif
                                                    @if($asistencia->hora_salida)
                                                        <small class="text-info">
                                                            <i class="fas fa-sign-out-alt"></i> Salida: {{ $asistencia->hora_salida }}
                                                        </small><br>
                                                    @endif
                                                    @if($asistencia->duracion_clase)
                                                        <small class="text-muted">
                                                            <i class="fas fa-hourglass-half"></i> Duración: {{ $asistencia->duracion_clase }} min
                                                        </small>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="d-grid gap-2">
                                                @if($asistencia && $asistencia->estado === 'pendiente_qr')
                                                    <button class="btn btn-warning btn-sm" onclick="abrirQRExistente('{{ $asistencia->qr_token }}')">
                                                        <i class="fas fa-qrcode"></i> Ver QR Generado
                                                    </button>
                                                @elseif($asistencia && in_array($asistencia->estado, ['presente', 'tardanza', 'en_clase']))
                                                    <button class="btn btn-success btn-sm" disabled>
                                                        <i class="fas fa-check-circle"></i> Asistencia Confirmada
                                                    </button>
                                                @elseif(!$asistencia)
                                                    <button class="btn btn-primary btn-sm" onclick="mostrarModalQR({{ $horario->id }})">
                                                        <i class="fas fa-qrcode"></i> Generar QR
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>          @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tienes clases programadas para hoy</h5>
                            <p class="text-muted">¡Disfruta tu día libre!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Horario Semanal -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-week"></i> Mi Horario Semanal
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Día</th>
                                    <th>Horario</th>
                                    <th>Materia</th>
                                    <th>Aula</th>
                                    <th>Grupo</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                                @endphp
                                @forelse($horariosSemana->flatten() as $horario)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $dias[$horario->dia_semana] ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</td>
                                        <td>{{ $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $horario->aula->codigo_aula ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($horario->tipo_clase) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No tienes horarios asignados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Generar QR -->
<div class="modal fade" id="modalGenerarQR" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode"></i> Generar Código QR
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="text-primary" id="infoClaseQR">Información de la clase</h6>
                    <div id="detallesClaseQR" class="text-muted small"></div>
                </div>
                
                <div class="mb-3">
                    <label for="modalidadClase" class="form-label">
                        <i class="fas fa-laptop"></i> Modalidad de la Clase
                    </label>
                    <select class="form-select" id="modalidadClase" required>
                        <option value="">Selecciona la modalidad</option>
                        <option value="presencial">
                            <i class="fas fa-building"></i> Presencial
                        </option>
                        <option value="virtual">
                            <i class="fas fa-video"></i> Virtual
                        </option>
                    </select>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Importante:</strong> El código QR será válido por 30 minutos. 
                    Los estudiantes podrán escanearlo para confirmar tu asistencia.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="generarQR()">
                    <i class="fas fa-qrcode"></i> Generar QR
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Mostrar QR -->
<div class="modal fade" id="modalMostrarQR" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode"></i> Código QR Generado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <h6 class="text-success" id="infoQRGenerado">QR generado exitosamente</h6>
                    <div id="detallesQRGenerado" class="text-muted small"></div>
                </div>

                <div class="mb-4">
                    <div id="qrCodeContainer" class="d-flex justify-content-center">
                        <!-- El QR se generará aquí -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="fas fa-info-circle"></i> Información
                                </h6>
                                <p class="mb-1"><strong>Modalidad:</strong> <span id="modalidadQR" class="badge"></span></p>
                                <p class="mb-1"><strong>Sesión:</strong> #<span id="numeroSesionQR"></span></p>
                                <p class="mb-0"><strong>Expira:</strong> <span id="expiraQR" class="text-warning"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="fas fa-share-alt"></i> Compartir
                                </h6>
                                <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="copiarEnlaceQR()">
                                    <i class="fas fa-copy"></i> Copiar Enlace
                                </button>
                                <button class="btn btn-outline-success btn-sm w-100" onclick="compartirQR()">
                                    <i class="fas fa-share"></i> Compartir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-clock"></i>
                    <strong>Recordatorio:</strong> Este código QR expirará automáticamente en 30 minutos.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="actualizarAsistencias()">
                    <i class="fas fa-sync-alt"></i> Actualizar Dashboard
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let horarioSeleccionado = null;
let qrActual = null;

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Dashboard del profesor cargado');
    
    // Verificar que Bootstrap esté disponible
    if (typeof bootstrap === 'undefined') {
        console.error('❌ Bootstrap no está disponible');
        mostrarAlerta('error', 'Error', 'Bootstrap no está cargado correctamente');
        return;
    }
    
    console.log('✅ Bootstrap está disponible');
});

// Función para mostrar modal de generar QR
function mostrarModalQR(horarioId) {
    console.log('🎯 Mostrando modal QR para horario:', horarioId);
    
    if (!horarioId) {
        console.error('❌ ID de horario no válido');
        mostrarAlerta('error', 'Error', 'ID de horario no válido');
        return;
    }
    
    horarioSeleccionado = horarioId;
    
    // Buscar la tarjeta de la clase usando un selector más específico
    const botonQR = document.querySelector(`button[onclick="mostrarModalQR(${horarioId})"]`);
    if (!botonQR) {
        console.error('❌ No se encontró el botón QR');
        mostrarAlerta('error', 'Error', 'No se pudo encontrar el botón');
        return;
    }
    
    const tarjetaClase = botonQR.closest('.card');
    if (!tarjetaClase) {
        console.error('❌ No se encontró la tarjeta de la clase');
        mostrarAlerta('error', 'Error', 'No se pudo encontrar la información de la clase');
        return;
    }
    
    const nombreMateria = tarjetaClase.querySelector('.card-title');
    const detallesElement = tarjetaClase.querySelector('.text-muted');
    
    if (!nombreMateria || !detallesElement) {
        console.error('❌ No se encontraron los elementos de información');
        mostrarAlerta('error', 'Error', 'No se pudo obtener la información de la clase');
        return;
    }
    
    const nombreMateriaTexto = nombreMateria.textContent.trim();
    const detalles = detallesElement.innerHTML;
    
    console.log('📚 Materia:', nombreMateriaTexto);
    
    // Actualizar modal
    document.getElementById('infoClaseQR').textContent = nombreMateriaTexto;
    document.getElementById('detallesClaseQR').innerHTML = detalles;
    document.getElementById('modalidadClase').value = '';
    
    // Mostrar modal
    const modalElement = document.getElementById('modalGenerarQR');
    if (!modalElement) {
        console.error('❌ Modal no encontrado');
        mostrarAlerta('error', 'Error', 'Modal no encontrado');
        return;
    }
    
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('✅ Modal mostrado correctamente');
    } catch (error) {
        console.error('❌ Error mostrando modal:', error);
        mostrarAlerta('error', 'Error', 'Error al mostrar el modal: ' + error.message);
    }
}

// Función para generar QR
function generarQR() {
    console.log('🚀 Iniciando generación de QR...');
    
    const modalidad = document.getElementById('modalidadClase').value;
    
    if (!modalidad) {
        console.log('❌ No se seleccionó modalidad');
        mostrarAlerta('error', 'Error', 'Debes seleccionar la modalidad de la clase');
        return;
    }
    
    if (!horarioSeleccionado) {
        console.error('❌ No hay horario seleccionado');
        mostrarAlerta('error', 'Error', 'No hay horario seleccionado');
        return;
    }

    const boton = document.querySelector('[onclick="generarQR()"]');
    if (boton) {
        boton.disabled = true;
        boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
    }

    const url = '{{ route("profesor.generar-qr") }}';
    const token = document.querySelector('meta[name="csrf-token"]');
    
    if (!token) {
        console.error('❌ CSRF Token no encontrado');
        mostrarAlerta('error', 'Error', 'Token CSRF no encontrado');
        if (boton) {
            boton.disabled = false;
            boton.innerHTML = '<i class="fas fa-qrcode"></i> Generar QR';
        }
        return;
    }

    const requestData = {
        horario_id: horarioSeleccionado,
        modalidad: modalidad
    };
    
    console.log('📤 Enviando datos:', requestData);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('📥 Respuesta:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📊 Datos recibidos:', data);
        
        if (boton) {
            boton.disabled = false;
            boton.innerHTML = '<i class="fas fa-qrcode"></i> Generar QR';
        }
        
        if (data.success) {
            console.log('✅ QR generado exitosamente');
            
            // Cerrar modal de generar
            const modalGenerarElement = document.getElementById('modalGenerarQR');
            const modalInstance = bootstrap.Modal.getInstance(modalGenerarElement);
            if (modalInstance) {
                modalInstance.hide();
            }
            
            mostrarAlerta('success', 'QR Generado', data.message);
            
            // Abrir QR en nueva ventana
            const qrVistaUrl = `{{ url('/profesor/qr-vista') }}/${data.data.qr_token}`;
            window.open(qrVistaUrl, '_blank', 'width=800,height=900,scrollbars=yes,resizable=yes');
            
            // Actualizar página después de 1 segundo (forzar recarga sin caché)
            setTimeout(() => {
                // Agregar timestamp para evitar caché
                window.location.href = window.location.href.split('?')[0] + '?t=' + new Date().getTime();
            }, 1000);
        } else {
            console.log('❌ Error en respuesta:', data.error);
            mostrarAlerta('error', 'Error', data.error || 'Error al generar QR');
        }
    })
    .catch(error => {
        console.error('💥 Error en fetch:', error);
        
        if (boton) {
            boton.disabled = false;
            boton.innerHTML = '<i class="fas fa-qrcode"></i> Generar QR';
        }
        
        mostrarAlerta('error', 'Error', 'Error de conexión: ' + error.message);
    });
}

// Función para mostrar QR generado
function mostrarQRGenerado(horarioId, qrToken, datosQR = null) {
    console.log('📱 Mostrando QR generado');
    
    const qrUrl = `{{ url('/profesor/qr') }}/${qrToken}`;
    qrActual = qrUrl;
    
    // Actualizar información del modal
    document.getElementById('infoQRGenerado').textContent = 'QR generado exitosamente';
    
    if (datosQR) {
        const modalidadElement = document.getElementById('modalidadQR');
        const numeroSesionElement = document.getElementById('numeroSesionQR');
        const expiraElement = document.getElementById('expiraQR');
        
        if (modalidadElement) {
            modalidadElement.textContent = datosQR.modalidad;
            modalidadElement.className = `badge bg-${datosQR.modalidad === 'presencial' ? 'primary' : 'info'}`;
        }
        
        if (numeroSesionElement) {
            numeroSesionElement.textContent = datosQR.numero_sesion;
        }
        
        if (expiraElement) {
            expiraElement.textContent = datosQR.expira_en;
        }
    }
    
    // Mostrar código QR usando la imagen generada por Laravel
    const qrContainer = document.getElementById('qrCodeContainer');
    if (!qrContainer) {
        console.error('❌ Contenedor QR no encontrado');
        return;
    }
    
    qrContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Cargando QR...</div>';
    
    if (datosQR && datosQR.qr_image_url) {
        // Usar la imagen QR generada por Laravel
        const qrImage = document.createElement('img');
        qrImage.src = datosQR.qr_image_url;
        qrImage.className = 'border rounded';
        qrImage.style.maxWidth = '300px';
        qrImage.style.height = 'auto';
        
        qrImage.onload = function() {
            qrContainer.innerHTML = '';
            qrContainer.appendChild(qrImage);
            console.log('✅ QR imagen cargada desde Laravel');
        };
        
        qrImage.onerror = function() {
            console.error('❌ Error cargando imagen QR');
            qrContainer.innerHTML = `<div class="alert alert-warning">
                <p><strong>QR generado exitosamente</strong></p>
                <p>URL: <a href="${qrUrl}" target="_blank" class="btn btn-primary btn-sm">Abrir QR</a></p>
                <button class="btn btn-sm btn-outline-primary" onclick="copiarEnlaceQR()">Copiar Enlace</button>
            </div>`;
        };
    } else {
        // Fallback si no hay imagen
        qrContainer.innerHTML = `<div class="alert alert-info">
            <p><strong>QR generado exitosamente</strong></p>
            <p><a href="${qrUrl}" target="_blank" class="btn btn-primary">Abrir QR</a></p>
            <button class="btn btn-sm btn-outline-primary" onclick="copiarEnlaceQR()">Copiar Enlace</button>
        </div>`;
    }
    
    // Mostrar modal
    const modalElement = document.getElementById('modalMostrarQR');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
}

// Función para copiar enlace QR
function copiarEnlaceQR() {
    if (!qrActual) {
        mostrarAlerta('error', 'Error', 'No hay enlace QR disponible');
        return;
    }
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(qrActual).then(function() {
            mostrarAlerta('success', 'Copiado', 'Enlace copiado al portapapeles');
        }).catch(function(error) {
            console.error('Error copiando:', error);
            mostrarAlerta('error', 'Error', 'No se pudo copiar el enlace');
        });
    } else {
        // Fallback para navegadores sin clipboard API
        const textArea = document.createElement('textarea');
        textArea.value = qrActual;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            mostrarAlerta('success', 'Copiado', 'Enlace copiado al portapapeles');
        } catch (error) {
            mostrarAlerta('error', 'Error', 'No se pudo copiar el enlace');
        }
        document.body.removeChild(textArea);
    }
}

// Función para compartir QR
function compartirQR() {
    if (!qrActual) return;
    
    if (navigator.share) {
        navigator.share({
            title: 'Código QR - Asistencia Docente',
            text: 'Escanea este código QR para confirmar la asistencia del profesor',
            url: qrActual
        }).catch(console.error);
    } else {
        copiarEnlaceQR();
    }
}

// Función para abrir QR existente
function abrirQRExistente(token) {
    const qrVistaUrl = `{{ url('/profesor/qr-vista') }}/${token}`;
    window.open(qrVistaUrl, '_blank', 'width=800,height=900,scrollbars=yes,resizable=yes');
}

// Función para actualizar asistencias
function actualizarAsistencias() {
    // Agregar timestamp para evitar caché
    window.location.href = window.location.href.split('?')[0] + '?t=' + new Date().getTime();
}

// Función para mostrar alertas
function mostrarAlerta(tipo, titulo, mensaje) {
    // Remover alerta existente
    const alertaExistente = document.getElementById('alerta-temporal');
    if (alertaExistente) {
        alertaExistente.remove();
    }
    
    const tipoClase = tipo === 'success' ? 'alert-success' : 'alert-danger';
    const icono = tipo === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    
    const alerta = document.createElement('div');
    alerta.id = 'alerta-temporal';
    alerta.className = `alert ${tipoClase} alert-dismissible fade show position-fixed`;
    alerta.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
    alerta.innerHTML = `
        <i class="${icono}"></i>
        <strong>${titulo}:</strong> ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alerta);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (alerta && alerta.parentNode) {
            alerta.remove();
        }
    }, 5000);
}
</script>
@endsection