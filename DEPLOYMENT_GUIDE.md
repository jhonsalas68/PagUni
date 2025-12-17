# GUÍA DE DESPLIEGUE - SISTEMA UNIVERSITARIO

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
