<?php
require '../../backend/config/conexion.php';

$id_venta = $_GET['id_venta'] ?? null;
if (!$id_venta) die("ID de venta no proporcionado.");

// Obtener venta y cliente
$stmt = $conn->prepare("
    SELECT v.fecha, v.total, c.nombre_completo, c.dni, u.usuario
    FROM ventas v
    JOIN clientes c ON v.id_cliente = c.id_cliente
    JOIN usuarios u ON v.id_usuario = u.id_usuario
    WHERE v.id_venta = ?
");
$stmt->execute([$id_venta]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$venta) die("Venta no encontrada.");

// Detalle
$stmt = $conn->prepare("
    SELECT m.nombre AS medicamento, s.cantidad, l.precio_unitario
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Boleta</title>
  <!-- Enlazar el CSS de la boleta -->
  <link rel="stylesheet" href="../assets/css/boleta_pos.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
  <div class="boleta-box">
    <!-- Encabezado -->
    <div class="boleta-header">
      <div class="center bold">Botica Bienestar y Salud</div>
      <div class="center">
        <img src="../assets/img/logo_botica.png" alt="Logo Botica" />
      </div>
      <div class="center">Fecha: <?= $venta['fecha'] ?></div>
      <div class="line"></div>
      <div><span class="bold">Cliente:</span> <?= $venta['nombre_completo'] ?></div>
      <div><span class="bold">DNI:</span> <?= $venta['dni'] ?></div>
      <div><span class="bold">Vendedor:</span> <?= $venta['usuario'] ?></div>
      <div class="line"></div>
    </div>

    <!-- Detalles de la venta -->
    <?php foreach ($detalles as $item): ?>
      <div class="item">
        <span><?= strtoupper(substr($item['medicamento'], 0, 12)) ?></span>
        <span><?= $item['cantidad'] ?> x <?= number_format($item['precio_unitario'], 2) ?></span>
      </div>
    <?php endforeach; ?>

    <div class="line"></div>
    <div class="item total">
      <span>TOTAL:</span>
      <span>S/ <?= number_format($venta['total'], 2) ?></span>
    </div>
    <div class="line"></div>
    <div class="center">¡Gracias por su compra!</div>
    <div class="center">www.boticabienestarysalud.site</div>

    <!-- Botón de impresión -->
    <button onclick="window.print()" class="btn-print">🖨️ Imprimir</button>
  </div>
</body>
</html>
