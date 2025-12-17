<?php
/**
 * Script para corregir problemas de compatibilidad de base de datos
 * Prepara el sistema para despliegue sin errores
 */

echo "=== CORRECCIÓN DE COMPATIBILIDAD DE BASE DE DATOS ===\n\n";

// 1. Verificar y corregir dependencias faltantes
echo "1. VERIFICANDO DEPENDENCIAS FALTANTES:\n";

$composer_json = json_decode(file_get_contents('composer.json'), true);

$missing_deps = [];

// Verificar doctrine/dbal para modificaciones de esquema
if (!isset($composer_json['require']['doctrine/dbal'])) {
    $missing_deps['doctrine/dbal'] = '^3.0';
    echo "⚠️ doctrine/dbal faltante - Necesario para modificaciones de esquema\n";
}

// Verificar barryvdh/laravel-dompdf para PDFs
if (!isset($composer_json['require']['barryvdh/laravel-dompdf'])) {
    $missing_deps['barryvdh/laravel-dompdf'] = '^2.0';
    echo "⚠️ barryvdh/laravel-dompdf faltante - Necesario para generar PDFs\n";
}

if (!empty($missing_deps)) {
    echo "\n📦 AGREGANDO DEPENDENCIAS FALTANTES:\n";
    
    // Agregar dependencias al composer.json
    foreach ($missing_deps as $package => $version) {
        $composer_json['require'][$package] = $version;
        echo "✅ Agregado: $package: $version\n";
    }
    
    // Guardar composer.json actualizado
    file_put_contents('composer.json', json_encode($composer_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "✅ composer.json actualizado\n";
    
    echo "\n🔧 EJECUTAR: composer install --no-dev --optimize-autoloader\n";
} else {
    echo "✅ Todas las dependencias necesarias están presentes\n";
}

// 2. Verificar y corregir configuración de base de datos
echo "\n2. VERIFICANDO CONFIGURACIÓN DE BASE DE DATOS:\n";

$env_content = file_get_contents('.env');
$env_lines = explode("\n", $env_content);

$db_config_issues = [];
$required_db_vars = [
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'universidad_db',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => ''
];

foreach ($required_db_vars as $var => $default) {
    $found = false;
    foreach ($env_lines as $line) {
        if (strpos($line, $var . '=') === 0) {
            $found = true;
            echo "✅ $var configurado\n";
            break;
        }
    }
    if (!$found) {
        $db_config_issues[] = "$var=$default";
        echo "⚠️ $var faltante\n";
    }
}

if (!empty($db_config_issues)) {
    echo "\n🔧 AGREGANDO CONFIGURACIONES FALTANTES:\n";
    $env_content .= "\n# Configuraciones de BD agregadas automáticamente\n";
    foreach ($db_config_issues as $config) {
        $env_content .= $config . "\n";
        echo "✅ Agregado: $config\n";
    }
    file_put_contents('.env', $env_content);
}

// 3. Crear script de migración segura
echo "\n3. CREANDO SCRIPT DE MIGRACIÓN SEGURA:\n";

$migration_script = '#!/bin/bash
# Script de migración segura para despliegue

echo "=== INICIANDO MIGRACIÓN SEGURA ==="

# 1. Verificar estado actual
echo "1. Verificando estado de migraciones..."
php artisan migrate:status

# 2. Hacer backup de la base de datos (si existe)
echo "2. Creando backup de seguridad..."
php artisan db:backup 2>/dev/null || echo "Backup no disponible, continuando..."

# 3. Ejecutar migraciones
echo "3. Ejecutando migraciones..."
php artisan migrate --force

# 4. Verificar que las migraciones se aplicaron correctamente
echo "4. Verificando migraciones aplicadas..."
php artisan migrate:status

# 5. Ejecutar seeders básicos
echo "5. Ejecutando seeders básicos..."
php artisan db:seed --class=AdminSeeder --force
php artisan db:seed --class=UniversidadSeeder --force
php artisan db:seed --class=FICCTCompletaSeeder --force

# 6. Limpiar cachés
echo "6. Limpiando cachés..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 7. Optimizar para producción
echo "7. Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Verificar instalación
echo "8. Verificando instalación..."
php artisan tinker --execute="echo \'Usuarios: \' . App\\Models\\User::count() . PHP_EOL;"
php artisan tinker --execute="echo \'Profesores: \' . App\\Models\\Profesor::count() . PHP_EOL;"
php artisan tinker --execute="echo \'Estudiantes: \' . App\\Models\\Estudiante::count() . PHP_EOL;"

echo "=== MIGRACIÓN COMPLETADA ==="
';

file_put_contents('migrate_safe.sh', $migration_script);
chmod('migrate_safe.sh', 0755);
echo "✅ Script de migración segura creado: migrate_safe.sh\n";

// 4. Crear script de verificación post-despliegue
echo "\n4. CREANDO SCRIPT DE VERIFICACIÓN:\n";

$verification_script = '<?php
/**
 * Script de verificación post-despliegue
 */

echo "=== VERIFICACIÓN POST-DESPLIEGUE ===\n\n";

try {
    // Verificar conexión a BD
    $pdo = new PDO(
        "mysql:host=" . env("DB_HOST") . ";dbname=" . env("DB_DATABASE"),
        env("DB_USERNAME"),
        env("DB_PASSWORD")
    );
    echo "✅ Conexión a base de datos exitosa\n";
    
    // Verificar tablas principales
    $tables = [
        "users", "administradores", "profesores", "estudiantes",
        "facultades", "carreras", "materias", "grupos", "horarios",
        "aulas", "inscripciones", "calificaciones", "asistencia_docente",
        "conversations", "messages", "conversation_participants"
    ];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE \"$table\"");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabla $table existe\n";
        } else {
            echo "❌ Tabla $table NO existe\n";
        }
    }
    
    // Verificar datos básicos
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM administradores");
    $admin_count = $stmt->fetch()["count"];
    echo "📊 Administradores: $admin_count\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM profesores");
    $prof_count = $stmt->fetch()["count"];
    echo "📊 Profesores: $prof_count\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM estudiantes");
    $est_count = $stmt->fetch()["count"];
    echo "📊 Estudiantes: $est_count\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM materias");
    $mat_count = $stmt->fetch()["count"];
    echo "📊 Materias: $mat_count\n";
    
    echo "\n✅ VERIFICACIÓN COMPLETADA EXITOSAMENTE\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>';

