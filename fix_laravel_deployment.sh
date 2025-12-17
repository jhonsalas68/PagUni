#!/bin/bash

echo "=== REGENERACIÓN LIMPIA DE DEPENDENCIAS LARAVEL ==="

# 1. Limpiar cache de composer
rm -rf vendor/
rm -f composer.lock

# 2. Regenerar autoload y dependencias
composer install --no-dev --optimize-autoloader

# 3. Verificar estructura Laravel crítica
echo "Verificando archivos críticos:"
echo "✓ bootstrap/app.php: $(test -f bootstrap/app.php && echo "EXISTS" || echo "MISSING")"
echo "✓ bootstrap/providers.php: $(test -f bootstrap/providers.php && echo "EXISTS" || echo "MISSING")"
echo "✓ public/index.php: $(test -f public/index.php && echo "EXISTS" || echo "MISSING")"
echo "✓ config/app.php: $(test -f config/app.php && echo "EXISTS" || echo "MISSING")"

# 4. Crear directorios de storage necesarios
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# 5. Configurar permisos
chmod -R 755 storage bootstrap/cache

echo "=== REGENERACIÓN COMPLETADA ==="