<?php
/**
 * Database Connection logic using PDO
 */
$host = 'localhost';
$db   = 'sanjanaraj';
$user = 'root'; // Default XAMPP username
$pass = '';     // Default XAMPP password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In production, we shouldn't throw error to public. Logging it is better.
    die("Database connection failed. Please ensure the database exists and credentials are correct.");
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
