<?php

use App\Models\User;
use App\Models\Profesor;
use Illuminate\Support\Facades\Config;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Config::set('database.default', 'mysql');

$email = 'jperez@uagrm.edu.bo';
echo "Checking for User with email: $email\n";

$user = User::where('email', $email)->first();

if (!$user) {
    echo "User NOT FOUND.\n";
    // Check if professor exists
    $profesor = Profesor::where('email', $email)->first();
    if ($profesor) {
        echo "However, Profesor FOUND. Creating User for testing context (if needed, but user says they logged in).\n";
    }
} else {
    echo "User found. ID: " . $user->id . "\n";
    echo "Testing relationship \$user->profesor...\n";
    
    $profesor = $user->profesor;
    
    if ($profesor) {
        echo "SUCCESS: Relationship working. Profesor: " . $profesor->nombre . " " . $profesor->apellido . "\n";
    } else {
        echo "FAILURE: \$user->profesor is NULL.\n";
    }
}
