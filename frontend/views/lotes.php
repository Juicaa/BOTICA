<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../frontend/views/index.html");
    exit();
}

require '../../backend/config/conexion.php';

// Crear lote
if (isset($_POST['crear'])) {
    $stmt = $conn->prepare("INSERT INTO Lotes (id_medicamento, cantidad, fecha_ingreso, fecha_vencimiento, precio_unitario) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['id_medicamento'], $_POST['cantidad'], $_POST['fecha_ingreso'], $_POST['fecha_vencimiento'], $_POST['precio_unitario']
    ]);
    header("Location: lotes.php");
    exit();
}

// Editar lote
if (isset($_POST['editar'])) {
    $stmt = $conn->prepare("UPDATE Lotes SET cantidad=?, fecha_ingreso=?, fecha_vencimiento=?, precio_unitario=? WHERE id_lote=?");
    $stmt->execute([
        $_POST['cantidad_editada'], $_POST['fecha_ingreso_editada'], $_POST['fecha_vencimiento_editada'], $_POST['precio_editado'], $_POST['id_editar']
    ]);
    header("Location: lotes.php");
    exit();
}

// Eliminar lote
if (isset($_GET['eliminar'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM salidalotes WHERE id_lote = ?");
    $stmt->execute([$_GET['eliminar']]);
    if ($stmt->fetchColumn() > 0) {
        echo "<script>alert('❌ No se puede eliminar este lote porque tiene salidas registradas.'); window.location.href='lotes.php';</script>";
        exit();
    }
    $stmt = $conn->prepare("DELETE FROM Lotes WHERE id_lote = ?");
    $stmt->execute([$_GET['eliminar']]);
    header("Location: lotes.php");
    exit();
}

$medicamentos = $conn->query("SELECT * FROM Medicamentos")->fetchAll(PDO::FETCH_ASSOC);
$lotes = $conn->query("SELECT l.*, m.nombre AS medicamento FROM Lotes l JOIN Medicamentos m ON l.id_medicamento = m.id_medicamento ORDER BY l.id_lote DESC")->fetchAll(PDO::FETCH_ASSOC);
$hoy = new DateTime();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Lotes</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
<div class="container p-4">
  <h2>Gestión de Lotes</h2>
  <a href="dashboard_admin.php" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left-circle"></i> Volver al Dashboard</a>

  <div class="card mb-4">
    <div class="card-header bg-success text-white">Registrar nuevo lote</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-3">
            <select id="selectMedicamento" name="id_medicamento" class="form-select" required>
              <option value="" disabled selected>Buscar medicamento</option>
              <?php foreach ($medicamentos as $m): ?>
                <option value="<?= $m['id_medicamento'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <input type="number" name="cantidad" class="form-control" placeholder="Cantidad" required>
          </div>
          <div class="col-md-2">
            <input type="date" name="fecha_ingreso" class="form-control" required>
          </div>
          <div class="col-md-2">
            <input type="date" name="fecha_vencimiento" class="form-control" required>
          </div>
          <div class="col-md-2">
            <input type="number" step="0.01" name="precio_unitario" class="form-control" placeholder="Precio" required>
          </div>
          <div class="col-md-1 d-grid">
            <button class="btn btn-success" name="crear">Registrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="table-responsive">
    <table id="tabla-lotes" class="table table-bordered table-hover">
      <thead class="table-success">
        <tr>
          <th>ID</th>
          <th>Medicamento</th>
          <th>Cantidad</th>
          <th>Ingreso</th>
          <th>Vencimiento</th>
          <th>Precio (S/)</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lotes as $l): 
          $fechaVenc = new DateTime($l['fecha_vencimiento']);
          $dias = (int)$hoy->diff($fechaVenc)->format('%r%a');
          $clase = $dias <= 15 ? 'table-warning' : '';
        ?>
        <tr class="<?= $clase ?>">
          <td><?= $l['id_lote'] ?></td>
          <td><?= htmlspecialchars($l['medicamento']) ?></td>
          <td><?= $l['cantidad'] ?></td>
          <td><?= $l['fecha_ingreso'] ?></td>
          <td><?= $l['fecha_vencimiento'] ?>
            <?php if ($dias <= 15): ?><span class="text-danger fw-bold"> &#9888; Próximo a vencer</span><?php endif; ?>
          </td>
          <td><?= number_format($l['precio_unitario'], 2) ?></td>
          <td>
            <button class="btn btn-warning btn-sm" onclick="mostrarFormulario(<?= $l['id_lote'] ?>, <?= $l['cantidad'] ?>, '<?= $l['fecha_ingreso'] ?>', '<?= $l['fecha_vencimiento'] ?>', <?= $l['precio_unitario'] ?>)">Editar</button>
            <a href="?eliminar=<?= $l['id_lote'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este lote?')">Eliminar</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card mt-4 d-none" id="formEditar">
    <div class="card-header bg-warning">Editar lote</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="id_editar" id="idEditar">
        <div class="row g-3">
          <div class="col-md-2">
            <input type="number" name="cantidad_editada" id="cantidadEditar" class="form-control" required>
          </div>
          <div class="col-md-3">
            <input type="date" name="fecha_ingreso_editada" id="ingresoEditar" class="form-control" required>
          </div>
          <div class="col-md-3">
            <input type="date" name="fecha_vencimiento_editada" id="vencimientoEditar" class="form-control" required>
          </div>
          <div class="col-md-3">
            <input type="number" step="0.01" name="precio_editado" id="precioEditar" class="form-control" required>
          </div>
          <div class="col-md-1 d-grid">
            <button class="btn btn-warning" name="editar">Guardar</button>
            <button type="button" class="btn btn-secondary mt-2" onclick="cancelarEdicion()">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
  $('#tabla-lotes').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
    },
    paging: true,
    searching: true,
    ordering: false,
    info: false
  });

  $('#selectMedicamento').select2({
    placeholder: 'Buscar medicamento',
    width: '100%'
  });
});

function mostrarFormulario(id, cantidad, ingreso, vencimiento, precio) {
  document.getElementById("idEditar").value = id;
  document.getElementById("cantidadEditar").value = cantidad;
  document.getElementById("ingresoEditar").value = ingreso;
  document.getElementById("vencimientoEditar").value = vencimiento;
  document.getElementById("precioEditar").value = precio;
  document.getElementById("formEditar").classList.remove("d-none");
  document.getElementById("formEditar").scrollIntoView({ behavior: "smooth" });
}

function cancelarEdicion() {
  document.getElementById("formEditar").classList.add("d-none");
  document.getElementById("idEditar").value = "";
}
</script>
</body>
</html>
