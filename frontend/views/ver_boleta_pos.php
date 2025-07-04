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
<html>
<head>
  <meta charset="UTF-8">
  <title>Boleta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">    
    <link rel="stylesheet" href="../assets/css/pos.css">
           
    

</head>
<body>
  <div class="center bold">Botica Bienestar y Salud</div>
  <center><div class="text-center mb-3"><img width="100px" height="100px" src="../assets/img/logo_botica.png" alt="Botica Bienestar y Salud" class="login-logo" /></div></center>
    <div class="center">Fecha: <?= $venta['fecha'] ?></div>
  <div class="line"></div>
  <div><span class="bold">Cliente:</span> <?= $venta['nombre_completo'] ?></div>
  <div><span class="bold">DNI:</span> <?= $venta['dni'] ?></div>
  <div><span class="bold">Vendedor:</span> <?= $venta['usuario'] ?></div>
  <div class="line"></div>

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

  <button onclick="window.print()" style="margin-top:10px; width:100%;">🖨️ Imprimir</button>
</body>
</html> 