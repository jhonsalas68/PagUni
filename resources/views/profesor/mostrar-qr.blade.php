@extends('layouts.app')

@section('title', 'Código QR - Asistencia')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode"></i> Código QR - Asistencia Docente
                    </h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <h5 class="text-primary">{{ $asistencia->horario->cargaAcademica->grupo->materia->nombre ?? 'Materia' }}</h5>
                        <p class="text-muted">
                            <i class="fas fa-user-tie"></i> {{ $asistencia->profesor->nombre ?? 'Profesor' }} {{ $asistencia->profesor->apellido ?? '' }}<br>
                            <i class="fas fa-calendar"></i> {{ $asistencia->fecha->format('d/m/Y') }}<br>
                            <i class="fas fa-clock"></i> {{ $asistencia->horario->hora_inicio ?? '' }} - {{ $asistencia->horario->hora_fin ?? '' }}<br>
                            <i class="fas fa-door-open"></i> {{ $asistencia->horario->aula->codigo_aula ?? 'Aula' }}
                        </p>
                    </div>

                    <!-- QR Code generado por Laravel -->
                    <div class="mb-4">
                        {!! QrCode::size(300)->margin(2)->generate(route('profesor.escanear-qr', ['token' => $token])) !!}
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fas fa-info-circle"></i> Información
                                    </h6>
                                    <p class="mb-1"><strong>Modalidad:</strong> 
                                        <span class="badge bg-{{ $asistencia->modalidad === 'presencial' ? 'primary' : 'info' }}">
                                            {{ ucfirst($asistencia->modalidad) }}
                                        </span>
                                    </p>
                                    <p class="mb-1"><strong>Sesión:</strong> #{{ $asistencia->numero_sesion }}</p>
                                    <p class="mb-0"><strong>Expira:</strong> 
                                        <span class="text-warning">{{ $asistencia->qr_generado_at->addMinutes(30)->format('H:i') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-share-alt"></i> Compartir
                                    </h6>
                                    <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="copiarEnlace()">
                                        <i class="fas fa-copy"></i> Copiar Enlace
                                    </button>
                                    <button class="btn btn-outline-success btn-sm w-100" onclick="compartir()">
                                        <i class="fas fa-share"></i> Compartir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instrucciones:</strong> Los estudiantes deben escanear este código QR para confirmar tu asistencia a la clase.
                        El código expirará automáticamente en 30 minutos.
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('profesor.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const qrUrl = '{{ route("profesor.escanear-qr", ["token" => $token]) }}';

function copiarEnlace() {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(qrUrl).then(function() {
            mostrarAlerta('success', 'Enlace copiado al portapapeles');
        }).catch(function(error) {
            console.error('Error copiando:', error);
            mostrarAlerta('error', 'No se pudo copiar el enlace');
        });
    } else {
        // Fallback
        const textArea = document.createElement('textarea');
        textArea.value = qrUrl;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            mostrarAlerta('success', 'Enlace copiado al portapapeles');
        } catch (error) {
            mostrarAlerta('error', 'No se pudo copiar el enlace');
        }
        document.body.removeChild(textArea);
    }
}

function compartir() {
    if (navigator.share) {
        navigator.share({
            title: 'Código QR - Asistencia Docente',
            text: 'Escanea este código QR para confirmar la asistencia del profesor',
            url: qrUrl
        }).catch(console.error);
    } else {
        copiarEnlace();
    }
}

function mostrarAlerta(tipo, mensaje) {
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
        <strong>${mensaje}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alerta);
    
    setTimeout(() => {
        if (alerta && alerta.parentNode) {
            alerta.remove();
        }
    }, 3000);
}
</script>
@endsection