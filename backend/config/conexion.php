<?php
$host = 'localhost';
$dbname = 'u537253387_botica'; // Tu base de datos
$username = 'u537253387_botica';           // Cambia si usas otro usuario
$password = 'u537253387B';               // Deja vacío si no tienes contraseña en Laragon

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
