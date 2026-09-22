document.addEventListener('DOMContentLoaded', () => {

    const slides = document.querySelectorAll('.home-slide');
    const dots = document.querySelectorAll('.slider-dot');

    const previousButton = document.querySelector('.slider-prev');
    const nextButton = document.querySelector('.slider-next');

    const slider = document.querySelector('.home-slider');

    if (!slides.length) {
        return;
    }

    let currentSlide = 0;
    let automaticChange;


    function mostrarSlide(index) {

        slides.forEach((slide, i) => {

            slide.classList.toggle(
                'active',
                i === index
            );

        });


        dots.forEach((dot, i) => {

            dot.classList.toggle(
                'active',
                i === index
            );

        });

        currentSlide = index;
    }


    function siguiente() {

        const siguienteIndex =
            (currentSlide + 1) % slides.length;

        mostrarSlide(siguienteIndex);
    }


    function anterior() {

        const anteriorIndex =
            (currentSlide - 1 + slides.length)
            % slides.length;

        mostrarSlide(anteriorIndex);
    }


    function iniciarAutomatico() {

        clearInterval(automaticChange);

        automaticChange = setInterval(() => {

            siguiente();

        }, 5000);
    }


    previousButton?.addEventListener('click', () => {

        anterior();

        iniciarAutomatico();

    });


    nextButton?.addEventListener('click', () => {

        siguiente();

        iniciarAutomatico();

    });


    dots.forEach((dot, index) => {

        dot.addEventListener('click', () => {

            mostrarSlide(index);

            iniciarAutomatico();

        });

    });


    slider?.addEventListener('mouseenter', () => {

        clearInterval(automaticChange);

    });


    slider?.addEventListener('mouseleave', () => {

        iniciarAutomatico();

    });


    mostrarSlide(0);

    iniciarAutomatico();

});

