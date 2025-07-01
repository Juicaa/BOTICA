// frontend/js/menu_lateral.js

function toggleMenu() {
    var menu = document.getElementById('menuLateral');
    console.log(menu.classList);  // Esto es para verificar si la función se ejecuta
    menu.classList.toggle('active'); // Alternar la clase 'active' para mostrar u ocultar el menú
}
