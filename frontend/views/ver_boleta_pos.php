<?php
// Incluir el archivo de conexión
include '../../backend/config/conexion.php'; // Ajusta la ruta según tu estructura

// Verifica si se pasa un id_venta por la URL
if (isset($_GET['id_venta'])) {
    $id_venta = $_GET['id_venta'];  // Obtener el id de la venta desde la URL
} else {
    // Si no se pasa el id_venta, redirigir a otra página o mostrar un error
    die('Venta no encontrada');
}

// Obtener los detalles de la venta
$sql_venta = "SELECT v.id_venta, v.fecha, v.total, c.nombre_completo, c.dni, u.usuario as vendedor
              FROM ventas v
              JOIN clientes c ON v.id_cliente = c.id_cliente
              JOIN usuarios u ON v.id_usuario = u.id_usuario
              WHERE v.id_venta = :id_venta";  // Usamos un parámetro para PDO

$stmt_venta = $conn->prepare($sql_venta);
$stmt_venta->bindParam(':id_venta', $id_venta, PDO::PARAM_INT);
$stmt_venta->execute();

// Verificar si la venta existe
if ($stmt_venta->rowCount() > 0) {
    $venta = $stmt_venta->fetch(PDO::FETCH_ASSOC);
} else {
    die('Venta no encontrada');
}

// Obtener los productos de la venta
$sql_productos = "SELECT m.nombre, l.cantidad, l.precio_unitario
                  FROM salidalotes s
                  JOIN lotes l ON s.id_lote = l.id_lote
                  JOIN medicamentos m ON l.id_medicamento = m.id_medicamento
                  WHERE s.id_venta = :id_venta";

$stmt_productos = $conn->prepare($sql_productos);
$stmt_productos->bindParam(':id_venta', $id_venta, PDO::PARAM_INT);
$stmt_productos->execute();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta de Compra</title>
    <link rel="stylesheet" href="../assets/css/boleta_pos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="ticket">
        <header class="header">
            <!-- Logo de la empresa -->
            <img src="../assets/img/logo_botica.png" alt="Logo Botica Bienestar y Salud" class="logo">
            <h1>Botica Bienestar y Salud</h1>
            <p>Fecha: <?php echo date("Y-m-d H:i:s", strtotime($venta['fecha'])); ?></p>
            <p>Cliente: <?php echo $venta['nombre_completo']; ?></p>
            <p>DNI: <?php echo $venta['dni']; ?></p>
            <p>Vendedor: <?php echo $venta['vendedor']; ?></p>
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
                while ($producto = $stmt_productos->fetch(PDO::FETCH_ASSOC)) {
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
            <p><strong>¡Gracias por su compra!</strong></p>
            <button onclick="window.print()">Imprimir Boleta</button>
        </footer>
    </div>

    <?php $conn = null; // Cierra la conexión ?>
</body>
</html>
