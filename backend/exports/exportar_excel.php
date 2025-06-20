<?php
require '../../vendor/autoload.php';
require '../config/conexion.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Obtener filtros desde GET
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$usuario = $_GET['usuario'] ?? '';
$medicamento = $_GET['medicamento'] ?? '';

$filtro = "WHERE 1=1";
$params = [];

if (!empty($desde) && !empty($hasta)) {
    $filtro .= " AND DATE(v.fecha) BETWEEN ? AND ?";
    $params[] = $desde;
    $params[] = $hasta;
}
if (!empty($usuario)) {
    $filtro .= " AND u.usuario = ?";
    $params[] = $usuario;
}
if (!empty($medicamento)) {
    $filtro .= " AND m.nombre = ?";
    $params[] = $medicamento;
}

$sql = "SELECT v.id_venta, v.fecha, u.usuario, m.nombre AS medicamento, s.cantidad, 
               l.precio_unitario, (s.cantidad * l.precio_unitario) AS total
        FROM ventas v
        JOIN Usuarios u ON v.id_usuario = u.id_usuario
        JOIN SalidaLotes s ON v.id_venta = s.id_venta
        JOIN Lotes l ON s.id_lote = l.id_lote
        JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento
        $filtro
        ORDER BY v.fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("ventas");

$sheet->fromArray(
    ['ID Venta', 'Fecha', 'Usuario', 'Medicamento', 'Cantidad', 'Precio Unitario', 'Total'],
    NULL, 'A1'
);

$fila = 2;
foreach ($datos as $row) {
    $sheet->setCellValue("A$fila", $row['id_venta']);
    $sheet->setCellValue("B$fila", $row['fecha']);
    $sheet->setCellValue("C$fila", $row['usuario']);
    $sheet->setCellValue("D$fila", $row['medicamento']);
    $sheet->setCellValue("E$fila", $row['cantidad']);
    $sheet->setCellValue("F$fila", number_format($row['precio_unitario'], 2));
    $sheet->setCellValue("G$fila", number_format($row['total'], 2));
    $fila++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_ventas.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
