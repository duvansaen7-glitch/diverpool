document.addEventListener('DOMContentLoaded', () => {

    const steps = document.querySelectorAll('.how-step');

    const infoLabel = document.querySelector('.how-info-label');
    const infoTitle = document.querySelector('.how-info h2');
    const infoText = document.querySelector('.how-info p');
    const infoList = document.querySelector('.how-list');

    const visualNumber = document.querySelector('.visual-number');
    const visualSymbol = document.querySelector('.visual-symbol');

    const contents = [
        {
            number: '01',

            title: 'Registre a su mascota',

            text:
                'Agregue la información de su mascota para tener todos sus datos organizados y facilitar cada visita a Diverpool.',

            symbol: '✦',

            items: [
                'Información básica de su mascota',
                'Datos importantes para su cuidado',
                'Perfil disponible para futuras reservas'
            ]
        },

        {
            number: '02',

            title: 'Elija el servicio',

            text:
                'Explore los servicios disponibles y seleccione el cuidado que mejor se adapte a las necesidades de su mascota.',

            symbol: '＋',

            items: [
                'Peluquería y cuidado estético',
                'Servicios veterinarios',
                'Terapias, guardería y consultas'
            ]
        },

        {
            number: '03',

            title: 'Seleccione el horario',

            text:
                'Consulte los horarios disponibles y seleccione la fecha y hora que mejor se adapte a su agenda.',

            symbol: '◷',

            items: [
                'Disponibilidad actualizada',
                'Horarios según el servicio',
                'Selección sencilla de fecha y hora'
            ]
        },

        {
            number: '04',

            title: 'Confirme su reserva',

            text:
                'Revise los datos de su cita y confirme la reserva. Su solicitud quedará registrada en el sistema.',

            symbol: '✓',

            items: [
                'Resumen de la reserva',
                'Servicio y horario seleccionado',
                'Confirmación de la solicitud'
            ]
        }
    ];


    function cambiarPaso(index) {

        const content = contents[index];

        if (!content) {
            return;
        }


        /* ================================
           ACTIVAR PASO
        ================================= */

        steps.forEach((step, stepIndex) => {

            step.classList.toggle(
                'active',
                stepIndex === index
            );

        });


        /* ================================
           ACTUALIZAR INFORMACIÓN
        ================================= */

        if (infoLabel) {
            infoLabel.textContent = `PASO ${content.number}`;
        }

        if (infoTitle) {
            infoTitle.textContent = content.title;
        }

        if (infoText) {
            infoText.textContent = content.text;
        }


        /* ================================
           ACTUALIZAR PARTE VISUAL
        ================================= */

        if (visualNumber) {
            visualNumber.textContent = content.number;
        }

        if (visualSymbol) {
            visualSymbol.textContent = content.symbol;
        }


        /* ================================
           ACTUALIZAR LISTA
        ================================= */

        if (infoList) {

            infoList.innerHTML = content.items
                .map(item => `
                    <li>
                        <span>✓</span>
                        ${item}
                    </li>
                `)
                .join('');

        }


        /* ================================
           ANIMACIÓN
        ================================= */

        const info = document.querySelector('.how-info');
        const visual = document.querySelector('.how-visual');


        if (info) {
            info.style.animation = 'none';
        }

        if (visual) {
            visual.style.animation = 'none';
        }


        requestAnimationFrame(() => {

            if (info) {
                info.style.animation =
                    'howContentIn .4s ease';
            }

            if (visual) {
                visual.style.animation =
                    'howVisualIn .4s ease';
            }

        });

    }


    /* ================================
       BOTONES DE LOS PASOS
    ================================= */

    steps.forEach((step, index) => {

        step.addEventListener('click', () => {

            cambiarPaso(index);

        });

    });


    /* ================================
       PASO INICIAL
    ================================= */

    cambiarPaso(0);

});