<?php
$host = 'localhost';
$dbname = 'db5wgxae6r5pxe';
$username = 'u904md2c9xiua';
$password = 'liz1idfnd9bn';
 
try {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}
?>
