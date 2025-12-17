<?php

namespace App\Providers;

use App\Database\Connectors\NeonConnector;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\ServiceProvider;

class NeonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['db']->extend('pgsql', function ($config, $name) {
            $connector = new NeonConnector();
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