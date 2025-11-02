@extends('layouts.app')

@section('title', 'Código QR Inválido')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Código QR Inválido
                    </h4>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-qrcode fa-4x text-danger mb-4"></i>
                    <h5>Código QR No Válido</h5>
                    <p class="text-muted mb-4">
                        El código QR que intentas escanear no es válido o ya ha sido utilizado.
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        <strong>Posibles causas:</strong>
                        <ul class="text-start mt-2 mb-0">
                            <li>El código QR ya fue escaneado anteriormente</li>
                            <li>El código QR no pertenece a este sistema</li>
                            <li>El enlace está corrupto o incompleto</li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="window.close()">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection