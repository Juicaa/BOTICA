<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../frontend/views/index.html");
    exit();
}

require '../../backend/config/conexion.php';

// Lotes por vencer
$hoy = date('Y-m-d');
$limite = date('Y-m-d', strtotime('+15 days'));

$stmt1 = $conn->prepare("
    SELECT l.id_lote, m.nombre AS medicamento, l.fecha_vencimiento, l.cantidad 
    FROM Lotes l
    JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento
    WHERE l.fecha_vencimiento BETWEEN ? AND ?
");
$stmt1->execute([$hoy, $limite]);
$vencimientos = $stmt1->fetchAll(PDO::FETCH_ASSOC);

// Lotes con stock bajo
$stmt2 = $conn->prepare("
    SELECT l.id_lote, m.nombre AS medicamento, l.cantidad 
    FROM Lotes l
    JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento
    WHERE l.cantidad < 10
");
$stmt2->execute();
$stock_bajo = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Alertas</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Alertas del Sistema</h2>
  <a href="dashboard_admin.php" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left-circle"></i> Volver al Dashboard</a>

  <div class="row">
    <div class="col-md-6">
      <h5 class="text-danger">🔴 Lotes por vencer (≤ 15 días)</h5>
      <table class="table table-bordered table-sm">
        <thead class="table-danger">
          <tr>
            <th>ID Lote</th>
            <th>Medicamento</th>
            <th>Fecha Vencimiento</th>
            <th>Cantidad</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($vencimientos as $v): ?>
          <tr>
            <td><?= $v['id_lote'] ?></td>
            <td><?= htmlspecialchars($v['medicamento']) ?></td>
            <td><?= $v['fecha_vencimiento'] ?></td>
            <td><?= $v['cantidad'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="col-md-6">
      <h5 class="text-warning">🟠 Lotes con stock bajo (&lt; 10)</h5>
      <table class="table table-bordered table-sm">
        <thead class="table-warning">
          <tr>
            <th>ID Lote</th>
            <th>Medicamento</th>
            <th>Cantidad</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($stock_bajo as $s): ?>
          <tr>
            <td><?= $s['id_lote'] ?></td>
            <td><?= htmlspecialchars($s['medicamento']) ?></td>
            <td><?= $s['cantidad'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
