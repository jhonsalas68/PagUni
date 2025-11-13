@extends('layouts.app')

@section('title', 'Código QR Inválido')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Código QR Inválido o Ya Utilizado
                    </h4>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-5x text-danger mb-4"></i>
                    <h5 class="text-danger">No se pudo confirmar la asistencia</h5>
                    <p class="lead">Código QR inválido o ya utilizado</p>
                    
                    <div class="alert alert-warning text-start mt-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle"></i> ¿Por qué sucede esto?
                        </h6>
                        <ul class="mb-0">
                            <li><strong>QR ya utilizado:</strong> Cada código QR solo puede escanearse UNA VEZ</li>
                            <li><strong>QR antiguo:</strong> El profesor generó un nuevo QR para esta clase</li>
                            <li><strong>Enlace incorrecto:</strong> El enlace puede estar incompleto o corrupto</li>
                            <li><strong>QR de otra clase:</strong> Este QR no corresponde a la clase actual</li>
                        </ul>
                    </div>

                    <div class="alert alert-info text-start">
                        <h6 class="alert-heading">
                            <i class="fas fa-lightbulb"></i> ¿Qué hacer ahora?
                        </h6>
                        <ol class="mb-0">
                            <li>Solicita al profesor que genere un <strong>NUEVO código QR</strong></li>
                            <li>Asegúrate de escanear el QR más reciente (no uno anterior)</li>
                            <li>Verifica que el enlace esté completo al copiarlo</li>
                            <li>Si el problema persiste, contacta al profesor</li>
                        </ol>
                    </div>

                    <div class="card bg-light mt-4">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-question-circle"></i> Información Importante
                            </h6>
                            <p class="mb-0 text-start">
                                <strong>Sistema de QR de un solo uso:</strong> Por seguridad, cada código QR solo puede 
                                ser escaneado una vez. Esto evita que se reutilicen códigos antiguos. El profesor debe 
                                generar un nuevo QR para cada sesión de clase.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-muted">
                            <i class="fas fa-clock"></i> Hora actual: <strong>{{ now()->format('H:i:s') }}</strong>
                        </p>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button onclick="window.location.reload()" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Intentar Nuevamente
                        </button>
                        <button onclick="window.close()" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
