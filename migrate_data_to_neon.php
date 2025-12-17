<?php

require_once 'app/Helpers/NeonHelper.php';

echo "=== MIGRAR DATOS DE LOCAL A NEON ===\n\n";

// Configuración de base de datos local
$localHost = 'localhost';
$localPort = '5432';
$localDb = 'WebUniversidad';
$localUser = 'postgres';
$localPassword = 'tu_password_local'; // Cambiar por tu password

try {
    // Conectar a base de datos local
    echo "🔌 Conectando a base de datos local...\n";
    $localPdo = new PDO(
        "pgsql:host={$localHost};port={$localPort};dbname={$localDb}",
        $localUser,
        $localPassword,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Conexión local exitosa\n";
    
    // Probar conexión Neon
    echo "\n🔌 Probando conexión Neon...\n";
    if (!NeonHelper::testConnection()) {
        throw new Exception("No se puede conectar a Neon");
    }
    echo "✅ Conexión Neon exitosa\n";
    
    // Tablas a migrar (en orden de dependencias)
    $tablesToMigrate = [
        'facultades',
        'carreras', 
        'materias',
        'profesores',
        'estudiantes',
        'administradores',
        'aulas',
        'grupos',
        'carga_academica',
        'horarios',
        'periodos_academicos',
        'inscripciones',
        'calificaciones',
        'asistencia_docente',
        'asistencia_estudiantes'
    ];
    
    echo "\n🚀 INICIANDO MIGRACIÓN DE DATOS...\n";
    
    foreach ($tablesToMigrate as $table) {
        echo "\n📋 Migrando tabla: {$table}\n";
        
        try {
            // Verificar si la tabla existe en local
            $checkLocal = $localPdo->query("SELECT to_regclass('public.{$table}')");
            $exists = $checkLocal->fetchColumn();
            
            if (!$exists) {
                echo "   ⚠️  Tabla no existe en local, saltando...\n";
                continue;
            }
            
            // Obtener datos de la tabla local
            $stmt = $localPdo->query("SELECT * FROM {$table}");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($data)) {
                echo "   📝 Tabla vacía, saltando...\n";
                continue;
            }
            
            echo "   📊 Encontrados " . count($data) . " registros\n";
            
            // Limpiar tabla en Neon
            NeonHelper::execute("DELETE FROM {$table}");
            echo "   🧹 Tabla limpiada en Neon\n";
            
            // Insertar datos en Neon
            $inserted = 0;
            foreach ($data as $row) {
                $columns = array_keys($row);
                $placeholders = ':' . implode(', :', $columns);
                $columnsList = implode(', ', $columns);
                
                $sql = "INSERT INTO {$table} ({$columnsList}) VALUES ({$placeholders})";
                
                try {
                    NeonHelper::execute($sql, $row);
                    $inserted++;
                } catch (Exception $e) {
                    echo "   ⚠️  Error insertando registro: " . $e->getMessage() . "\n";
                }
            }
            
            echo "   ✅ {$inserted} registros migrados exitosamente\n";
            
        } catch (Exception $e) {
            echo "   ❌ Error migrando {$table}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🎉 MIGRACIÓN COMPLETADA\n";
    
    // Verificar datos migrados
    echo "\n📊 VERIFICANDO DATOS MIGRADOS...\n";
    
    foreach ($tablesToMigrate as $table) {
        try {
            $count = NeonHelper::fetchOne("SELECT COUNT(*) as total FROM {$table}");
            if ($count) {
                echo "   - {$table}: {$count['total']} registros\n";
            }
        } catch (Exception $e) {
            // Tabla no existe, continuar
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\n💡 SOLUCIÓN:\n";
    echo "1. Asegúrate de tener PostgreSQL local configurado\n";
    echo "2. Actualiza las credenciales locales en este script\n";
    echo "3. Verifica que las tablas existan en tu base local\n";
}

echo "\n=== FIN DE MIGRACIÓN ===\n";