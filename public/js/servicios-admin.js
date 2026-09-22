document.addEventListener('DOMContentLoaded', function () {

    const buscador =
        document.getElementById('buscarServicio');

    const filtroCategoria =
        document.getElementById('filtroCategoria');

    const filtroEstado =
        document.getElementById('filtroEstado');

    const tarjetas =
        document.querySelectorAll('.service-admin-card');


    function filtrarServicios() {

        const texto =
            (buscador?.value || '')
                .trim()
                .toLowerCase();

        const categoria =
            filtroCategoria?.value || 'todos';

        const estado =
            filtroEstado?.value || 'todos';


        tarjetas.forEach(function (tarjeta) {

            const nombre =
                tarjeta.dataset.name || '';

            const categoriaTarjeta =
                tarjeta.dataset.category || '';

            const estadoTarjeta =
                tarjeta.dataset.status || '';


            const coincideTexto =
                nombre.includes(texto);

            const coincideCategoria =
                categoria === 'todos' ||
                categoriaTarjeta === categoria;

            const coincideEstado =
                estado === 'todos' ||
                estadoTarjeta === estado;


            tarjeta.style.display =
                coincideTexto &&
                coincideCategoria &&
                coincideEstado
                    ? ''
                    : 'none';

        });

    }


    buscador?.addEventListener(
        'input',
        filtrarServicios
    );

    filtroCategoria?.addEventListener(
        'change',
        filtrarServicios
    );

    filtroEstado?.addEventListener(
        'change',
        filtrarServicios
    );

});
