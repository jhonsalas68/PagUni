<?php

class NeonHelper 
{
    private static $pdo = null;
    
    public static function getConnection() 
    {
        if (self::$pdo === null) {
            // Usar valores directos si env() no está disponible
            $host = function_exists('env') ? env("NEON_HOST", "ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech") : "ep-calm-glitter-adgesoqd-pooler.c-2.us-east-1.aws.neon.tech";
            $port = function_exists('env') ? env("NEON_PORT", "5432") : "5432";
            $dbname = function_exists('env') ? env("NEON_DATABASE", "neondb") : "neondb";
            $user = function_exists('env') ? env("NEON_USERNAME", "neondb_owner") : "neondb_owner";
            $password = function_exists('env') ? env("NEON_PASSWORD", "npg_U0PA6dWCqayo") : "npg_U0PA6dWCqayo";
            $endpoint = function_exists('env') ? env("NEON_ENDPOINT", "ep-calm-glitter-adgesoqd") : "ep-calm-glitter-adgesoqd";
            
            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require;options=endpoint={$endpoint}";
            
            self::$pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 30,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        
        return self::$pdo;
    }
    
    public static function query($sql, $params = []) 
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function fetchAll($sql, $params = []) 
    {
        return self::query($sql, $params)->fetchAll();
    }
    
    public static function fetchOne($sql, $params = []) 
    {
        return self::query($sql, $params)->fetch();
    }
    
    public static function execute($sql, $params = []) 
    {
        return self::query($sql, $params)->rowCount();
    }
    
    public static function testConnection() 
    {
        try {
            $pdo = self::getConnection();
            $result = $pdo->query("SELECT 1 as test")->fetch();
            return $result['test'] === 1;
        } catch (Exception $e) {
            return false;
        }
    }
}