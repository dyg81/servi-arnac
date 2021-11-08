/*
*   Tema Oscuro/Claro
*/
const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : null;

if (currentTheme) {
    if (currentTheme === 'light') {
        let body = document.body;
        let navbar = document.querySelector('.navbar-dark');

        body.classList.remove("dark-mode");
        navbar.classList.remove("navbar-dark");
        navbar.classList.add('navbar-white');
        navbar.classList.add('navbar-light');

        toggleSwitch.checked = true;
    }
}

toggleSwitch.addEventListener('change', switchTheme, false);

function switchTheme(e) {
    if (e.target.checked) {
        let body = document.body;
        let navbar = document.querySelector('.navbar-dark');

        body.classList.remove("dark-mode");
        navbar.classList.remove("navbar-dark");
        navbar.classList.add('navbar-white');
        navbar.classList.add('navbar-light');

        localStorage.setItem('theme', 'light');
    } else {
        let body = document.body;
        let navbar = document.querySelector('.navbar-white');

        body.classList.add("dark-mode");
        navbar.classList.remove('navbar-white');
        navbar.classList.remove('navbar-light');
        navbar.classList.add("navbar-dark");

        localStorage.setItem('theme', 'dark');
    }
}

/*
* Inicialización de la librería Sweetalert2
*/
const Toast = Swal.mixin({
    toast: true,
    position: 'top',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});

/*
* Inicialización de los tooltips de BS4
*/
$('[data-toggle="tooltip"]').tooltip(
    {
        delay: {show: 300, hide: 0},
    }
);

/*
* Inicialización del autofocus en las ventanas modales
*/
$(document).on('shown.bs.modal', function() {
    $(this).find('[autofocus]').focus();
});