<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../frontend/views/index.html");
    exit();
}

require '../../backend/config/conexion.php';

// Crear categoría
if (isset($_POST['crear'])) {
    $nombre = trim($_POST['nombre']);
    $stmt = $conn->prepare("INSERT INTO categorias (nombre) VALUES (:nombre)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->execute();
    header("Location: categorias.php");
    exit();
}

// Editar categoría
if (isset($_POST['editar'])) {
    $id = $_POST['id_editar'];
    $nombre = trim($_POST['nombre_editado']);
    $stmt = $conn->prepare("UPDATE categorias SET nombre = :nombre WHERE id_categoria = :id");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: categorias.php");
    exit();
}

// Eliminar categoría
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $conn->prepare("DELETE FROM categorias WHERE id_categoria = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: categorias.php");
    exit();
}

// Obtener todas las categorías
$stmt = $conn->prepare("SELECT * FROM categorias ORDER BY id_categoria DESC");
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Categorías</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Gestión de Categorías</h2>
  <a href="dashboard_admin.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left-circle"></i> Volver al Dashboard
  </a>

  <!-- Formulario para crear -->
  <div class="card mb-4">
    <div class="card-header bg-success text-white">Registrar nueva categoría</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-10">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre de categoría" required>
          </div>
          <div class="col-md-2 d-grid">
            <button class="btn btn-success" name="crear">Registrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabla de categorías -->
  <div class="table-responsive">
    <table id="tabla-categorias" class="table table-bordered table-hover">
      <thead class="table-success">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categorias as $cat): ?>
          <tr>
            <td><?= $cat['id_categoria'] ?></td>
            <td><?= htmlspecialchars($cat['nombre']) ?></td>
            <td>
              <button class="btn btn-warning btn-sm" onclick="mostrarFormulario(<?= $cat['id_categoria'] ?>, '<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>')">Editar</button>
              <a href="?eliminar=<?= $cat['id_categoria'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Formulario para editar -->
  <div class="card mt-4 d-none" id="formEditar">
    <div class="card-header bg-warning">Editar categoría</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="id_editar" id="idEditar">
        <div class="row g-3">
          <div class="col-md-10">
            <input type="text" name="nombre_editado" id="nombreEditar" class="form-control" required>
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

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function () {
    $('#tabla-categorias').DataTable({
      paging: true,
      searching: true,
      info: false,
      ordering: false,
      pageLength: 10,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
      }
    });
  });

  function mostrarFormulario(id, nombre) {
    document.getElementById("idEditar").value = id;
    document.getElementById("nombreEditar").value = nombre;
    document.getElementById("formEditar").classList.remove("d-none");
    document.getElementById("formEditar").scrollIntoView({ behavior: "smooth" });
  }

  function cancelarEdicion() {
    document.getElementById("formEditar").classList.add("d-none");
    document.getElementById("idEditar").value = "";
    document.getElementById("nombreEditar").value = "";
  }
</script>
</body>
</html>
