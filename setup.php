<?php
// Simple database setup script
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute SQL file
    $sql = file_get_contents('database/setup.sql');
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<h2>✅ Database setup completed successfully!</h2>";
    echo "<p>The NextTrak database and tables have been created.</p>";
    echo "<p><a href='auth/login.php'>Go to Login Page</a></p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Database setup failed!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>XAMPP/MySQL is running</li>";
    echo "<li>Database credentials are correct</li>";
    echo "<li>MySQL server is accessible</li>";
    echo "</ul>";
}
?>