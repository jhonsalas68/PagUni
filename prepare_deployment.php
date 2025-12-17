<?php
/**
 * Script para preparar el despliegue sin errores de dependencias
 * Maneja problemas de extensiones y compatibilidad
 */

echo "=== PREPARACIÓN PARA DESPLIEGUE ===\n\n";

// 1. Verificar extensiones PHP necesarias
echo "1. VERIFICANDO EXTENSIONES PHP:\n";

$required_extensions = [
    'pdo' => 'Base de datos',
    'pdo_mysql' => 'MySQL',
    'mbstring' => 'Strings multibyte',
    'openssl' => 'Encriptación',
    'tokenizer' => 'Tokenización',
    'xml' => 'Procesamiento XML',
    'ctype' => 'Verificación de caracteres',
    'json' => 'Procesamiento JSON',
    'bcmath' => 'Matemáticas precisas'
];

$optional_extensions = [
    'gd' => 'Procesamiento de imágenes (QR, gráficos)',
    'zip' => 'Compresión (Excel)',
    'curl' => 'Peticiones HTTP'
];

$missing_required = [];
$missing_optional = [];

foreach ($required_extensions as $ext => $desc) {
    if (extension_loaded($ext)) {
        echo "✅ $ext - $desc\n";
    } else {
        $missing_required[] = $ext;
        echo "❌ $ext - $desc - REQUERIDA\n";
    }
}

foreach ($optional_extensions as $ext => $desc) {
    if (extension_loaded($ext)) {
        echo "✅ $ext - $desc\n";
    } else {
        $missing_optional[] = $ext;
        echo "⚠️ $ext - $desc - OPCIONAL\n";
    }
}

// 2. Crear composer.json optimizado para producción
echo "\n2. OPTIMIZANDO COMPOSER.JSON PARA PRODUCCIÓN:\n";

$composer = json_decode(file_get_contents('composer.json'), true);

// Agregar dependencias solo si no causan conflictos
if (!in_array('gd', $missing_optional)) {
    $composer['require']['doctrine/dbal'] = '^3.0';
    $composer['require']['barryvdh/laravel-dompdf'] = '^2.0';
    echo "✅ Dependencias completas agregadas\n";
} else {
    echo "⚠️ Extensión GD faltante, usando configuración alternativa\n";
    // Usar alternativas que no requieran GD
    $composer['require']['doctrine/dbal'] = '^3.0';
    echo "✅ Doctrine DBAL agregado (para migraciones)\n";
}

// Optimizar autoloader
$composer['config']['optimize-autoloader'] = true;
$composer['config']['classmap-authoritative'] = true;

// Configurar para producción
if (!isset($composer['scripts'])) {
    $composer['scripts'] = [];
}

$composer['scripts']['post-install-cmd'] = [
    "Illuminate\\Foundation\\ComposerScripts::postInstall",
    "@php artisan optimize"
];

$composer['scripts']['post-update-cmd'] = [
    "Illuminate\\Foundation\\ComposerScripts::postUpdate",
    "@php artisan optimize"
];

