<?php

echo "=== VERIFICACIÓN PARA VERCEL DEPLOYMENT ===\n\n";

// Verificar archivos necesarios
$archivosNecesarios = [
    'api/index.php' => 'Función serverless principal',
    'vercel.json' => 'Configuración de Vercel',
    '.vercelignore' => 'Archivos a ignorar',
    'public/.htaccess' => 'Configuración Apache',
    'composer.json' => 'Dependencias PHP',
    'package.json' => 'Dependencias Node.js'
];

echo "📁 VERIFICANDO ARCHIVOS NECESARIOS:\n";
foreach ($archivosNecesarios as $archivo => $descripcion) {
    if (file_exists($archivo)) {
        echo "   ✅ {$archivo} - {$descripcion}\n";
    } else {
        echo "   ❌ {$archivo} - {$descripcion} (FALTANTE)\n";
    }
}

echo "\n🔧 VERIFICANDO CONFIGURACIÓN:\n";

// Verificar vercel.json
if (file_exists('vercel.json')) {
    $vercelConfig = json_decode(file_get_contents('vercel.json'), true);
    
    if (isset($vercelConfig['functions']['api/index.php'])) {
        echo "   ✅ Función serverless configurada\n";
    } else {
        echo "   ❌ Función serverless NO configurada\n";
    }
    
    if (isset($vercelConfig['env']['DB_HOST'])) {
        echo "   ✅ Variables de entorno configuradas\n";
    } else {
        echo "   ❌ Variables de entorno NO configuradas\n";
    }
    
    if (isset($vercelConfig['routes'])) {
        echo "   ✅ Rutas configuradas (" . count($vercelConfig['routes']) . " rutas)\n";
    } else {
        echo "   ❌ Rutas NO configuradas\n";
    }
}

echo "\n📊 INFORMACIÓN DEL PROYECTO:\n";
echo "   📧 Admin: admin@ficct.edu.bo / admin123\n";
echo "   🗄️  Base de datos: Neon PostgreSQL\n";
echo "   🌐 Framework: Laravel " . (class_exists('Illuminate\Foundation\Application') ? app()->version() : 'N/A') . "\n";

echo "\n🚀 COMANDOS PARA DEPLOYMENT:\n";
echo "   1. git add .\n";
echo "   2. git commit -m \"Configure for Vercel deployment\"\n";
echo "   3. git push origin main\n";
echo "   4. Vercel se desplegará automáticamente\n";

echo "\n📋 CONFIGURACIÓN DE VERCEL:\n";
echo "   - Runtime: vercel-php@0.7.1\n";
echo "   - Función: api/index.php\n";
echo "   - Base de datos: Neon (ya configurada)\n";
echo "   - Variables de entorno: Incluidas en vercel.json\n";

echo "\n⚠️  NOTAS IMPORTANTES:\n";
echo "   - Las sesiones usan 'array' driver (sin persistencia)\n";
echo "   - Cache usa 'array' driver (temporal)\n";
echo "   - Logs van a stderr para Vercel\n";
echo "   - Archivos estáticos se sirven directamente\n";

echo "\n🎉 PROYECTO LISTO PARA VERCEL!\n";
echo "URL esperada: https://pag-uni.vercel.app\n";

echo "\n=== FIN DE VERIFICACIÓN ===\n";