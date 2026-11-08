/*
    Inicialización de la libreria Animsition para los efectos de cambio
    entre páginas.
*/
var $animsition = $(".animsition");
if ($animsition.length && $.fn.animsition) {
    $animsition.animsition({
        inClass: 'fade-in',
        outClass: 'fade-out',
        inDuration: 500,
        outDuration: 500,
        linkElement: 'a:not([target="_blank"]):not([href^="#"]):not([class^="chosen-single"])',
        loading: true,
        loadingParentElement: 'html',
        loadingClass: 'page-loader',
        loadingInner: '<div class="page-loader__spin"></div>',
        timeout: false,
        timeoutCountdown: 5000,
        onLoadEvent: true,
        browser: ['animation-duration', '-webkit-animation-duration'],
        overlay: false,
        overlayClass: 'animsition-overlay-slide',
        overlayParentElement: 'html',
        transition: function (url) {
            window.location.href = url;
        }
    });
}

/*
    Inicialización de la librería Sweetalert2.
*/
(function () {
    window.Toast = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
    });
})();

/*
    Inicialización de la librería dataTables.
*/
var $mainTable = $('#sac');
if ($mainTable.length && $.fn.DataTable && !$.fn.DataTable.isDataTable($mainTable[0])) {
    $mainTable.DataTable({
        "ordering": false,
        "pageResize": true,
        "scrollX": "100%",
        "pagingType": "full_numbers",
        "language": {
            "url": "/assets/plugins/datatables/spanish.json"
        },
        dom: `<'row'<'col-sm-6 text-left'><'col-sm-6 text-right'f>>
              <'row'<'col-sm-12'tr>>
              <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
    });
}

/*
    Inicialización de los tooltips de Bootstrap4.
*/
if ($.fn.tooltip) $('[data-toggle="tooltip"]').tooltip(
    {
        delay: {show: 300, hide: 0},
        trigger: 'hover',
        boundary: 'window'
    }
);

/*
    Inicialización del autofocus en las ventanas modales.
*/
$(document).off('shown.bs.modal.customAutofocus').on('shown.bs.modal.customAutofocus', function (event) {
    $(event.target).find('[autofocus]').first().trigger('focus');
});

/*
    Función para cerrar el menú del usuario al dar clic en Cambiar Contraseña.
*/
$(document).off('click.customPasswordMenu', '#pwd-change').on('click.customPasswordMenu', '#pwd-change', function () {
    $(this).closest('.dropdown-menu').removeClass('show');
});