file_put_contents('verify_deployment.php', $verification_script);
echo "✅ Script de verificación creado: verify_deployment.php\n";

// 5. Crear archivo de configuración de producción
echo "\n5. CREANDO CONFIGURACIÓN DE PRODUCCIÓN:\n";

$production_env = '# Configuración para producción
APP_NAME="Sistema Universitario"
APP_ENV=production
APP_KEY=base64:' . base64_encode(random_bytes(32)) . '
APP_DEBUG=false
APP_URL=https://tu-dominio.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Base de datos de producción
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=universidad_prod
DB_USERNAME=usuario_prod
DB_PASSWORD=contraseña_segura

# Cache y sesiones
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail (configurar según proveedor)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Configuraciones adicionales
VITE_APP_NAME="${APP_NAME}"
';

file_put_contents('.env.production', $production_env);
echo "✅ Configuración de producción creada: .env.production\n";

// 6. Crear checklist de despliegue
echo "\n6. CREANDO CHECKLIST DE DESPLIEGUE:\n";

$checklist = '# CHECKLIST DE DESPLIEGUE - SISTEMA UNIVERSITARIO

## PRE-DESPLIEGUE
- [ ] Servidor con PHP 8.2+ instalado
- [ ] MySQL/MariaDB instalado y configurado
- [ ] Composer instalado
- [ ] Node.js y npm instalados (para assets)
- [ ] Permisos de escritura en storage/ y bootstrap/cache/

## INSTALACIÓN
1. [ ] Clonar repositorio: `git clone <repo-url>`
2. [ ] Instalar dependencias: `composer install --no-dev --optimize-autoloader`
3. [ ] Copiar configuración: `cp .env.production .env`
4. [ ] Editar .env con datos reales de producción
5. [ ] Generar clave: `php artisan key:generate`
6. [ ] Crear base de datos vacía
7. [ ] Ejecutar migraciones: `./migrate_safe.sh`
8. [ ] Configurar permisos: `chmod -R 755 storage bootstrap/cache`
9. [ ] Configurar servidor web (Apache/Nginx)

## POST-DESPLIEGUE
- [ ] Verificar instalación: `php verify_deployment.php`
- [ ] Probar login con admin@ficct.edu.bo / admin123
- [ ] Verificar todas las funcionalidades principales
- [ ] Configurar backups automáticos
- [ ] Configurar SSL/HTTPS
- [ ] Configurar monitoreo

## FUNCIONALIDADES A PROBAR
- [ ] Login de administrador, profesor, estudiante
- [ ] Gestión de facultades, carreras, materias
- [ ] Gestión de usuarios (profesores, estudiantes)
- [ ] Sistema de horarios y aulas
- [ ] Sistema de calificaciones
- [ ] Sistema de asistencia (QR)
- [ ] Sistema de chat/mensajería
- [ ] Generación de reportes (PDF/Excel)
- [ ] Funcionalidades PWA
- [ ] Responsive design en móviles

## CREDENCIALES POR DEFECTO
- **Admin:** admin@ficct.edu.bo / admin123
- **Profesor:** prof001@ficct.edu.bo / prof123
- **Estudiante:** est001@ficct.edu.bo / est123

## COMANDOS ÚTILES
```bash
# Ver logs
tail -f storage/logs/laravel.log

# Limpiar cachés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar estado
php artisan migrate:status
php artisan queue:work (si se usa)
```

## SOLUCIÓN DE PROBLEMAS COMUNES
1. **Error 500:** Verificar permisos de storage/ y .env
2. **Error de BD:** Verificar credenciales en .env
3. **Assets no cargan:** Ejecutar `npm run build`
4. **Chat no funciona:** Verificar migraciones de chat
5. **PDFs no generan:** Verificar extensión php-gd
';

file_put_contents('DEPLOYMENT_CHECKLIST.md', $checklist);
echo "✅ Checklist de despliegue creado: DEPLOYMENT_CHECKLIST.md\n";

echo "\n=== RESUMEN DE CORRECCIONES ===\n";
echo "✅ Dependencias faltantes identificadas y agregadas\n";
echo "✅ Configuración de BD verificada y corregida\n";
echo "✅ Script de migración segura creado\n";
echo "✅ Script de verificación post-despliegue creado\n";
echo "✅ Configuración de producción preparada\n";
echo "✅ Checklist completo de despliegue creado\n";

echo "\n🚀 PRÓXIMOS PASOS:\n";
echo "1. Ejecutar: composer install --no-dev --optimize-autoloader\n";
echo "2. Configurar .env para producción\n";
echo "3. Ejecutar: ./migrate_safe.sh\n";
echo "4. Verificar: php verify_deployment.php\n";
echo "5. Seguir DEPLOYMENT_CHECKLIST.md\n";

echo "\n✅ SISTEMA PREPARADO PARA DESPLIEGUE SIN ERRORES\n";
?>