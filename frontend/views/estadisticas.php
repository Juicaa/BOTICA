<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../frontend/views/index.html");
    exit();
}
require '../../backend/config/conexion.php';

$inicioSemana = $_GET['inicio_semana'] ?? date('Y-m-d');
$finSemana = $_GET['fin_semana'] ?? date('Y-m-d', strtotime($inicioSemana . ' +7 days'));
$stmtSemana = $conn->prepare("SELECT DATE(fecha) as dia, SUM(total) as total FROM ventas WHERE fecha BETWEEN ? AND ? GROUP BY dia");
$stmtSemana->execute([$inicioSemana, $finSemana]);
$ventas_semanales = $stmtSemana->fetchAll(PDO::FETCH_ASSOC);

$mesSeleccionado = $_GET['mes'] ?? date('n');
$anioMesSeleccionado = $_GET['anio_mes'] ?? date('Y');
$stmtMensual = $conn->prepare("SELECT MONTH(fecha) as mes, SUM(total) as total FROM ventas WHERE YEAR(fecha) = ? GROUP BY mes");
$stmtMensual->execute([$anioMesSeleccionado]);
$ventas_mensuales = $stmtMensual->fetchAll(PDO::FETCH_ASSOC);

$anualYear = $_GET['anio_anual'] ?? date('Y');
$stmtAnual = $conn->prepare("SELECT MONTH(fecha) as mes, SUM(total) as total FROM ventas WHERE YEAR(fecha) = ? GROUP BY mes");
$stmtAnual->execute([$anualYear]);
$ventas_anuales = $stmtAnual->fetchAll(PDO::FETCH_ASSOC);

$vendedorSeleccionado = $_GET['vendedor'] ?? '';
if ($vendedorSeleccionado) {
    $stmtVendedor = $conn->prepare("SELECT u.usuario, SUM(v.total) as total FROM ventas v JOIN usuarios u ON v.id_usuario = u.id_usuario WHERE u.usuario = ? GROUP BY u.usuario");
    $stmtVendedor->execute([$vendedorSeleccionado]);
} else {
    $stmtVendedor = $conn->query("SELECT u.usuario, SUM(v.total) as total FROM ventas v JOIN usuarios u ON v.id_usuario = u.id_usuario GROUP BY u.usuario");
}
$vendedores = $stmtVendedor->fetchAll(PDO::FETCH_ASSOC);
$totalVendedor = array_sum(array_column($vendedores, 'total'));
$totalGeneral = $conn->query("SELECT SUM(total) FROM ventas")->fetchColumn() ?: 1;
$listausuarios = $conn->query("SELECT DISTINCT usuario FROM usuarios WHERE rol = 'vendedor'")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Estadísticas de ventas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Estadísticas de ventas</h2>
  <a href="dashboard_admin.php" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left-circle"></i> Volver al Dashboard</a>
  <div class="row g-4">

    <div class="col-md-6">
      <div class="card p-3">
        <form method="get">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5>ventas Semanales</h5>
            <div class="d-flex gap-2">
              <input type="date" name="inicio_semana" id="fechaInicio" value="<?= htmlspecialchars($inicioSemana) ?>" class="form-control form-control-sm" required>
              <input type="date" name="fin_semana" id="fechaFin" value="<?= htmlspecialchars($finSemana) ?>" class="form-control form-control-sm" readonly>
              <button type="submit" class="btn btn-sm btn-primary">Ver</button>
            </div>
          </div>
        </form>
        <canvas id="graficoSemanal"></canvas>
      </div>
    </div>

      <div class="col-md-6">
      <div class="card p-3">
        <form method="get">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5>ventas Mensuales</h5>
            <div class="d-flex gap-2">
              <select name="mes" class="form-select form-select-sm">
                <?php foreach (range(1, 12) as $m): ?>
                  <option value="<?= $m ?>" <?= $mesSeleccionado == $m ? 'selected' : '' ?>>
                    <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <select name="anio_mes" class="form-select form-select-sm">
                <?php foreach (range(date('Y') - 5, date('Y') + 1) as $y): ?>
                  <option value="<?= $y ?>" <?= $anioMesSeleccionado == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-sm btn-primary">Ver</button>
            </div>
          </div>
        </form>
        <canvas id="graficoMensual"></canvas>
      </div>
    </div>

     <div class="col-md-6">
      <div class="card p-3">
        <form method="get">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5>ventas Anuales</h5>
            <div class="d-flex gap-2">
              <select name="anio_anual" class="form-select form-select-sm">
                <?php foreach (range(date('Y') - 5, date('Y') + 1) as $a): ?>
                  <option value="<?= $a ?>" <?= $anualYear == $a ? 'selected' : '' ?>><?= $a ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-sm btn-primary">Ver</button>
            </div>
          </div>
        </form>
        <canvas id="graficoAnual"></canvas>
      </div>
    </div>

    
    <div class="col-md-6">
      <div class="card p-3">
        <form method="get">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5>ventas por Vendedor</h5>
            <div class="d-flex gap-2">
              <select name="vendedor" class="form-select form-select-sm">
                <option value="">Todos</option>
                <?php foreach ($listausuarios as $usuario): ?>
                  <option value="<?= $usuario ?>" <?= $vendedorSeleccionado == $usuario ? 'selected' : '' ?>><?= $usuario ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-sm btn-primary">Ver</button>
            </div>
          </div>
        </form>
        <div style="height: 300px; position: relative;">
          <canvas id="graficoVendedor" style="max-height: 300px;"></canvas>
          <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; font-size: 1.2rem;">
            S/ <?= number_format($totalVendedor, 2) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const semanal = <?= json_encode($ventas_semanales) ?>;
