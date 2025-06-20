<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../frontend/views/index.html");
    exit();
}

require '../../backend/config/conexion.php';

// Crear medicamento
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['id_categoria'];

    $stmt = $conn->prepare("INSERT INTO Medicamentos (nombre, id_categoria) VALUES (?, ?)");
    $stmt->execute([$nombre, $categoria]);
    header("Location: medicamentos.php");
    exit();
}

// Editar medicamento
if (isset($_POST['editar'])) {
    $id = $_POST['id_editar'];
    $nombre = $_POST['nombre_editado'];
    $categoria = $_POST['categoria_editada'];

    $stmt = $conn->prepare("UPDATE Medicamentos SET nombre = ?, id_categoria = ? WHERE id_medicamento = ?");
    $stmt->execute([$nombre, $categoria, $id]);
    header("Location: medicamentos.php");
    exit();
}

// Eliminar medicamento
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $conn->prepare("DELETE FROM Medicamentos WHERE id_medicamento = ?");
    $stmt->execute([$id]);
    header("Location: medicamentos.php");
    exit();
}

// Obtener medicamentos con sus categorías
$stmt = $conn->prepare("SELECT m.id_medicamento, m.nombre, c.nombre AS categoria, c.id_categoria 
                        FROM Medicamentos m 
                        JOIN Categorias c ON m.id_categoria = c.id_categoria 
                        ORDER BY m.id_medicamento DESC");
$stmt->execute();
$medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las categorías
$categorias = $conn->query("SELECT * FROM Categorias")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Medicamentos</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container p-4">
    <h2>Gestión de Medicamentos</h2>
    <a href="dashboard_admin.php" class="btn btn-outline-secondary mb-3">
      <i class="bi bi-arrow-left-circle"></i> Volver al Dashboard
    </a>

    <!-- Formulario crear -->
    <div class="card mb-4">
      <div class="card-header bg-success text-white">Registrar nuevo medicamento</div>
      <div class="card-body">
        <form method="POST">
          <div class="row g-3">
            <div class="col-md-6">
              <input type="text" name="nombre" class="form-control" placeholder="Nombre del medicamento" required>
            </div>
            <div class="col-md-4">
              <select name="id_categoria" id="selectCategoriaCrear" class="form-select" required>
                <option value="" disabled selected>Seleccione categoría</option>
                <?php foreach ($categorias as $cat): ?>
                  <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-grid">
              <button class="btn btn-success" name="crear">Registrar</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabla de medicamentos -->
    <div class="table-responsive">
      <table id="tabla-medicamentos" class="table table-bordered table-hover">
        <thead class="table-success">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($medicamentos as $med): ?>
            <tr>
              <td><?= $med['id_medicamento'] ?></td>
              <td><?= htmlspecialchars($med['nombre']) ?></td>
              <td><?= htmlspecialchars($med['categoria']) ?></td>
              <td>
                <button class="btn btn-warning btn-sm"
                        onclick="mostrarFormulario(<?= $med['id_medicamento'] ?>, '<?= htmlspecialchars($med['nombre'], ENT_QUOTES) ?>', <?= $med['id_categoria'] ?>)">
                  Editar
                </button>
                <a href="?eliminar=<?= $med['id_medicamento'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este medicamento?')">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Formulario editar -->
    <div class="card mt-4 d-none" id="formEditar">
      <div class="card-header bg-warning">Editar medicamento</div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="id_editar" id="idEditar">
          <div class="row g-3">
            <div class="col-md-5">
              <input type="text" name="nombre_editado" id="nombreEditar" class="form-control" required>
            </div>
            <div class="col-md-5">
              <select name="categoria_editada" id="categoriaEditar" class="form-select" required>
                <?php foreach ($categorias as $cat): ?>
                  <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-grid">
              <button class="btn btn-warning" name="editar">Guardar</button>
              <button type="button" class="btn btn-secondary mt-2" onclick="cancelarEdicion()">Cancelar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#tabla-medicamentos').DataTable({
        paging: true,
        searching: true,
        info: false,
        ordering: false,
        pageLength: 10,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
      });

      $('#selectCategoriaCrear').select2({
        placeholder: 'Seleccione categoría',
        width: '100%'
      });

      $('#categoriaEditar').select2({
        placeholder: 'Seleccione categoría',
        width: '100%'
      });
    });

    function mostrarFormulario(id, nombre, categoriaId) {
      document.getElementById("idEditar").value = id;
      document.getElementById("nombreEditar").value = nombre;
      document.getElementById("categoriaEditar").value = categoriaId;
      document.getElementById("formEditar").classList.remove("d-none");
      document.getElementById("formEditar").scrollIntoView({ behavior: "smooth" });
      $('#categoriaEditar').val(categoriaId).trigger('change');
    }

    function cancelarEdicion() {
      document.getElementById("formEditar").classList.add("d-none");
      document.getElementById("idEditar").value = "";
      document.getElementById("nombreEditar").value = "";
    }
  </script>
</body>
</html>
