// Obtener los datos del controlador PHP con ruta relativa
fetch('../backend/controllers/adminController.php')
  .then(response => response.json())
  .then(data => {
    document.getElementById('totalMedicamentos').textContent = data.totalMedicamentos;
    document.getElementById('stockTotal').textContent = data.stockTotal;
    document.getElementById('lotesPorVencer').textContent = data.lotesPorVencer;
    document.getElementById('ventasHoy').textContent = 'S/ ' + data.ventasHoy.toFixed(2);
    document.getElementById('username').textContent = data.username || 'Administrador';
  })
  .catch(error => console.error('Error:', error));

  // dashboard_administrador.js

function toggleMenu() {
  var menu = document.getElementById('menuLateral');
  menu.classList.toggle('active'); // Alternar la clase 'active' para mostrar u ocultar el menú
}