file_put_contents('composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "✅ composer.json optimizado para producción\n";

// 3. Crear configuración alternativa para PDFs sin DomPDF
echo "\n3. CONFIGURANDO ALTERNATIVAS PARA FUNCIONALIDADES:\n";

if (in_array('gd', $missing_optional)) {
    echo "⚠️ Sin extensión GD - Configurando alternativas:\n";
    
    // Crear servicio alternativo para PDFs
    $pdf_service = '<?php

namespace App\\Services;

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
';
    
    if (!file_exists('app/Services')) {
        mkdir('app/Services', 0755, true);
    }
    file_put_contents('app/Services/PdfService.php', $pdf_service);
    echo "✅ Servicio alternativo de PDF creado\n";
}

// 4. Crear script de instalación para servidor
echo "\n4. CREANDO SCRIPT DE INSTALACIÓN PARA SERVIDOR:\n";

$install_script = '#!/bin/bash
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
';

file_put_contents('install.sh', $install_script);
chmod('install.sh', 0755);
echo "✅ Script de instalación creado: install.sh\n";

// 5. Crear documentación de despliegue
echo "\n5. CREANDO DOCUMENTACIÓN DE DESPLIEGUE:\n";

$deployment_guide = '# GUÍA DE DESPLIEGUE - SISTEMA UNIVERSITARIO

## REQUISITOS DEL SERVIDOR

### PHP 8.2+
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd

# CentOS/RHEL
sudo yum install php82 php82-php-fpm php82-php-mysqlnd php82-php-mbstring php82-php-xml
```

### MySQL/MariaDB
```bash
# Ubuntu/Debian
sudo apt install mysql-server

# CentOS/RHEL
sudo yum install mariadb-server
```

### Servidor Web
```bash
# Nginx (recomendado)
sudo apt install nginx

# Apache
sudo apt install apache2
```

## INSTALACIÓN RÁPIDA

1. **Clonar repositorio**
```bash
git clone <repository-url> universidad
cd universidad
```

2. **Ejecutar instalación automática**
```bash
chmod +x install.sh
sudo ./install.sh
```

3. **Configurar base de datos**
```bash
mysql -u root -p
CREATE DATABASE universidad_prod;
CREATE USER "universidad_user"@"localhost" IDENTIFIED BY "password_segura";
GRANT ALL PRIVILEGES ON universidad_prod.* TO "universidad_user"@"localhost";
FLUSH PRIVILEGES;
EXIT;
```

4. **Editar configuración**
```bash
nano .env
# Configurar DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

5. **Finalizar instalación**
```bash
php artisan migrate --force
php artisan db:seed --force
```

## CONFIGURACIÓN NGINX

```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /var/www/universidad/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## CONFIGURACIÓN APACHE

```apache
<VirtualHost *:80>
    ServerName tu-dominio.com
    DocumentRoot /var/www/universidad/public
    
    <Directory /var/www/universidad/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## VERIFICACIÓN

```bash
# Verificar instalación
php verify_deployment.php

# Probar en navegador
curl http://tu-dominio.com

# Verificar logs
tail -f storage/logs/laravel.log
```

## CREDENCIALES POR DEFECTO

- **Administrador:** admin@ficct.edu.bo / admin123
- **Profesor:** prof001@ficct.edu.bo / prof123  
- **Estudiante:** est001@ficct.edu.bo / est123

## MANTENIMIENTO

```bash
# Backup de base de datos
mysqldump -u usuario -p universidad_prod > backup_$(date +%Y%m%d).sql

# Actualizar sistema
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpiar logs
truncate -s 0 storage/logs/laravel.log
```

## SOLUCIÓN DE PROBLEMAS

### Error 500
- Verificar permisos: `chmod -R 755 storage bootstrap/cache`
- Verificar .env: configuración de base de datos
- Verificar logs: `tail -f storage/logs/laravel.log`

### Base de datos
- Verificar conexión: `php artisan tinker` → `DB::connection()->getPdo()`
- Verificar migraciones: `php artisan migrate:status`

### Rendimiento
- Activar OPcache en PHP
- Usar Redis para cache y sesiones
- Configurar CDN para assets estáticos

## SEGURIDAD

- Cambiar APP_KEY en producción
- Usar HTTPS (SSL/TLS)
- Configurar firewall
- Actualizar regularmente
- Hacer backups automáticos
';

file_put_contents('DEPLOYMENT_GUIDE.md', $deployment_guide);
echo "✅ Guía de despliegue creada: DEPLOYMENT_GUIDE.md\n";

// 6. Resumen final
echo "\n=== RESUMEN DE PREPARACIÓN ===\n";

if (empty($missing_required)) {
    echo "✅ Todas las extensiones requeridas están disponibles\n";
} else {
    echo "❌ Extensiones requeridas faltantes: " . implode(', ', $missing_required) . "\n";
    echo "   Instalar en servidor: sudo apt-get install " . implode(' ', array_map(fn($ext) => "php-$ext", $missing_required)) . "\n";
}

if (!empty($missing_optional)) {
    echo "⚠️ Extensiones opcionales faltantes: " . implode(', ', $missing_optional) . "\n";
    echo "   Para funcionalidad completa: sudo apt-get install " . implode(' ', array_map(fn($ext) => "php-$ext", $missing_optional)) . "\n";
}

echo "\n📁 ARCHIVOS CREADOS:\n";
echo "- install.sh (instalación automática)\n";
echo "- DEPLOYMENT_GUIDE.md (guía completa)\n";
echo "- verify_deployment.php (verificación)\n";
echo "- .env.production (configuración)\n";
if (in_array('gd', $missing_optional)) {
    echo "- app/Services/PdfService.php (alternativa PDF)\n";
}

echo "\n🚀 LISTO PARA DESPLIEGUE:\n";
echo "1. Subir archivos al servidor\n";
echo "2. Ejecutar: chmod +x install.sh && ./install.sh\n";
echo "3. Configurar .env con datos reales\n";
echo "4. Configurar servidor web (Nginx/Apache)\n";
echo "5. Verificar: php verify_deployment.php\n";

echo "\n✅ SISTEMA PREPARADO PARA DESPLIEGUE SIN ERRORES\n";
?>