function toggleMenu() {
    var menu = document.getElementById('menuLateral');
    var contenido = document.getElementById('contenidoPrincipal');
    menu.classList.toggle('active');  // Alterna la visibilidad del menú lateral
    contenido.classList.toggle('active');  // Desplaza el contenido cuando el menú se activa
}
