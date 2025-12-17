<?php
try {
    $dsn = "pgsql:host=127.0.0.1;port=5432;dbname=Universidad";
    $username = "postgres";
    $password = "123456789";
    
    echo "Attempting connection to $dsn with user '$username'...\n";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5, // 5 seconds timeout for test
    ];

    $start = microtime(true);
    $pdo = new PDO($dsn, $username, $password, $options);
    $end = microtime(true);
    
    echo "Connected successfully in " . round($end - $start, 4) . " seconds.\n";
    
    $stmt = $pdo->query("SELECT count(*) FROM users");
    echo "User count: " . $stmt->fetchColumn() . "\n";
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
