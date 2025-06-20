<?php
$host = 'localhost';
$dbname = 'boticabienestar'; // Tu base de datos
$username = 'root';           // Cambia si usas otro usuario
$password = '';               // Deja vacío si no tienes contraseña en Laragon

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
