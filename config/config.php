<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nexttrak');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('APP_NAME', 'NextTrak');
define('APP_URL', 'http://localhost/NextTrak');

// Security configuration
define('HASH_ALGO', PASSWORD_DEFAULT);
define('SESSION_LIFETIME', 1800); // 1 hour

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('UTC');