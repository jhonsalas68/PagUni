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
function confirmarAsistencia() {
    // Deshabilitar botón para evitar doble clic
    const boton = document.querySelector('button[onclick="confirmarAsistencia()"]');
    boton.disabled = true;
    boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Confirmando...';

    // Obtener ubicación si es posible
    let ubicacion = null;
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                ubicacion = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy
                };
                enviarConfirmacion(ubicacion);
            },
            function(error) {
                console.log('Error obteniendo ubicación:', error);
                enviarConfirmacion(null);
            },
            { timeout: 5000 }
        );
    } else {
        enviarConfirmacion(null);
    }
}

function enviarConfirmacion(ubicacion) {
    fetch('{{ route("profesor.confirmar-qr", ["token" => $token]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ubicacion: ubicacion
        })
    })
    .then(response => response.json())
    .then(data => {
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
                </div>
            `;
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
        }

        modal.show();
    })
    .catch(error => {
        console.error('Error:', error);
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
                <p>No se pudo conectar con el servidor. Verifica tu conexión a internet.</p>
            </div>
        `;

        modal.show();
    });
}
</script>
@endsection