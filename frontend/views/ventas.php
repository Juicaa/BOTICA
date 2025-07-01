<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../frontend/views/index.html");
    exit();
}

require '../../backend/config/conexion.php';

// Filtros de fecha
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$condicion = '';
$params = [];

if (!empty($desde) && !empty($hasta)) {
    $condicion = "WHERE DATE(v.fecha) BETWEEN :desde AND :hasta";
    $params[':desde'] = $desde;
    $params[':hasta'] = $hasta;
}

// Consulta principal
$query = "
    SELECT v.id_venta, v.fecha, v.total, u.usuario, c.nombre_completo AS cliente
    FROM ventas v
    JOIN usuarios u ON v.id_usuario = u.id_usuario
    JOIN clientes c ON v.id_cliente = c.id_cliente
    $condicion
    ORDER BY v.fecha DESC
";

$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de ventas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Historial de ventas</h2>
  <a href="dashboard_admin.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left-circle"></i> Volver al Dashboard
  </a>

  <!-- Filtros -->
  <form class="row g-3 mb-4" method="GET">
    <div class="col-md-4">
      <label for="desde" class="form-label">Desde</label>
      <input type="date" class="form-control" name="desde" value="<?= htmlspecialchars($desde) ?>">
    </div>
    <div class="col-md-4">
      <label for="hasta" class="form-label">Hasta</label>
      <input type="date" class="form-control" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
    </div>
    <div class="col-md-4 d-flex align-items-end gap-2">
      <button class="btn btn-success w-50">Filtrar</button>
      <a href="ventas.php" class="btn btn-outline-danger w-50">Limpiar</a>
    </div>
  </form>

  <!-- Tabla -->
  <table class="table table-bordered table-hover">
    <thead class="table-success">
      <tr>
        <th>ID Venta</th>
        <th>Fecha</th>
        <th>Total (S/)</th>
        <th>Vendedor</th>
        <th>Cliente</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($ventas as $venta): ?>
        <tr>
          <td><?= $venta['id_venta'] ?></td>
          <td><?= $venta['fecha'] ?></td>
          <td><?= number_format($venta['total'], 2) ?></td>
          <td><?= htmlspecialchars($venta['usuario']) ?></td>
          <td><?= htmlspecialchars($venta['cliente']) ?></td>
          <td>
            <a href="ver_boleta_admin.php?id_venta=<?= $venta['id_venta'] ?>" class="btn btn-warning" target="_blank">
              <i class="bi bi-receipt"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function () {
    $('.table').DataTable({
      paging: true,
      searching: false,
      info: false,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
      }
    });
  });
</script>
</body>
</html>
