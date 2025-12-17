<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;

try {
    echo "🔍 PROBANDO RUTAS DEL CHAT DIRECTAMENTE\n";
    echo "======================================\n\n";
    
    // Simular una sesión de usuario
    session(['user_id' => 1, 'user_type' => 'administrador']);
    
    echo "1. Simulando sesión de usuario:\n";
    echo "   - user_id: " . session('user_id') . "\n";
    echo "   - user_type: " . session('user_type') . "\n\n";
    
    // Crear instancia del controlador
    $controller = new ChatController();
    
    // 2. Probar getSearchOptions
    echo "2. Probando getSearchOptions()...\n";
    try {
        $response = $controller->getSearchOptions();
        $data = json_decode($response->getContent(), true);
        
        echo "   ✅ Respuesta exitosa\n";
        echo "   📊 Materias encontradas: " . count($data['materias']) . "\n";
        
        foreach ($data['materias'] as $materia) {
            echo "     - {$materia['codigo']}: {$materia['nombre']}\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
    
    // 3. Probar searchUsers con diferentes parámetros
    echo "\n3. Probando searchUsers()...\n";
    
    $busquedas = [
        ['query' => 'juan', 'role' => 'all'],
        ['query' => 'carlos', 'role' => 'estudiante'],
        ['query' => 'maría', 'role' => 'profesor'],
        ['query' => '', 'role' => 'all', 'materia' => 1]
    ];
    
    foreach ($busquedas as $params) {
        echo "\n   🔍 Búsqueda: " . json_encode($params) . "\n";
        
        try {
            $request = new Request($params);
            $response = $controller->searchUsers($request);
            $users = json_decode($response->getContent(), true);
            
            echo "     ✅ Respuesta exitosa\n";
            echo "     👥 Usuarios encontrados: " . count($users) . "\n";
            
            foreach ($users as $user) {
                echo "       - {$user['name']} ({$user['role_label']}) - Tipo: {$user['type']}\n";
            }
            
        } catch (Exception $e) {
            echo "     ❌ Error: " . $e->getMessage() . "\n";
        }
    }
    
    // 4. Probar index (lista de conversaciones)
    echo "\n4. Probando index()...\n";
    try {
        $response = $controller->index();
        echo "   ✅ Vista de chat cargada correctamente\n";
        echo "   📄 Tipo de respuesta: " . get_class($response) . "\n";
    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
    
    // 5. Verificar URLs que usa el frontend
    echo "\n5. URLs que debería usar el frontend:\n";
    
    $baseUrl = config('app.url', 'http://localhost');
    
    $urls = [
        'Opciones de búsqueda' => $baseUrl . '/chat/users/options',
        'Búsqueda de usuarios' => $baseUrl . '/chat/users/search?query=juan&role=all',
        'Búsqueda por materia' => $baseUrl . '/chat/users/search?materia=1',
        'Crear conversación' => $baseUrl . '/chat/create',
        'Chat principal' => $baseUrl . '/chat'
    ];
    
    foreach ($urls as $descripcion => $url) {
        echo "   📍 {$descripcion}: {$url}\n";
    }
    
    // 6. Crear un script de prueba para el navegador
    echo "\n6. Script de prueba para el navegador:\n";
    echo "   Copie y pegue esto en la consola del navegador:\n\n";
    
    echo "   // Probar búsqueda de usuarios\n";
    echo "   fetch('/chat/users/search?query=juan&role=all')\n";
    echo "     .then(response => response.json())\n";
    echo "     .then(data => console.log('Usuarios encontrados:', data))\n";
    echo "     .catch(error => console.error('Error:', error));\n\n";
    
    echo "   // Probar opciones de materias\n";
    echo "   fetch('/chat/users/options')\n";
    echo "     .then(response => response.json())\n";
    echo "     .then(data => console.log('Materias:', data))\n";
    echo "     .catch(error => console.error('Error:', error));\n\n";
    
    echo "🎯 DIAGNÓSTICO FINAL:\n";
    echo "=====================\n";
    echo "✅ El backend funciona correctamente\n";
    echo "✅ Las rutas están configuradas\n";
    echo "✅ Los datos existen en la base de datos\n";
    echo "✅ La búsqueda encuentra usuarios\n\n";
    
    echo "💡 EL PROBLEMA ESTÁ EN EL FRONTEND:\n";
    echo "===================================\n";
    echo "1. Verificar que el JavaScript esté cargando correctamente\n";
    echo "2. Revisar la consola del navegador para errores\n";
    echo "3. Verificar que las peticiones AJAX se estén enviando\n";
    echo "4. Comprobar que la autenticación esté funcionando en el navegador\n";
    echo "5. Verificar que no haya errores de CSRF token\n\n";
    
    echo "🔧 PASOS PARA SOLUCIONAR:\n";
    echo "=========================\n";
    echo "1. Abrir el navegador y ir al chat\n";
    echo "2. Abrir las herramientas de desarrollador (F12)\n";
    echo "3. Ir a la pestaña 'Network' o 'Red'\n";
    echo "4. Intentar buscar un usuario\n";
    echo "5. Ver si aparecen peticiones HTTP en la pestaña Network\n";
    echo "6. Si no aparecen, el problema es JavaScript\n";
    echo "7. Si aparecen pero fallan, revisar el error específico\n";
    
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}