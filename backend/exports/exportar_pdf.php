<?php
require '../../vendor/autoload.php';
require '../config/conexion.php';
use TCPDF;

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
        JOIN usuarios u ON v.id_usuario = u.id_usuario
        JOIN salidalotes s ON v.id_venta = s.id_venta
        JOIN lotes l ON s.id_lote = l.id_lote
        JOIN medicamentos m ON l.id_medicamento = m.id_medicamento
        $filtro
        ORDER BY v.fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->Image('../../frontend/assets/img/logo_botica.png', 10, 10, 30);
$pdf->Ln(25);
$pdf->SetFont('helvetica', '', 9);

$html = '<h2 style="text-align:center;">Reporte de Ventas</h2>
<table border="1" cellpadding="4">
<thead>
<tr style="background-color:#f2f2f2;">
<th><b>ID Venta</b></th>
<th><b>Fecha</b></th>
<th><b>Usuario</b></th>
<th><b>Medicamento</b></th>
<th><b>Cantidad</b></th>
<th><b>P. Unitario</b></th>
<th><b>Total</b></th>
</tr>
</thead>
<tbody>';

$total_general = 0;
foreach ($ventas as $row) {
    $html .= "<tr>
        <td>{$row['id_venta']}</td>
        <td>{$row['fecha']}</td>
        <td>{$row['usuario']}</td>
        <td>{$row['medicamento']}</td>
        <td>{$row['cantidad']}</td>
        <td>" . number_format($row['precio_unitario'], 2) . "</td>
        <td>" . number_format($row['total'], 2) . "</td>
    </tr>";
    $total_general += $row['total'];
}

$html .= "<tr>
    <td colspan='6' align='right'><b>Total General:</b></td>
    <td><b>S/ " . number_format($total_general, 2) . "</b></td>
</tr>";

$html .= '</tbody></table>';

ob_clean();
$pdf->writeHTML($html);
$pdf->Output('reporte_ventas.pdf', 'I');
