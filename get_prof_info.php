<?php
use Illuminate\Support\Facades\Config;
use App\Models\Profesor;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Config::set('database.default', 'mysql');

$p = Profesor::where('codigo_docente', 'PROF001')->first();
if ($p) {
    echo "Nombre: " . $p->nombre . " " . $p->apellido . "\n";
    echo "Email: " . $p->email . "\n";
    echo "Codigo: " . $p->codigo_docente . "\n";
} else {
    echo "Profesor PROF001 not found.\n";
}
