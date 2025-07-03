function toggleMenu() {
    var menu = document.getElementById('menuLateral');
    var contenido = document.getElementById('contenidoPrincipal'); // Asegúrate de que el ID del contenido sea correcto
    menu.classList.toggle('active'); // Alternar la clase 'active' para mostrar u ocultar el menú
    contenido.classList.toggle('active'); // Alternar la clase 'active' para mover el contenido
}
