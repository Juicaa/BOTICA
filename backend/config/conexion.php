<?php
$host = 'localhost';
$dbname = 'u537253387_botica'; 
$username = 'u537253387_botica';           
$password = 'u537253387B';              
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
