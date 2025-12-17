<?php

namespace App\Providers;

use App\Database\Connectors\NeonPostgresConnector;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Reemplazar el conector pgsql con nuestro conector personalizado
        $this->app['db']->extend('pgsql', function ($config, $name) {
            $connector = new NeonPostgresConnector();
            $connection = $connector->connect($config);
            
            return new PostgresConnection(
                $connection,
                $config['database'],
                $config['prefix'] ?? '',
                $config
            );
        });
    }
}