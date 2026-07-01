<?php
// includes/db_connect.php

$host = 'localhost';
$dbname = 'scholarlink_db'; // The name of the database we just created
$username = 'root';         // Default XAMPP username
$password = '';             // Default XAMPP password (leave blank)

try {
    // Create a new PDO instance
$pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set error mode to Exception so it throws errors if queries fail
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch data as associative arrays by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If connection fails, stop the page and show the error
    die("Database Connection Failed: " . $e->getMessage());
}
?>