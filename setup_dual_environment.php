<?php

echo "=== CONFIGURACIÓN DUAL: LOCAL + NEON ===\n\n";

// Crear configuración para desarrollo local
$envLocal = "APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:vhyFGfuZypDI5i0SaA+4HWjR/hGpiwDxCKIqHZ7w0D8=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=America/La_Paz

# BASE DE DATOS LOCAL (DESARROLLO)
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=WebUniversidad
DB_USERNAME=postgres
DB_PASSWORD=tu_password_local

# Resto de configuración igual...
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_LEVEL=debug
";

// Crear configuración para producción Neon
$envProduction = "APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:vhyFGfuZypDI5i0SaA+4HWjR/hGpiwDxCKIqHZ7w0D8=
APP_DEBUG=false
APP_URL=https://tu-dominio.com
APP_TIMEZONE=America/La_Paz

# BASE DE DATOS NEON (PRODUCCIÓN)
# Usar scripts PDO personalizados para operaciones críticas
NEON_HOST=ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech
NEON_PORT=5432
NEON_DATABASE=neondb
NEON_USERNAME=neondb_owner
NEON_PASSWORD=npg_U0PA6dWCqayo
NEON_ENDPOINT=ep-calm-glitter-adgesoqd

# Para Laravel (funciona para consultas básicas)
DB_CONNECTION=pgsql
DB_URL=\"pgsql://neondb_owner:npg_U0PA6dWCqayo@ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech:5432/neondb?sslmode=require&options=endpoint%3Dep-calm-glitter-adgesoqd\"

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_LEVEL=error
";

// Guardar archivos de configuración
file_put_contents('.env.local', $envLocal);
file_put_contents('.env.production', $envProduction);

echo "✅ Archivos de configuración creados:\n";
echo "   - .env.local (para desarrollo)\n";
echo "   - .env.production (para producción)\n\n";

// Crear helper para conexión Neon
$neonHelper = '<?php

class NeonHelper 
{
    private static $pdo = null;
    
    public static function getConnection() 
    {
        if (self::$pdo === null) {
            $host = env("NEON_HOST", "ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech");
            $port = env("NEON_PORT", "5432");
            $dbname = env("NEON_DATABASE", "neondb");
            $user = env("NEON_USERNAME", "neondb_owner");
            $password = env("NEON_PASSWORD", "npg_U0PA6dWCqayo");
            $endpoint = env("NEON_ENDPOINT", "ep-calm-glitter-adgesoqd");
            
            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options=endpoint={$endpoint}";
            
            self::$pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 30,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        
        return self::$pdo;
    }
    
    public static function query($sql, $params = []) 
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function fetchAll($sql, $params = []) 
    {
        return self::query($sql, $params)->fetchAll();
    }
    
    public static function fetchOne($sql, $params = []) 
    {
        return self::query($sql, $params)->fetch();
    }
    
    public static function execute($sql, $params = []) 
    {
        return self::query($sql, $params)->rowCount();
    }
}
';

file_put_contents('app/Helpers/NeonHelper.php', $neonHelper);

echo "✅ Helper NeonHelper.php creado en app/Helpers/\n\n";

echo "🚀 INSTRUCCIONES DE USO:\n\n";

echo "=== PARA DESARROLLO LOCAL ===\n";
echo "1. Copia .env.local a .env:\n";
echo "   copy .env.local .env\n\n";
echo "2. Configura tu PostgreSQL local\n";
echo "3. Ejecuta migraciones normalmente:\n";
echo "   php artisan migrate\n";
echo "   php artisan db:seed\n\n";

echo "=== PARA PRODUCCIÓN NEON ===\n";
echo "1. Copia .env.production a .env:\n";
echo "   copy .env.production .env\n\n";
echo "2. Para operaciones críticas, usa NeonHelper:\n";
echo "   \$users = NeonHelper::fetchAll('SELECT * FROM administradores');\n\n";
echo "3. Para migraciones, usa el script existente:\n";
echo "   php execute_real_migrations.php\n\n";

echo "=== MIGRAR DATOS DE LOCAL A NEON ===\n";
echo "Próximo paso: Crear script de migración de datos\n\n";

echo "🎉 CONFIGURACIÓN DUAL COMPLETADA\n";
echo "Ahora puedes desarrollar localmente y desplegar en Neon sin problemas!\n";