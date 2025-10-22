<?php
require_once __DIR__ . '/config.php';

$pdo = null;

try {

    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Disable emulated prepared statements (better security)
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS,$options);
    echo "Database connected successfully!";
}
catch (PDOException $e) {
    die("Database connection failed!!! " . $e -> getMessage());
}