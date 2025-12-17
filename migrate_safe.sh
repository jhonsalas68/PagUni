#!/bin/bash
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
php artisan tinker --execute="echo 'Usuarios: ' . App\Models\User::count() . PHP_EOL;"
php artisan tinker --execute="echo 'Profesores: ' . App\Models\Profesor::count() . PHP_EOL;"
php artisan tinker --execute="echo 'Estudiantes: ' . App\Models\Estudiante::count() . PHP_EOL;"

echo "=== MIGRACIÓN COMPLETADA ==="
