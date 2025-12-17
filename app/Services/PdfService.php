<?php

namespace App\Services;

class PdfService
{
    public static function generatePdf($html, $filename = "document.pdf")
    {
        // Alternativa simple sin DomPDF
        // En producción, usar un servicio externo o instalar GD
        
        $content = "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <title>Reporte</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { margin: 20px 0; }
    </style>
</head>
<body>
    <div class=\"header\">
        <h1>Sistema Universitario</h1>
        <p>Reporte generado el " . date("d/m/Y H:i") . "</p>
    </div>
    <div class=\"content\">
        " . $html . "
    </div>
</body>
</html>";

        // Retornar HTML que se puede imprimir como PDF desde el navegador
        return response($content)
            ->header("Content-Type", "text/html")
            ->header("Content-Disposition", "inline; filename=\"$filename\"");
    }
}
