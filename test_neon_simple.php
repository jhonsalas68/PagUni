<?php

echo "=== TEST CONEXIÓN NEON SIMPLE ===\n\n";

// Datos de conexión
$host = 'ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech';
$port = '5432';
$dbname = 'neondb';
$user = 'neondb_owner';
$password = 'npg_U0PA6dWCqayo';

// Construir DSN con endpoint
$endpoint_id = 'ep-calm-glitter-adgesoqd';
$dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options=endpoint={$endpoint_id}";

echo "🔧 CONFIGURACIÓN:\n";
echo "Host: {$host}\n";
echo "Database: {$dbname}\n";
echo "User: {$user}\n";
echo "Endpoint ID: {$endpoint_id}\n";
echo "DSN: {$dsn}\n\n";

try {
    echo "🔌 PROBANDO CONEXIÓN PDO DIRECTA...\n";
    
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30,
    ]);
    
    echo "✅ Conexión PDO exitosa\n";
    
    // Probar query
    echo "\n📊 PROBANDO QUERY...\n";
    $stmt = $pdo->query('SELECT version() as version');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "✅ Query exitosa\n";
    echo "Versión PostgreSQL: " . $result['version'] . "\n";
    
    // Verificar esquema
    echo "\n🏗️  INFORMACIÓN DEL ESQUEMA:\n";
    $stmt = $pdo->query('SELECT current_schema() as schema, current_user as user, current_database() as database');
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Esquema: " . $info['schema'] . "\n";
    echo "Usuario: " . $info['user'] . "\n";
    echo "Base de datos: " . $info['database'] . "\n";
    
    echo "\n🎉 CONEXIÓN NEON EXITOSA - CONFIGURACIÓN CORRECTA\n";
    
} catch (PDOException $e) {
    echo "❌ ERROR DE CONEXIÓN: " . $e->getMessage() . "\n";
    
    echo "\n🔧 DIAGNÓSTICO:\n";
    
    if (strpos($e->getMessage(), 'Endpoint ID') !== false) {
        echo "- El problema es el endpoint ID\n";
        echo "- Solución: Agregar ?options=endpoint%3D{$endpoint_id} a la URL\n";
    } elseif (strpos($e->getMessage(), 'authentication') !== false) {
        echo "- Problema de autenticación\n";
        echo "- Verificar usuario y contraseña\n";
    } elseif (strpos($e->getMessage(), 'timeout') !== false) {
        echo "- Problema de conectividad/timeout\n";
        echo "- Verificar conexión a internet\n";
    } else {
        echo "- Error desconocido\n";
    }
}

echo "\n=== FIN DEL TEST ===\n";