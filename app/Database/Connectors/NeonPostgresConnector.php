<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;
use PDO;

class NeonPostgresConnector extends PostgresConnector
{
    /**
     * Create a DSN string from a configuration.
     */
    protected function getDsn(array $config)
    {
        // Si es Neon, construir DSN personalizado
        if (isset($config['host']) && strpos($config['host'], 'neon.tech') !== false) {
            $host = $config['host'];
            $port = $config['port'] ?? 5432;
            $database = $config['database'];
            
            // Extraer endpoint ID
            $endpointId = explode('.', $host)[0];
            
            $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
            
            if (isset($config['sslmode'])) {
                $dsn .= ";sslmode={$config['sslmode']}";
            }
            
            // Agregar endpoint para Neon
            $dsn .= ";options=endpoint={$endpointId}";
            
            return $dsn;
        }
        
        return parent::getDsn($config);
    }
}