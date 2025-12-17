<?php

namespace App\Providers;

use App\Database\Connectors\NeonPostgresConnector;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\ServiceProvider;

class NeonDatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registrar el conector en el contenedor
        $this->app->singleton('db.connector.pgsql', function () {
            return new NeonPostgresConnector();
        });
    }

    public function boot(): void
    {
        // Reemplazar el factory de conexiones PostgreSQL
        $this->app['db']->extend('pgsql', function ($config, $name) {
            $connector = $this->app->make('db.connector.pgsql');
            $pdo = $connector->connect($config);
            
            return new PostgresConnection(
                $pdo,
                $config['database'],
                $config['prefix'] ?? '',
                $config
            );
        });
    }
}