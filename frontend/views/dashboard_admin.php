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
  <link href="../assets/css/footer.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/dashboard.css">  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
  <div class="d-flex" style="min-height: 100vh;">
    <!-- Menú lateral -->
    <nav class="bg-white border-end p-3" style="width: 250px;">
      <h5 class="text-success text-center mb-4">Administrador</h5>
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link active" href="dashboard_admin.php"><i class="bi bi-house-door"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="usuarios.php"><i class="bi bi-person"></i> Usuarios</a></li>
        <li class="nav-item"><a class="nav-link" href="medicamentos.php"><i class="bi bi-capsule-pill"></i> medicamentos</a></li>
        <li class="nav-item"><a class="nav-link" href="categorias.php"><i class="bi bi-tags"></i> Categorías</a></li>
        <li class="nav-item"><a class="nav-link" href="lotes.php"><i class="bi bi-box-seam"></i> lotes</a></li>
        <li class="nav-item"><a class="nav-link" href="ventas.php"><i class="bi bi-cart-check"></i> ventas</a></li>
        <li class="nav-item"><a class="nav-link" href="alertas.php"><i class="bi bi-bell"></i> Alertas</a></li>
        <li class="nav-item"><a class="nav-link" href="reportes.php"><i class="bi bi-bar-chart-line"></i> Reportes</a></li>
        <li class="nav-item"><a class="nav-link" href="estadisticas.php"><i class="bi bi-graph-up"></i> Estadísticas</a></li>
        <a class="btn btn-outline-danger w-100" href="../../backend/auth/logout.php">Cerrar sesión</a>
      </ul>
    </nav>

    <!-- Contenido principal -->
    <div class="flex-fill p-4">
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
            <h6>lotes por Vencer</h6>
            <h4><?php echo $lotesPorVencer; ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3">
            <h6>ventas del Día</h6>
            <h4>S/ <?php echo number_format($ventasHoy, 2); ?></h4>
          </div>
        </div>
      </div>

    </div>
  </div>

<script src="../js/dashboard_administrador.js"></script>

</body>
<footer class="footer">
  <p>© 2025 Botica Bienestar y Salud. Todos los derechos reservados.</p>
</footer>
</html> 
