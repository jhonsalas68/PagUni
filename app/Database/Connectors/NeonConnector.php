<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;
use PDO;

class NeonConnector extends PostgresConnector
{
    /**
     * Establish a database connection.
     *
     * @param  array  $config
     * @return \PDO
     */
    public function connect(array $config)
    {
        // Si es Neon, usar DSN personalizado
        if (isset($config['host']) && strpos($config['host'], 'neon.tech') !== false) {
            $dsn = $this->getNeonDsn($config);
            
            $connection = $this->createConnection($dsn, $config, $this->getOptions($config));
            
            return $connection;
        }
        
        // Para otros hosts, usar el conector estándar
        return parent::connect($config);
    }
    
    /**
     * Create a DSN string for Neon.
     *
     * @param  array  $config
     * @return string
     */
    protected function getNeonDsn(array $config)
    {
        $host = $config['host'];
        $port = $config['port'] ?? 5432;
        $database = $config['database'];
        
        // Extraer endpoint ID
        $endpointId = explode('.', $host)[0];
        
        return "pgsql:host={$host};port={$port};dbname={$database};sslmode=require;options=endpoint={$endpointId}";
    }
}