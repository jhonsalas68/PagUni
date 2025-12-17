<?php
/**
 * Verificación completa de compatibilidad de base de datos
 * Revisa migraciones, modelos, relaciones y posibles conflictos
 */

echo "=== VERIFICACIÓN COMPLETA DE COMPATIBILIDAD DE BASE DE DATOS ===\n\n";

// 1. Verificar todas las migraciones
echo "1. VERIFICANDO MIGRACIONES:\n";
$migrations_dir = 'database/migrations';
$migrations = glob($migrations_dir . '/*.php');

if (empty($migrations)) {
    echo "❌ No se encontraron migraciones\n";
} else {
    echo "✅ Encontradas " . count($migrations) . " migraciones\n";
    
    // Ordenar migraciones por fecha
    sort($migrations);
    
    foreach ($migrations as $migration) {
        $filename = basename($migration);
        echo "  - $filename\n";
    }
}

// 2. Verificar estructura de migraciones críticas
echo "\n2. VERIFICANDO MIGRACIONES CRÍTICAS:\n";

$critical_migrations = [
    'create_users_table.php' => 'Tabla de usuarios base',
    'create_facultades_table.php' => 'Tabla de facultades',
    'create_carreras_table.php' => 'Tabla de carreras',
    'create_materias_table.php' => 'Tabla de materias',
    'create_profesores_table.php' => 'Tabla de profesores',
    'create_estudiantes_table.php' => 'Tabla de estudiantes',
    'create_grupos_table.php' => 'Tabla de grupos',
    'create_horarios_table.php' => 'Tabla de horarios',
    'create_aulas_table.php' => 'Tabla de aulas',
    'create_inscripciones_table.php' => 'Tabla de inscripciones',
    'create_calificaciones_table.php' => 'Tabla de calificaciones',
    'create_asistencia_docente_table.php' => 'Tabla de asistencia docente',
    'create_asistencia_estudiantes_table.php' => 'Tabla de asistencia estudiantes'
];

