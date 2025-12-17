<?php
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
?>