<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: ../index.html");
    exit();
}

require '../../backend/config/conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    die("Usuario no identificado.");
}

// Obtener ventas del día hechas por este vendedor, incluyendo datos del cliente
$stmt = $conn->prepare("
    SELECT v.id_venta, v.fecha, v.total, c.nombre_completo, c.dni
    FROM ventas v
    JOIN clientes c ON v.id_cliente = c.id_cliente
    WHERE v.id_usuario = ? AND DATE(v.fecha) = CURDATE()
    ORDER BY v.fecha DESC
");
$stmt->execute([$id_usuario]);
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Ventas</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
  <div class="container p-4">
    <h2 class="mb-4">Ventas de hoy</h2>
    <a href="dashboard_vendedor.php" class="btn btn-outline-dark mb-3">
      <i class="bi bi-arrow-left-circle"></i> Volver al Dashboard
    </a>

    <?php if (count($ventas) > 0): ?>
      <?php foreach ($ventas as $venta): ?>
        <div class="card mb-4">
          <div class="card-header bg-success text-white">
            <strong>Venta ID:</strong> <?= $venta['id_venta'] ?> |
            <strong>Fecha:</strong> <?= $venta['fecha'] ?> |
            <strong>Total:</strong> S/ <?= number_format($venta['total'], 2) ?><br>
            <strong>Cliente:</strong> <?= htmlspecialchars($venta['nombre_completo']) ?> (DNI: <?= $venta['dni'] ?>)
            <a href="ver_boleta_historial.php?id_venta=<?= $venta['id_venta'] ?>" class="btn btn-warning">
                <i class="bi bi-receipt"></i></a>
          </div>
          <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID Lote</th>
                  <th>Medicamento</th>
                  <th>Cantidad Vendida</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $detalleStmt = $conn->prepare("
                  SELECT s.id_lote, s.cantidad, m.nombre AS nombre_medicamento
                  FROM salidalotes s
                  JOIN lotes l ON s.id_lote = l.id_lote
                  JOIN medicamentos m ON l.id_medicamento = m.id_medicamento
                  WHERE s.id_venta = ?
                ");
                $detalleStmt->execute([$venta['id_venta']]);
                $detalles = $detalleStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($detalles as $detalle): ?>
                  <tr>
                    <td><?= $detalle['id_lote'] ?></td>
                    <td><?= htmlspecialchars($detalle['nombre_medicamento']) ?></td>
                    <td><?= $detalle['cantidad'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="alert alert-info text-center">No has realizado ventas hoy.</div>
    <?php endif; ?>
  </div>
</body>
</html>
