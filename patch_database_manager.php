<?php

// Patch para DatabaseManager - aplicar después de bootstrap
use App\Database\Connectors\NeonPostgresConnector;

// Obtener el database manager
$db = app('db');

// Reemplazar el conector PostgreSQL
$reflection = new ReflectionClass($db);
$connectorsProperty = $reflection->getProperty('connectors');
$connectorsProperty->setAccessible(true);
$connectors = $connectorsProperty->getValue($db);
$connectors['pgsql'] = new NeonPostgresConnector();
$connectorsProperty->setValue($db, $connectors);

echo "Conector PostgreSQL reemplazado con NeonPostgresConnector\n";
