<?php
session_start();
require '../../backend/config/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    die("Acceso denegado.");
}

$id_usuario = (int)$_SESSION['id_usuario'];
$dni_cliente = trim($_POST['dni_cliente'] ?? '');
$nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
$lotes = $_POST['lotes'] ?? [];
$cantidades = $_POST['cantidades'] ?? [];

file_put_contents('log_errores.txt', json_encode($_POST));

if (strlen($dni_cliente) !== 8 || !ctype_digit($dni_cliente) || empty($nombre_cliente)) {
    die("Error: DNI o nombre del cliente inválido.");
}

if (count($lotes) !== count($cantidades) || count($lotes) === 0) {
    die("Error: datos incompletos o vacíos.");
}

$stmt = $conn->prepare("SELECT id_cliente FROM clientes WHERE dni = ?");
$stmt->execute([$dni_cliente]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cliente) {
    $id_cliente = $cliente['id_cliente'];
} else {
    $stmt = $conn->prepare("INSERT INTO clientes (dni, nombre_completo) VALUES (?, ?)");
    $stmt->execute([$dni_cliente, $nombre_cliente]);
    $id_cliente = $conn->lastInsertId();
}

$total = 0;
$detalles = [];

foreach ($lotes as $i => $id_lote) {
    $cantidad = (int)$cantidades[$i];

    $stmt = $conn->prepare("SELECT cantidad, precio_unitario FROM lotes WHERE id_lote = ?");
    $stmt->execute([$id_lote]);
    $lote = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lote) {
        die("Error: Lote ID $id_lote no encontrado.");
    }

    if ($cantidad > $lote['cantidad']) {
        die("Error: Stock insuficiente para el lote ID $id_lote.");
    }

    $subtotal = $cantidad * $lote['precio_unitario'];
    $total += $subtotal;

    $detalles[] = [
        'id_lote' => $id_lote,
        'cantidad' => $cantidad
    ];
}

$stmt = $conn->prepare("INSERT INTO ventas (total, id_usuario, id_cliente) VALUES (?, ?, ?)");
$stmt->execute([$total, $id_usuario, $id_cliente]);
$id_venta = $conn->lastInsertId();

foreach ($detalles as $detalle) {
    $stmt = $conn->prepare("INSERT INTO salidalotes (id_lote, id_venta, cantidad) VALUES (?, ?, ?)");
    $stmt->execute([$detalle['id_lote'], $id_venta, $detalle['cantidad']]);

    $stmt = $conn->prepare("UPDATE lotes SET cantidad = cantidad - ? WHERE id_lote = ?");
    $stmt->execute([$detalle['cantidad'], $detalle['id_lote']]);
}

header("Location: ver_boleta.php?id_venta=$id_venta");
exit();