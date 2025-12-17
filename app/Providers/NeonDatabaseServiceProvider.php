<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connection;
use PDO;

class NeonDatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Configurar conexión específica para Neon
        $this->app['db']->extend('pgsql', function ($config, $name) {
            // Si es conexión a Neon, configurar DSN específico
            if (isset($config['host']) && strpos($config['host'], 'neon.tech') !== false) {
                $host = $config['host'];
                $port = $config['port'] ?? 5432;
                $database = $config['database'];
                $username = $config['username'];
                $password = $config['password'];
                
                // Extraer endpoint ID del host
                $endpointId = explode('.', $host)[0];
                
                // Construir DSN con endpoint para Neon
                $dsn = "pgsql:host={$host};port={$port};dbname={$database};sslmode=require;options=endpoint={$endpointId}";
                
                try {
                    $pdo = new PDO($dsn, $username, $password, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_STRINGIFY_FETCHES => false,
                    ]);
                    
                    return new \Illuminate\Database\PostgresConnection(
                        $pdo,
                        $database,
                        $config['prefix'] ?? '',
                        $config
                    );
                } catch (\Exception $e) {
                    throw new \Exception("Error conectando a Neon Database: " . $e->getMessage());
                }
            }
            
            // Para otras conexiones PostgreSQL, usar el conector estándar
            $connector = new \Illuminate\Database\Connectors\PostgresConnector();
            $pdo = $connector->connect($config);
            
            return new \Illuminate\Database\PostgresConnection(
                $pdo,
                $config['database'],
                $config['prefix'] ?? '',
                $config
            );
        });
    }
}