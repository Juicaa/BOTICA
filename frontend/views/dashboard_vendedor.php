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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">  
</head>
<body>
  <div class="d-flex" style="min-height: 100vh;">    
    <?php include 'menu_vendedor.php'; ?>  
  <div class="flex-fill p-4">
    <h3 class="mb-4">Bienvenido, <?= htmlspecialchars($_SESSION['usuario']); ?></h3>    
    <a href="realizar_venta.php" class="btn btn-success btn-lg">
      <i class="bi bi-cart-plus"></i> Registrar nueva venta
    </a>
  </div>
</div>
</body>
</html>
