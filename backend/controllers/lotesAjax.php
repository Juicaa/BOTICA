<?php
require '../config/conexion.php';

$term = $_GET['term'] ?? '';

$stmt = $conn->prepare("
    SELECT l.id_lote, l.cantidad, l.precio_unitario, m.nombre
    FROM Lotes l
    JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento
    WHERE l.cantidad > 0 AND m.nombre LIKE ?
    LIMIT 10
");
$stmt->execute(["%$term%"]);
$lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formato Select2
$resultados = array_map(function($lote) {
    return [
        'id' => $lote['id_lote'],
        'text' => $lote['nombre'] . " - Lote " . $lote['id_lote'] .
                 " ({$lote['cantidad']} unidades, S/{$lote['precio_unitario']})",
        'data_precio' => $lote['precio_unitario'],
        'data_stock' => $lote['cantidad']
    ];
}, $lotes);

echo json_encode(['results' => $resultados]);
?>
