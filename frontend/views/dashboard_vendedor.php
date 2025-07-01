<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: ../frontend/views/index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Vendedor</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">  
</head>
<body>
<div class="d-flex" style="min-height: 100vh;">
  <!-- Menú lateral con mismo estilo del administrador -->
  <nav class="bg-white border-end p-3" style="width: 250px;">
    <h5 class="text-success text-center mb-4">Vendedor</h5>
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link active" href="dashboard_vendedor.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
      <li class="nav-item"><a class="nav-link" href="realizar_venta.php"><i class="bi bi-cart-plus"></i> Realizar Venta</a></li>
      <li class="nav-item"><a class="nav-link" href="historial_ventas.php"><i class="bi bi-cart-check"></i> Historial de Ventas</a></li>
      <a class="btn btn-outline-danger w-100" href="../../backend/auth/logout.php">Cerrar sesión</a>
    </ul>
  </nav>

  <!-- Contenido principal -->
  <div class="flex-fill p-4">
    <h3 class="mb-4">Bienvenido, <?= htmlspecialchars($_SESSION['usuario']); ?></h3>

    <!-- Acceso directo -->
    <a href="realizar_venta.php" class="btn btn-success btn-lg">
      <i class="bi bi-cart-plus"></i> Registrar nueva venta
    </a>
  </div>
</div>
</body>
</html>
