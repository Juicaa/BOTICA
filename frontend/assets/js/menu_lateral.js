function toggleMenu() {
    var menu = document.getElementById('menuLateral');
    var contenido = document.getElementById('contenidoPrincipal');
    menu.classList.toggle('active');  // Alternar la visibilidad del menú lateral
    contenido.classList.toggle('active');  // Desplazar el contenido cuando el menú se activa
}
