<?php
session_start();

// Asegúrate de que el usuario esté autenticado
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../frontend/views/index.html");
    exit();
}

// Incluir la conexión a la base de datos
require '../../backend/config/conexion.php';

// Consultas usando PDO para obtener los datos
$totalmedicamentos = $conn->query("SELECT COUNT(*) FROM medicamentos")->fetchColumn();
$stockTotal = $conn->query("SELECT COALESCE(SUM(cantidad), 0) FROM lotes")->fetchColumn();
$hoy = date('Y-m-d');
$limite = date('Y-m-d', strtotime('+15 days'));

$lotesPorVencerStmt = $conn->prepare("SELECT COUNT(*) FROM lotes WHERE fecha_vencimiento BETWEEN ? AND ?");
$lotesPorVencerStmt->execute([$hoy, $limite]);
$lotesPorVencer = $lotesPorVencerStmt->fetchColumn();

$ventasHoy = $conn->query("SELECT COALESCE(SUM(total), 0) FROM ventas WHERE DATE(fecha) = CURDATE()")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Administrador</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/dashboard.css"> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/menu_lateral.css">

</head>
<body>
  <div class="d-flex" style="min-height: 100vh;">
    <!-- Icono de las tres rayas (hamburguesa) -->
    <div class="menu-toggle" onclick="toggleMenu()">&#9776;</div>

    <!-- Incluir el menú lateral -->
    <?php include 'menu_lateral.php'; ?>

    <!-- Contenido principal -->
    <div id="contenidoPrincipal" class="flex-fill p-4 content">
      <h3 class="mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h3>

      <!-- Tarjetas resumen -->
      <div class="row g-3">
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3">
            <h6>Total medicamentos</h6>
            <h4><?php echo $totalmedicamentos; ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3">
            <h6>Stock Disponible</h6>
            <h4><?php echo $stockTotal; ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3">
            <h6>Lotes por Vencer</h6>
            <h4><?php echo $lotesPorVencer; ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3">
            <h6>Ventas del Día</h6>
            <h4>S/ <?php echo number_format($ventasHoy, 2); ?></h4>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Asegúrate de colocar estos enlaces justo antes del cierre de la etiqueta </body> -->
  <script src="../js/menu_lateral.js"></script>
  <script src="../js/dashboard_administrador.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

