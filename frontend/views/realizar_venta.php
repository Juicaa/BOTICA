<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    die("Error: id_usuario no está definido en sesión.");
}
$id_usuario = (int)$_SESSION['id_usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Venta</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
<div class="container p-4">
  <h2>Registrar Venta</h2>
  <a href="dashboard_vendedor.php" class="btn btn-outline-dark mb-3">
      <i class="bi bi-arrow-left-circle"></i> Volver al Dashboard
  </a>


  <form method="POST" action="procesar_venta_multiple.php" id="ventaForm">
      <div class="row mb-3">
  <div class="col-md-4">
    <label for="dni_cliente">DNI del Cliente:</label>
    <input type="text" id="dni_cliente" name="dni_cliente" maxlength="8" class="form-control" required>
  </div>
  <div class="col-md-8">
    <label for="nombre_cliente">Nombre del Cliente:</label>
    <input type="text" class="form-control" id="nombre_cliente_visible" readonly>
    <input type="hidden" name="nombre_cliente" id="nombre_cliente">
  </div>
</div>
    <div id="lotes-container">
      <div class="row g-3 align-items-end lote-item">
        <div class="col-md-6">
          <label>Buscar medicamento</label>
          <select name="lotes[]" class="form-select lote-ajax" required></select>
        </div>
        <div class="col-md-3">
          <label>Cantidad</label>
          <input type="number" name="cantidades[]" class="form-control" min="1" oninput="actualizarTotal(this)" required>
        </div>
        <div class="col-md-2">
          <label>Total (S/)</label>
          <input type="text" class="form-control item-total" disabled>
        </div>
        <div class="col-md-1 d-grid">
          <button type="button" class="btn btn-danger remove-lote">X</button>
        </div>
      </div>
    </div>
    

    <div class="mt-3">
      <button type="button" class="btn btn-primary mb-3" id="add-lote">Agregar otro medicamento</button>
      <div class="mb-3"><strong>Total General: S/</strong><span id="totalGeneral">0.00</span></div>
      <button type="submit" class="btn btn-success">Registrar Venta</button>
    </div>
  </form>
</div>

<!-- MODAL DE ÉXITO -->
<div class="modal fade" id="exitoModal" tabindex="-1" aria-labelledby="exitoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-success shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="exitoModalLabel">✅ ¡Venta realizada!</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        La venta ha sido registrada correctamente.
      </div>
      <div class="modal-footer">
        <a href="realizar_venta.php" class="btn btn-success">Aceptar</a>
      </div>
    </div>
  </div>
</div>

<!-- MODALES --> 
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="stockModalLabel">❌ Stock insuficiente</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Has ingresado una cantidad mayor al stock disponible para este lote.</div>
      <div class="modal-footer"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Entendido</button></div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function actualizarTotal(elemento) {
  const row = elemento.closest('.lote-item');
  const select = row.querySelector('select');
  const inputCantidad = row.querySelector('input[name="cantidades[]"]');
  const totalField = row.querySelector('.item-total');
  const precio = parseFloat(select.dataset.precio || 0);
  const cantidad = parseInt(inputCantidad.value || 0);
  const stock = parseInt(select.dataset.stock || 0);
  if (cantidad > stock) {
    inputCantidad.value = '';
    totalField.value = '';
    mostrarModalError();
    return;
  }
  totalField.value = (cantidad * precio).toFixed(2);
  calcularTotalGeneral();
}

function calcularTotalGeneral() {
  let total = 0;
  document.querySelectorAll('.item-total').forEach(el => {
    const val = parseFloat(el.value);
    if (!isNaN(val)) total += val;
  });
  document.getElementById('totalGeneral').innerText = total.toFixed(2);
}

function inicializarSelect2Ajax() {
  $('.lote-ajax').select2({
    placeholder: "Buscar medicamento...",
    allowClear: true,
    width: '100%',
    ajax: {
      url: '../../backend/controllers/lotesAjax.php',
      dataType: 'json',
      delay: 250,
      data: params => ({ term: params.term }),
      processResults: data => ({
        results: data.results.map(item => ({
          id: item.id,
          text: item.text,
          data_precio: item.data_precio,
          data_stock: item.data_stock
        }))
      }),
      cache: true
    }
  }).on('select2:select', function (e) {
    const data = e.params.data;
    const row = this.closest('.lote-item');
    this.dataset.precio = data.data_precio;
    this.dataset.stock = data.data_stock;
    actualizarTotal(this);
  });
}

function asignarEventosEliminar() {
  document.querySelectorAll('.remove-lote').forEach(btn => {
    btn.onclick = function () {
      const fila = btn.closest('.lote-item');
      const items = document.querySelectorAll('.lote-item');
      if (items.length > 1) {
        fila.remove();
        calcularTotalGeneral();
      }
    };
  });
}

document.getElementById('add-lote').addEventListener('click', () => {
  const container = document.getElementById('lotes-container');
  const filaNueva = document.createElement('div');
  filaNueva.className = 'row g-3 align-items-end lote-item';
  filaNueva.innerHTML = `
    <div class="col-md-6">
      <label>Buscar medicamento</label>
      <select name="lotes[]" class="form-select lote-ajax" required></select>
    </div>
    <div class="col-md-3">
      <label>Cantidad</label>
      <input type="number" name="cantidades[]" class="form-control" min="1" oninput="actualizarTotal(this)" required>
    </div>
    <div class="col-md-2">
      <label>Total (S/)</label>
      <input type="text" class="form-control item-total" disabled>
    </div>
    <div class="col-md-1 d-grid">
      <button type="button" class="btn btn-danger remove-lote">X</button>
    </div>
  `;

  container.appendChild(filaNueva);
  inicializarSelect2Ajax();
  asignarEventosEliminar();
});


function mostrarModalError() {
  const modal = new bootstrap.Modal(document.getElementById('stockModal'));
  modal.show();
}
<?php if (isset($_GET['exito']) && $_GET['exito'] == 1): ?>
window.addEventListener('load', () => {
  const modal = new bootstrap.Modal(document.getElementById('exitoModal'));
  modal.show();
});
<?php endif; ?>


document.addEventListener('DOMContentLoaded', () => {
  inicializarSelect2Ajax();
  asignarEventosEliminar();
});

// Consultar API DNI Perú
document.getElementById('dni_cliente').addEventListener('keyup', function () {
  const dni = this.value;
  if (dni.length === 8) {
    fetch(`https://apiperu.dev/api/dni/${dni}?api_token=ae59073b3727aec3124df7de91a7d1ae53510151461d95433683891827d5eb6c`)
      .then(res => res.json())
      .then(data => {
    if (data.success && data.data) {
        const nombres = data.data.nombres || '';
        const apellidoPaterno = data.data.apellido_paterno || '';
        const apellidoMaterno = data.data.apellido_materno || '';
        const nombreCompleto = `${nombres} ${apellidoPaterno} ${apellidoMaterno}`.trim();
        document.getElementById('nombre_cliente_visible').value = nombreCompleto;
        document.getElementById('nombre_cliente').value = nombreCompleto;

    } else {
        alert("No se encontró el cliente.");
    }
})

      .catch(err => {
        console.error('Error al consultar DNI:', err);
      });
  }
});

</script>
</body>
</html>
