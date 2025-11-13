@extends('layouts.app')

@section('title', 'Confirmar Asistencia')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode"></i> Confirmar Asistencia
                    </h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-chalkboard-teacher fa-4x text-primary mb-3"></i>
                        <h5>¡Código QR Válido!</h5>
                        <p class="text-muted">Confirma tu asistencia a la siguiente clase:</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fas fa-book"></i> Información de la Clase
                                    </h6>
                                    <p class="mb-1"><strong>Materia:</strong> {{ $asistencia->horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Grupo:</strong> {{ $asistencia->horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Aula:</strong> {{ $asistencia->horario->aula->codigo_aula ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Horario:</strong> {{ $asistencia->horario->hora_inicio }} - {{ $asistencia->horario->hora_fin }}</p>
                                    <p class="mb-0"><strong>Sesión:</strong> #{{ $asistencia->numero_sesion }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-user-tie"></i> Información del Profesor
                                    </h6>
                                    <p class="mb-1"><strong>Nombre:</strong> {{ $asistencia->profesor->nombre ?? 'N/A' }} {{ $asistencia->profesor->apellido ?? '' }}</p>
                                    <p class="mb-1"><strong>Fecha:</strong> {{ $asistencia->fecha->format('d/m/Y') }}</p>
                                    <p class="mb-1"><strong>Modalidad:</strong> 
                                        <span class="badge bg-{{ $asistencia->modalidad === 'presencial' ? 'primary' : 'info' }}">
                                            {{ ucfirst($asistencia->modalidad) }}
                                        </span>
                                    </p>
                                    <p class="mb-0"><strong>Generado:</strong> {{ $asistencia->qr_generado_at->format('H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Importante:</strong> Al confirmar tu asistencia, se registrará automáticamente tu entrada a la clase.
                        Este código QR expirará en {{ $asistencia->qr_generado_at->addMinutes(30)->diffForHumans() }}.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-lg" onclick="confirmarAsistencia()">
                            <i class="fas fa-check-circle"></i> Confirmar Mi Asistencia
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="window.close()">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="resultadoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <h5 class="modal-title" id="modalTitulo">Resultado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalMensaje">
                Procesando...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="window.close()">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Bandera para evitar peticiones duplicadas
let confirmacionEnviada = false;

function confirmarAsistencia() {
    console.log('🔄 Iniciando confirmación de asistencia...');
    
    // Verificar si ya se envió
    if (confirmacionEnviada) {
        console.log('⚠️ Ya se envió una confirmación, ignorando...');
        return;
    }
    
    // Deshabilitar botón para evitar doble clic
    const boton = document.querySelector('button[onclick="confirmarAsistencia()"]');
    boton.disabled = true;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Confirmando...';

    // Obtener ubicación si es posible (con timeout corto)
    let ubicacion = null;
    if (navigator.geolocation) {
        console.log('📍 Solicitando ubicación...');
        navigator.geolocation.getCurrentPosition(
            function(position) {
                ubicacion = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy
                };
                console.log('✅ Ubicación obtenida:', ubicacion);
                enviarConfirmacion(ubicacion);
            },
            function(error) {
                console.log('⚠️ Error obteniendo ubicación:', error.message);
                enviarConfirmacion(null);
            },
            { timeout: 3000, enableHighAccuracy: false }
        );
    } else {
        console.log('⚠️ Geolocalización no disponible');
        enviarConfirmacion(null);
    }
}

function enviarConfirmacion(ubicacion) {
    // Verificar si ya se envió
    if (confirmacionEnviada) {
        console.log('⚠️ Confirmación ya enviada, ignorando petición duplicada...');
        return;
    }
    
    // Marcar como enviada
    confirmacionEnviada = true;
    
    console.log('📤 Enviando confirmación al servidor...');
    
    const url = '{{ route("profesor.confirmar-qr", ["token" => $token]) }}';
    const token = '{{ csrf_token() }}';
    
    console.log('URL:', url);
    console.log('Token CSRF:', token ? 'Presente' : 'Faltante');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            ubicacion: ubicacion
        })
    })
    .then(response => {
        console.log('📥 Respuesta recibida:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📊 Datos recibidos:', data);
        const modal = new bootstrap.Modal(document.getElementById('resultadoModal'));
        const modalHeader = document.getElementById('modalHeader');
        const modalTitulo = document.getElementById('modalTitulo');
        const modalMensaje = document.getElementById('modalMensaje');

        if (data.success) {
            modalHeader.className = 'modal-header bg-success text-white';
            modalTitulo.innerHTML = '<i class="fas fa-check-circle"></i> ¡Asistencia Confirmada!';
            modalMensaje.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>${data.message}</h5>
                    <div class="mt-3">
                        <p><strong>Estado:</strong> <span class="badge bg-success">${data.data.estado_texto}</span></p>
                        <p><strong>Modalidad:</strong> <span class="badge bg-primary">${data.data.modalidad}</span></p>
                        <p><strong>Hora de entrada:</strong> ${data.data.hora_entrada}</p>
                        ${!data.data.validado_en_horario ? '<p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Registrado con tardanza</p>' : ''}
                    </div>
                    <div class="mt-3">
                        <p class="text-muted">Esta ventana se cerrará automáticamente en <span id="countdown">3</span> segundos...</p>
                    </div>
                </div>
            `;
            
            // Mostrar modal
            modal.show();
            
            // Cerrar automáticamente después de 3 segundos
            let segundos = 3;
            const countdownElement = document.getElementById('countdown');
            const intervalo = setInterval(() => {
                segundos--;
                if (countdownElement) {
                    countdownElement.textContent = segundos;
                }
                if (segundos <= 0) {
                    clearInterval(intervalo);
                    window.close();
                    // Si window.close() no funciona (por restricciones del navegador)
                    setTimeout(() => {
                        window.location.href = 'about:blank';
                    }, 100);
                }
            }, 1000);
        } else {
            modalHeader.className = 'modal-header bg-danger text-white';
            modalTitulo.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error';
            modalMensaje.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5>No se pudo confirmar la asistencia</h5>
                    <p class="text-danger">${data.error}</p>
                </div>
            `;
            
            // Mostrar modal de error
            modal.show();
        }
    })
    .catch(error => {
        console.error('💥 Error en fetch:', error);
        
        // Permitir reintentar
        confirmacionEnviada = false;
        
        // Rehabilitar botón
        const boton = document.querySelector('button[onclick="confirmarAsistencia()"]');
        if (boton) {
            boton.disabled = false;
            boton.innerHTML = '<i class="fas fa-check-circle"></i> Confirmar Mi Asistencia';
        }
        
        const modal = new bootstrap.Modal(document.getElementById('resultadoModal'));
        const modalHeader = document.getElementById('modalHeader');
        const modalTitulo = document.getElementById('modalTitulo');
        const modalMensaje = document.getElementById('modalMensaje');

        modalHeader.className = 'modal-header bg-danger text-white';
        modalTitulo.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error de Conexión';
        modalMensaje.innerHTML = `
            <div class="text-center">
                <i class="fas fa-wifi fa-3x text-danger mb-3"></i>
                <h5>Error de conexión</h5>
                <p class="text-danger">${error.message}</p>
                <p class="text-muted">No se pudo conectar con el servidor. Verifica tu conexión a internet.</p>
                <button class="btn btn-primary mt-3" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Reintentar
                </button>
            </div>
        `;

        modal.show();
    });
}

// Verificar que Bootstrap esté cargado
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Página cargada');
    console.log('Bootstrap disponible:', typeof bootstrap !== 'undefined');
    console.log('Token CSRF:', document.querySelector('meta[name="csrf-token"]') ? 'Presente' : 'Faltante');
});
</script>
@endsection