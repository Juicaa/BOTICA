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
  <link rel="stylesheet" href="../assets/css/boleta_pos.css"> 
  <link rel="stylesheet" href="../assets/css/dashboard.css">   
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
  <!-- Contenedor de la boleta con clase ticket -->
  <div class="ticket">
    <header class="header">
      <div class="logo">
        <img width="100px" height="100px" src="../assets/img/logo_botica.png" alt="Botica Bienestar y Salud" class="login-logo" />
      </div>
      <h1>Botica Bienestar y Salud</h1>
      <p>Fecha: <?= $venta['fecha'] ?></p>
      <p><span class="bold">Cliente:</span> <?= $venta['nombre_completo'] ?></p>
      <p><span class="bold">DNI:</span> <?= $venta['dni'] ?></p>
      <p><span class="bold">Vendedor:</span> <?= $venta['usuario'] ?></p>
    </header>

    <section class="details">
      <table>
        <tr>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Precio</th>
          <th>Total</th>
        </tr>

        <?php
        // Mostrar los productos de la venta
        $total_venta = 0;
        foreach ($detalles as $item) {
            $total_producto = $item['cantidad'] * $item['precio_unitario'];
            $total_venta += $total_producto;
            echo "<tr>
                    <td>" . strtoupper(substr($item['medicamento'], 0, 12)) . "</td>
                    <td>" . $item['cantidad'] . "</td>
                    <td>S/. " . number_format($item['precio_unitario'], 2) . "</td>
                    <td>S/. " . number_format($total_producto, 2) . "</td>
                  </tr>";
        }
        ?>
      </table>
    </section>

    <footer class="footer">
      <p><strong>TOTAL:</strong> S/ <?= number_format($total_venta, 2) ?></p>
      <button onclick="window.print()">🖨️ Imprimir</button>
    </footer>
  </div>
</body>
</html>