foreach ($critical_migrations as $migration_name => $description) {
    $found = false;
    foreach ($migrations as $migration) {
        if (strpos($migration, $migration_name) !== false) {
            echo "✅ $description - ENCONTRADA\n";
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "⚠️ $description - NO ENCONTRADA (puede tener otro nombre)\n";
    }
}

// 3. Verificar migraciones del sistema de chat
echo "\n3. VERIFICANDO MIGRACIONES DEL CHAT:\n";

$chat_migrations = [
    'create_conversations_table.php' => 'Tabla de conversaciones',
    'create_messages_table.php' => 'Tabla de mensajes',
    'create_participants_table.php' => 'Tabla de participantes',
    'add_online_status_and_group_features_to_chat_system.php' => 'Estado en línea y grupos'
];

foreach ($chat_migrations as $migration_name => $description) {
    $found = false;
    foreach ($migrations as $migration) {
        if (strpos($migration, $migration_name) !== false) {
            echo "✅ $description - ENCONTRADA\n";
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "⚠️ $description - NO ENCONTRADA\n";
    }
}

// 4. Verificar modelos principales
echo "\n4. VERIFICANDO MODELOS PRINCIPALES:\n";

$models_dir = 'app/Models';
$models = [
    'User.php' => 'Modelo de usuario base',
    'Administrador.php' => 'Modelo de administrador',
    'Profesor.php' => 'Modelo de profesor',
    'Estudiante.php' => 'Modelo de estudiante',
    'Facultad.php' => 'Modelo de facultad',
    'Carrera.php' => 'Modelo de carrera',
    'Materia.php' => 'Modelo de materia',
    'Grupo.php' => 'Modelo de grupo',
    'Horario.php' => 'Modelo de horario',
    'Aula.php' => 'Modelo de aula',
    'Inscripcion.php' => 'Modelo de inscripción',
    'Calificacion.php' => 'Modelo de calificación',
    'AsistenciaDocente.php' => 'Modelo de asistencia docente',
    'AsistenciaEstudiante.php' => 'Modelo de asistencia estudiante'
];

foreach ($models as $model => $description) {
    if (file_exists("$models_dir/$model")) {
        echo "✅ $description - ENCONTRADO\n";
    } else {
        echo "❌ $description - NO ENCONTRADO\n";
    }
}

// 5. Verificar modelos del chat
echo "\n5. VERIFICANDO MODELOS DEL CHAT:\n";

$chat_models_dir = 'app/Models/Chat';
$chat_models = [
    'Conversation.php' => 'Modelo de conversación',
    'Message.php' => 'Modelo de mensaje',
    'Participant.php' => 'Modelo de participante',
    'UserOnlineStatus.php' => 'Modelo de estado en línea'
];

foreach ($chat_models as $model => $description) {
    if (file_exists("$chat_models_dir/$model")) {
        echo "✅ $description - ENCONTRADO\n";
    } else {
        echo "❌ $description - NO ENCONTRADO\n";
    }
}

// 6. Verificar configuración de base de datos
echo "\n6. VERIFICANDO CONFIGURACIÓN DE BASE DE DATOS:\n";

if (file_exists('config/database.php')) {
    echo "✅ Archivo de configuración de BD encontrado\n";
    
    $db_config = file_get_contents('config/database.php');
    
    // Verificar drivers soportados
    if (strpos($db_config, "'mysql'") !== false) {
        echo "✅ Soporte para MySQL configurado\n";
    }
    if (strpos($db_config, "'pgsql'") !== false) {
        echo "✅ Soporte para PostgreSQL configurado\n";
    }
    if (strpos($db_config, "'sqlite'") !== false) {
        echo "✅ Soporte para SQLite configurado\n";
    }
} else {
    echo "❌ Archivo de configuración de BD NO encontrado\n";
}

// 7. Verificar archivo .env
echo "\n7. VERIFICANDO CONFIGURACIÓN DE ENTORNO:\n";

if (file_exists('.env')) {
    echo "✅ Archivo .env encontrado\n";
    
    $env_content = file_get_contents('.env');
    
    $env_vars = [
        'DB_CONNECTION' => 'Tipo de conexión de BD',
        'DB_HOST' => 'Host de la BD',
        'DB_PORT' => 'Puerto de la BD',
        'DB_DATABASE' => 'Nombre de la BD',
        'DB_USERNAME' => 'Usuario de la BD',
        'DB_PASSWORD' => 'Contraseña de la BD'
    ];
    
    foreach ($env_vars as $var => $description) {
        if (strpos($env_content, $var) !== false) {
            echo "✅ $description configurado\n";
        } else {
            echo "❌ $description NO configurado\n";
        }
    }
} else {
    echo "❌ Archivo .env NO encontrado\n";
    if (file_exists('.env.example')) {
        echo "✅ Archivo .env.example encontrado (copiar a .env)\n";
    }
}

// 8. Verificar seeders
echo "\n8. VERIFICANDO SEEDERS:\n";

$seeders_dir = 'database/seeders';
$seeders = glob($seeders_dir . '/*.php');

if (empty($seeders)) {
    echo "❌ No se encontraron seeders\n";
} else {
    echo "✅ Encontrados " . count($seeders) . " seeders\n";
    
    foreach ($seeders as $seeder) {
        $filename = basename($seeder);
        echo "  - $filename\n";
    }
}

// 9. Verificar posibles conflictos en migraciones
echo "\n9. VERIFICANDO POSIBLES CONFLICTOS:\n";

$potential_issues = [];

// Verificar migraciones duplicadas o conflictivas
$migration_names = [];
foreach ($migrations as $migration) {
    $filename = basename($migration);
    $parts = explode('_', $filename);
    if (count($parts) >= 4) {
        $table_name = $parts[3]; // Generalmente el nombre de la tabla
        if (isset($migration_names[$table_name])) {
            $potential_issues[] = "Posible conflicto: múltiples migraciones para '$table_name'";
        }
        $migration_names[$table_name] = $filename;
    }
}

if (empty($potential_issues)) {
    echo "✅ No se detectaron conflictos obvios\n";
} else {
    foreach ($potential_issues as $issue) {
        echo "⚠️ $issue\n";
    }
}

// 10. Verificar dependencias de Laravel
echo "\n10. VERIFICANDO DEPENDENCIAS DE LARAVEL:\n";

if (file_exists('composer.json')) {
    echo "✅ Archivo composer.json encontrado\n";
    
    $composer = json_decode(file_get_contents('composer.json'), true);
    
    if (isset($composer['require']['laravel/framework'])) {
        echo "✅ Laravel Framework: " . $composer['require']['laravel/framework'] . "\n";
    }
    
    // Verificar dependencias importantes
    $important_deps = [
        'doctrine/dbal' => 'Para modificaciones de esquema',
        'maatwebsite/excel' => 'Para exportación Excel',
        'barryvdh/laravel-dompdf' => 'Para generación PDF'
    ];
    
    foreach ($important_deps as $dep => $description) {
        if (isset($composer['require'][$dep])) {
            echo "✅ $description: " . $composer['require'][$dep] . "\n";
        } else {
            echo "⚠️ $description: NO instalado\n";
        }
    }
} else {
    echo "❌ Archivo composer.json NO encontrado\n";
}

echo "\n=== RECOMENDACIONES PARA DESPLIEGUE ===\n";
echo "1. Ejecutar: php artisan migrate:status (verificar estado)\n";
echo "2. Ejecutar: php artisan migrate --force (aplicar migraciones)\n";
echo "3. Ejecutar: php artisan db:seed --force (poblar datos iniciales)\n";
echo "4. Verificar permisos de storage/ y bootstrap/cache/\n";
echo "5. Ejecutar: php artisan config:cache\n";
echo "6. Ejecutar: php artisan route:cache\n";
echo "7. Ejecutar: php artisan view:cache\n";
echo "8. Configurar correctamente el .env para producción\n";

echo "\n=== COMANDOS DE VERIFICACIÓN POST-DESPLIEGUE ===\n";
echo "php artisan tinker\n";
echo ">>> App\\Models\\User::count()\n";
echo ">>> App\\Models\\Profesor::count()\n";
echo ">>> App\\Models\\Estudiante::count()\n";
echo ">>> App\\Models\\Materia::count()\n";

echo "\n✅ VERIFICACIÓN COMPLETA FINALIZADA\n";
?>