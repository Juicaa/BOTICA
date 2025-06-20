<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../frontend/views/index.html");
    exit();
}

require '../../backend/config/conexion.php';

// Filtros desde GET
$filtro = "WHERE 1=1";
$params = [];

$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$usuario = $_GET['usuario'] ?? '';
$medicamento = $_GET['medicamento'] ?? '';

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

// Consulta principal
$sql = "
    SELECT v.id_venta, v.fecha, u.usuario, m.nombre AS medicamento, s.cantidad, l.precio_unitario, 
           (s.cantidad * l.precio_unitario) AS total
    FROM ventas v
    JOIN usuarios u ON v.id_usuario = u.id_usuario
    JOIN salidalotes s ON v.id_venta = s.id_venta
    JOIN lotes l ON s.id_lote = l.id_lote
    JOIN medicamentos m ON l.id_medicamento = m.id_medicamento
    $filtro
    ORDER BY v.fecha DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalGeneral = array_sum(array_column($reportes, 'total'));

// Filtros desplegables
$usuarios = $conn->query("SELECT DISTINCT usuario FROM usuarios WHERE rol = 'vendedor'")->fetchAll(PDO::FETCH_ASSOC);
$medicamentos = $conn->query("SELECT DISTINCT nombre FROM medicamentos")->fetchAll(PDO::FETCH_ASSOC);

// Para exportar
$exportParams = http_build_query([
    'desde' => $desde,
    'hasta' => $hasta,
    'usuario' => $usuario,
    'medicamento' => $medicamento
]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reportes de ventas</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Reportes de ventas</h2>
  <a href="dashboard_admin.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left-circle"></i> Volver al Dashboard
  </a>

  <!-- Filtros -->
  <form class="row g-3 mb-4" method="GET">
    <div class="col-md-3">
      <label>Desde</label>
      <input type="date" class="form-control" name="desde" value="<?= htmlspecialchars($desde) ?>">
    </div>
    <div class="col-md-3">
      <label>Hasta</label>
      <input type="date" class="form-control" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
    </div>
    <div class="col-md-3">
      <label>Usuario</label>
      <select class="form-select" name="usuario">
        <option value="">Todos</option>
        <?php foreach ($usuarios as $u): ?>
          <option value="<?= htmlspecialchars($u['usuario']) ?>" <?= $usuario === $u['usuario'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['usuario']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label>Medicamento</label>
      <select class="form-select" name="medicamento">
        <option value="">Todos</option>
        <?php foreach ($medicamentos as $m): ?>
          <option value="<?= htmlspecialchars($m['nombre']) ?>" <?= $medicamento === $m['nombre'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($m['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12 d-flex gap-3">
      <button class="btn btn-success" type="submit">Filtrar</button>
      <a href="reportes.php" class="btn btn-outline-danger">Limpiar</a>
    </div>
  </form>

  <!-- Tabla de reportes -->
  <div class="table-responsive">
    <table id="tabla-ventas" class="table table-bordered table-hover">
      <thead class="table-success">
        <tr>
          <th>ID Venta</th>
          <th>Fecha</th>
          <th>Usuario</th>
          <th>Medicamento</th>
          <th>Cantidad</th>
          <th>Precio Unitario (S/)</th>
          <th>Total (S/)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reportes as $r): ?>
        <tr>
          <td><?= $r['id_venta'] ?></td>
          <td><?= $r['fecha'] ?></td>
          <td><?= htmlspecialchars($r['usuario']) ?></td>
          <td><?= htmlspecialchars($r['medicamento']) ?></td>
          <td><?= $r['cantidad'] ?></td>
          <td><?= number_format($r['precio_unitario'], 2) ?></td>
          <td><?= number_format($r['total'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr class="table-light fw-bold">
          <td colspan="6" class="text-end">TOTAL GENERAL:</td>
          <td>S/ <?= number_format($totalGeneral, 2) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- Botones de exportación -->
  <div class="mt-3 d-flex gap-3">
    <a href="../../backend/exports/exportar_excel.php?<?= $exportParams ?>" class="btn btn-outline-success">Exportar a Excel</a>
    <a href="../../backend/exports/exportar_pdf.php?<?= $exportParams ?>" class="btn btn-outline-danger">Exportar a PDF</a>
  </div>
</div>

<!-- Scripts de DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
  const tabla = $('#tabla-ventas').DataTable({
    paging: true,
    searching: false,
    info: false,
    ordering: false,
    pageLength: 10,
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
    },
    drawCallback: function () {
      let totalRow = $('#tabla-ventas tfoot tr').detach();
      $('#tabla-ventas').parent().find('tfoot').remove();
      $('#tabla-ventas tbody').after($('<tfoot>').append(totalRow));
    }
  });
});

</script>
</body>
</html>
