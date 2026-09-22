/* =========================================================
   MENÚ ADMINISTRACIÓN
   ========================================================= */

document.addEventListener('DOMContentLoaded', () => {

    const dropdown = document.querySelector('.nav-dropdown');

    const toggle = document.querySelector('.nav-dropdown-toggle');

    if (!dropdown || !toggle) {
        return;
    }


    toggle.addEventListener('click', (event) => {

        event.stopPropagation();

        const abierto =
            dropdown.classList.toggle('open');

        toggle.setAttribute(
            'aria-expanded',
            abierto ? 'true' : 'false'
        );

    });


    document.addEventListener('click', (event) => {

        if (!dropdown.contains(event.target)) {

            dropdown.classList.remove('open');

            toggle.setAttribute(
                'aria-expanded',
                'false'
            );

        }

    });


    document.addEventListener('keydown', (event) => {

        if (event.key === 'Escape') {

            dropdown.classList.remove('open');

            toggle.setAttribute(
                'aria-expanded',
                'false'
            );

        }

    });

});