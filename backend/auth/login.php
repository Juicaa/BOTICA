<?php
session_start();
require '../config/conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    if (empty($usuario) || empty($contrasena)) {
        echo "<script>alert('Por favor completa todos los campos.'); window.history.back();</script>";
        exit();
    }

    
    $stmt = $conn->prepare("SELECT id_usuario, usuario, contraseña, rol FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($fila) {
        if ($contrasena === $fila['contraseña']) {
            $_SESSION['id_usuario'] = $fila['id_usuario'];
            $_SESSION['usuario'] = $fila['usuario'];
            $_SESSION['rol'] = $fila['rol'];

            if ($fila['rol'] === 'administrador') {
                header("Location: ../../frontend/views/dashboard_admin.php");
                exit();
            } elseif ($fila['rol'] === 'vendedor') {
                header("Location: ../../frontend/views/dashboard_vendedor.php");
                exit();
            } else {
                echo "<script>alert('Rol no reconocido.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Contraseña incorrecta.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Acceso no permitido.'); window.history.back();</script>";
}
?>