const mensual = <?= json_encode($ventas_mensuales) ?>;
const anual = <?= json_encode($ventas_anuales) ?>;
const vendedores = <?= json_encode($vendedores) ?>;
const mesFiltrado = <?= json_encode((int)($_GET['mes'] ?? 0)) ?>;

const labelsMensuales = [
  'Enero','Febrero','Marzo','Abril','Mayo','Junio',
  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
];

new Chart(document.getElementById('graficoSemanal'), {
  type: 'bar',
  data: {
    labels: semanal.map(e => e.dia),
    datasets: [{
      label: 'S/ ventas',
      data: semanal.map(e => e.total),
      backgroundColor: 'rgba(54, 162, 235, 0.6)'
    }]
  }
});

const dataMensual = Array.from({length: 12}, (_, i) => {
  const m = mensual.find(e => parseInt(e.mes) === i + 1);
  return m ? m.total : 0;
});
new Chart(document.getElementById('graficoMensual'), {
  type: 'line',
  data: {
    labels: labelsMensuales,
    datasets: [{
      label: 'S/ ventas',
      data: dataMensual,
      borderColor: 'green',
      backgroundColor: 'rgba(0,128,0,0.2)',
      pointBackgroundColor: dataMensual.map((_, i) => (i + 1) === mesFiltrado ? 'red' : 'green'),
      pointRadius: dataMensual.map((_, i) => (i + 1) === mesFiltrado ? 6 : 3),
      fill: false
    }]
  },
  options: {
    plugins: {
      tooltip: {
        callbacks: {
          label: ctx => 'S/ ' + ctx.parsed.y.toFixed(2)
        }
      }
    }
  }
});

new Chart(document.getElementById('graficoAnual'), {
  type: 'bar',
  data: {
    labels: labelsMensuales,
    datasets: [{
      label: 'S/ ventas',
      data: Array.from({length: 12}, (_, i) => {
        const found = anual.find(e => parseInt(e.mes) === i + 1);
        return found ? found.total : 0;
      }),
      backgroundColor: 'rgba(255, 206, 86, 0.7)'
    }]
  }
});

if (vendedores.length > 0) {
  const colores = vendedores.map(() => `rgba(${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)},${Math.floor(Math.random()*255)},0.7)`);
  new Chart(document.getElementById('graficoVendedor'), {
    type: 'doughnut',
    data: {
      labels: vendedores.map(v => v.usuario),
      datasets: [{
        label: 'S/ Vendido',
        data: vendedores.map(v => v.total),
        backgroundColor: colores
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });
}
</script>
</body>
</html>
