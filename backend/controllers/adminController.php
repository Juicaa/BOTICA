<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

require '../config/conexion.php';

// Consultas usando PDO para obtener los datos
$totalMedicamentos = $conn->query("SELECT COUNT(*) FROM Medicamentos")->fetchColumn();
$stockTotal = $conn->query("SELECT COALESCE(SUM(cantidad), 0) FROM Lotes")->fetchColumn();
$hoy = date('Y-m-d');
$limite = date('Y-m-d', strtotime('+15 days'));

$lotesPorVencerStmt = $conn->prepare("SELECT COUNT(*) FROM Lotes WHERE fecha_vencimiento BETWEEN ? AND ?");
$lotesPorVencerStmt->execute([$hoy, $limite]);
$lotesPorVencer = $lotesPorVencerStmt->fetchColumn();

$ventasHoy = $conn->query("SELECT COALESCE(SUM(total), 0) FROM Ventas WHERE DATE(fecha) = CURDATE()")->fetchColumn();

// Preparar los datos para la vista
$data = [
    'totalMedicamentos' => $totalMedicamentos,
    'stockTotal' => $stockTotal,
    'lotesPorVencer' => $lotesPorVencer,
    'ventasHoy' => $ventasHoy,
    'username' => $_SESSION['usuario']
];

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
