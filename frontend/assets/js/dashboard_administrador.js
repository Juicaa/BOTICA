fetch('../backend/controllers/adminController.php')
  .then(response => response.json())
  .then(data => {
    document.getElementById('totalMedicamentos').textContent = data.totalMedicamentos;
    document.getElementById('stockTotal').textContent = data.stockTotal;
    document.getElementById('lotesPorVencer').textContent = data.lotesPorVencer;
    document.getElementById('ventasHoy').textContent = 'S/ ' + data.ventasHoy.toFixed(2);
    document.getElementById('username').textContent = data.username || 'Administrador';
  })
  .catch((error) => console.error("Error:", error));


