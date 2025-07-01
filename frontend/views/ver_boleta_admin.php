<?php
session_start();
require '../../backend/config/conexion.php';

if (!isset($_GET['id_venta'])) {
    die("ID de venta no proporcionado.");
}

$id_venta = (int)$_GET['id_venta'];

// Obtener datos generales de la venta
$stmt = $conn->prepare("
    SELECT v.id_venta, v.fecha, v.total, 
           c.nombre_completo AS cliente, c.dni, 
           u.usuario AS vendedor
    FROM ventas v
    JOIN clientes c ON v.id_cliente = c.id_cliente
    JOIN usuarios u ON v.id_usuario = u.id_usuario
    WHERE v.id_venta = ?
");
$stmt->execute([$id_venta]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    die("Venta no encontrada.");
}

// Obtener detalles de los medicamentos vendidos
$stmt = $conn->prepare("
    SELECT m.nombre AS medicamento, l.id_lote, s.cantidad, l.precio_unitario
    FROM salidalotes s
    JOIN lotes l ON s.id_lote = l.id_lote
    JOIN medicamentos m ON l.id_medicamento = m.id_medicamento
    WHERE s.id_venta = ?
");
$stmt->execute([$id_venta]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Boleta de Venta</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/boleta.css">

  <style>
    body { margin: 20px; }
    .boleta-box { border: 1px solid #ccc; padding: 20px; border-radius: 10px; }
    .boleta-header { text-align: center; margin-bottom: 20px; }
    .boleta-footer { text-align: right; margin-top: 20px; font-weight: bold; }
    .btn-print { margin-top: 10px; }
  </style>
</head>
<body>
<div class="container boleta-box">
  <div class="boleta-header">
    <h3>Boleta de Venta</h3>
    <p><strong>Farmacia Bienestar</strong><br>
    Fecha: <?= $venta['fecha'] ?></p>
  </div>

  <p><strong>Cliente:</strong> <?= htmlspecialchars($venta['cliente']) ?> (DNI: <?= $venta['dni'] ?>)</p>
  <p><strong>Vendedor:</strong> <?= htmlspecialchars($venta['vendedor']) ?></p>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>ID Lote</th>
        <th>Medicamento</th>
        <th>Precio Unitario (S/)</th>
        <th>Cantidad</th>
        <th>Subtotal (S/)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($detalles as $item): ?>
        <tr>
          <td><?= $item['id_lote'] ?></td>
          <td><?= htmlspecialchars($item['medicamento']) ?></td>
          <td><?= number_format($item['precio_unitario'], 2) ?></td>
          <td><?= $item['cantidad'] ?></td>
          <td><?= number_format($item['precio_unitario'] * $item['cantidad'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

    <div class="boleta-footer">
    Total: S/ <?= number_format($venta['total'], 2) ?>
  </div>
</div>
</body>
</html>
