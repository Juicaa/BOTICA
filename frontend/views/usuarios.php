<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../frontend/views/index.html");
    exit();
}

require '../../backend/config/conexion.php';

if (isset($_POST['crear'])) {
    $nuevoUsuario = $_POST['nuevo_usuario'];
    $nuevaContra = $_POST['nueva_contrasena'];
    $rol = 'vendedor';

    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, contraseña, rol) VALUES (?, ?, ?)");
    $stmt->execute([$nuevoUsuario, $nuevaContra, $rol]);
    header("Location: usuarios.php");
    exit();
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $verificar = $conn->prepare("SELECT COUNT(*) as total FROM ventas WHERE id_usuario = ?");
    $verificar->execute([$id]);
    $dato = $verificar->fetch(PDO::FETCH_ASSOC);

    if ($dato['total'] > 0) {
        echo "<script>alert('No se puede eliminar este usuario porque tiene ventas registradas.'); window.location.href='usuarios.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    header("Location: usuarios.php");
    exit();
}

if (isset($_POST['editar'])) {
    $idEditar = $_POST['id_editar'];
    $usuarioEditado = $_POST['usuario_editado'];
    $contraEditada = $_POST['contrasena_editada'];

    $stmt = $conn->prepare("UPDATE usuarios SET usuario = ?, contraseña = ? WHERE id_usuario = ?");
    $stmt->execute([$usuarioEditado, $contraEditada, $idEditar]);
    header("Location: usuarios.php");
    exit();
}

$stmt = $conn->prepare("SELECT id_usuario, usuario, rol FROM usuarios WHERE rol = 'vendedor'");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div class="container p-4">
  <h2>Usuarios - Vendedores</h2>
  <a href="dashboard_admin.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left-circle"></i> Volver al Dashboard
  </a>

  <div class="card my-4">
    <div class="card-header bg-success text-white">Registrar nuevo vendedor</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-5">
            <input type="text" name="nuevo_usuario" class="form-control" placeholder="Nombre de usuario" required>
          </div>
          <div class="col-md-5">
            <input type="password" name="nueva_contrasena" class="form-control" placeholder="Contraseña" required>
          </div>
          <div class="col-md-2">
            <button class="btn btn-success w-100" name="crear">Registrar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="table-responsive">
    <table id="tabla-usuarios" class="table table-bordered table-hover">
      <thead class="table-success">
        <tr>
          <th>ID</th>
          <th>Usuario</th>
          <th>Rol</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $fila): ?>
        <tr>
          <td><?= $fila['id_usuario'] ?></td>
          <td><?= htmlspecialchars($fila['usuario']) ?></td>
          <td><?= $fila['rol'] ?></td>
          <td>
            <button class="btn btn-warning btn-sm" onclick="mostrarFormulario(<?= $fila['id_usuario'] ?>, '<?= htmlspecialchars($fila['usuario'], ENT_QUOTES) ?>')">Editar</button>
            <a href="?eliminar=<?= $fila['id_usuario'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card mt-4 d-none" id="formEditar">
    <div class="card-header bg-warning">Editar usuario</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="id_editar" id="idEditar">
        <div class="row g-3">
          <div class="col-md-5">
            <input type="text" name="usuario_editado" id="usuarioEditar" class="form-control" required>
          </div>
          <div class="col-md-5">
            <input type="password" name="contrasena_editada" class="form-control" placeholder="Nueva contraseña" required>
          </div>
          <div class="col-md-2 d-grid">
            <button class="btn btn-warning" name="editar">Guardar</button>
          </div>
          <div class="col-md-2 d-grid">
            <button type="button" class="btn btn-secondary" onclick="cancelarEdicion()">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function () {
    $('#tabla-usuarios').DataTable({
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

  function mostrarFormulario(id, usuario) {
    document.getElementById("idEditar").value = id;
    document.getElementById("usuarioEditar").value = usuario;
    document.getElementById("formEditar").classList.remove("d-none");
    document.getElementById("formEditar").scrollIntoView({ behavior: "smooth" });
  }

  function cancelarEdicion() {
    document.getElementById("formEditar").classList.add("d-none");
    document.getElementById("idEditar").value = "";
    document.getElementById("usuarioEditar").value = "";
  }
</script>
</body>
</html>
