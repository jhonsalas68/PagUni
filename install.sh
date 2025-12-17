#!/bin/bash
# Script de instalación para servidor de producción

echo "=== INSTALACIÓN SISTEMA UNIVERSITARIO ==="

# Verificar PHP
php_version=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
echo "Versión PHP: $php_version"

if (( $(echo "$php_version < 8.2" | bc -l) )); then
    echo "❌ PHP 8.2+ requerido"
    exit 1
fi

# Verificar extensiones
echo "Verificando extensiones PHP..."
required_exts=("pdo" "pdo_mysql" "mbstring" "openssl" "tokenizer" "xml" "ctype" "json")
for ext in "${required_exts[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo "✅ $ext"
    else
        echo "❌ $ext - INSTALAR: sudo apt-get install php-$ext"
        exit 1
    fi
done

# Extensiones opcionales
optional_exts=("gd" "zip" "curl")
for ext in "${optional_exts[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo "✅ $ext"
    else
        echo "⚠️ $ext - Recomendado: sudo apt-get install php-$ext"
    fi
done

# Instalar dependencias
echo "Instalando dependencias..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader
else
    php composer.phar install --no-dev --optimize-autoloader
fi

# Configurar permisos
echo "Configurando permisos..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || echo "Ajustar permisos manualmente"

# Configurar .env
if [ ! -f .env ]; then
    echo "Configurando .env..."
    cp .env.production .env
    php artisan key:generate
    echo "⚠️ EDITAR .env con configuración real de producción"
fi

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders básicos
echo "Ejecutando seeders..."
php artisan db:seed --class=AdminSeeder --force
php artisan db:seed --class=UniversidadSeeder --force

# Optimizar
echo "Optimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ INSTALACIÓN COMPLETADA"
echo "Credenciales: admin@ficct.edu.bo / admin123"
