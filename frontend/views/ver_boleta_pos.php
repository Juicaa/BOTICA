<?php
// Incluir el archivo de conexión
include '../backend/config/conexion.php'; // Ruta correcta según tu estructura

// Verifica si se pasa un id_venta por la URL
if (isset($_GET['id_venta'])) {
    $id_venta = $_GET['id_venta'];  // Obtener el id de la venta desde la URL
} else {
    // Si no se pasa el id_venta, redirigir a otra página o mostrar un error
    die('Venta no encontrada');
}

// Obtener los detalles de la venta
$sql_venta = "SELECT v.id_venta, v.fecha, v.total, c.nombre_completo
              FROM ventas v
              JOIN clientes c ON v.id_cliente = c.id_cliente
              WHERE v.id_venta = $id_venta";
$result_venta = $conn->query($sql_venta);

// Verificar si la venta existe
if ($result_venta->num_rows > 0) {
    $venta = $result_venta->fetch_assoc();
} else {
    die('Venta no encontrada');
}

// Obtener los productos de la venta
$sql_productos = "SELECT m.nombre, l.cantidad, l.precio_unitario
                  FROM salidalotes s
                  JOIN lotes l ON s.id_lote = l.id_lote
                  JOIN medicamentos m ON l.id_medicamento = m.id_medicamento
                  WHERE s.id_venta = $id_venta";
$result_productos = $conn->query($sql_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta de Compra</title>
    <link rel="stylesheet" href="../assets/css/boleta.css"> <!-- Enlaza el CSS para el estilo -->
</head>
<body>
    <div class="ticket">
        <header class="header">
            <h1>Boleta de Compra</h1>
            <p>Fecha: <?php echo date("Y-m-d H:i:s", strtotime($venta['fecha'])); ?></p>
            <p>Cliente: <?php echo $venta['nombre_completo']; ?></p>
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
                while ($producto = $result_productos->fetch_assoc()) {
                    $total_producto = $producto['cantidad'] * $producto['precio_unitario'];
                    $total_venta += $total_producto;
                    echo "<tr>
                            <td>" . $producto['nombre'] . "</td>
                            <td>" . $producto['cantidad'] . "</td>
                            <td>S/. " . number_format($producto['precio_unitario'], 2) . "</td>
                            <td>S/. " . number_format($total_producto, 2) . "</td>
                          </tr>";
                }
                ?>
            </table>
        </section>

        <footer class="footer">
            <!-- Mensaje de agradecimiento -->
            <p><strong>Gracias por su compra</strong></p>
            <p><strong>Visítanos en: </strong><a href="http://www.boticabienestarysalud.site" target="_blank">www.boticabienestarysalud.site</a></p>
            <button onclick="window.print()">Imprimir Boleta</button>
        </footer>
    </div>

    <?php $conn->close(); // Cierra la conexión ?>
</body>
</html>

