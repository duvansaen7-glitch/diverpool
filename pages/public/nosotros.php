<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/funciones.php';

$pageTitle = 'Nosotros | Diverpool Mascotas';
$pageDescription = 'Conozca Diverpool Mascotas y nuestra propuesta para el cuidado y bienestar de las mascotas.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

    <!-- Encabezado -->
    <section class="hero">
        <div class="container">
            <div class="hero-copy">

                <span class="eyebrow">SOBRE NOSOTROS</span>

                <h1>
                    Cuidamos a las mascotas
                    <span>como parte de la familia.</span>
                </h1>

                <p>
                    Diverpool Mascotas es una plataforma pensada para facilitar
                    el acceso y la gestión de servicios orientados al cuidado,
                    salud y bienestar de las mascotas.
                </p>

            </div>
        </div>
    </section>


    <!-- Nuestra propuesta -->
    <section class="section">

        <div class="container">

            <div class="about-card">

                <div>

                    <span class="eyebrow">DIVERPOOL MASCOTAS</span>

                    <h2>
                        Una forma más sencilla de gestionar el cuidado de su mascota.
                    </h2>

                    <p>
                        Nuestro propósito es reunir diferentes servicios para
                        mascotas en un mismo lugar, facilitando la búsqueda,
                        organización y gestión de las atenciones que necesitan.
                    </p>

                    <p>
                        Desde servicios de peluquería y terapias hasta
                        veterinaria, guardería y consultas, buscamos ofrecer
                        una experiencia organizada para los propietarios
                        y sus mascotas.
                    </p>

                </div>

                <div class="about-pets">
                    🐶
                    <span>♡</span>
                    🐱
                </div>

            </div>

        </div>

    </section>


    <!-- Qué ofrecemos -->
    <section class="section">

        <div class="container">

            <div class="section-heading centered">

                <span class="eyebrow">NUESTRA PROPUESTA</span>

                <h2>
                    Pensado para mascotas y propietarios.
                </h2>

                <p>
                    Diverpool Mascotas busca centralizar los servicios y
                    facilitar su gestión desde una sola plataforma.
                </p>

            </div>


            <div class="service-grid">

                <article class="service-card">

                    <div class="service-image service-peluqueria">
                        <span>♡</span>
                    </div>

                    <div class="service-body">

                        <h3>Bienestar</h3>

                        <p>
                            Servicios orientados al cuidado y bienestar
                            integral de las mascotas.
                        </p>

                    </div>

                </article>


                <article class="service-card">

                    <div class="service-image service-veterinaria">
                        <span>+</span>
                    </div>

                    <div class="service-body">

                        <h3>Atención</h3>

                        <p>
                            Acceso organizado a servicios profesionales
                            para diferentes necesidades.
                        </p>

                    </div>

                </article>


                <article class="service-card">

                    <div class="service-image service-guarderia">
                        <span>⌂</span>
                    </div>

                    <div class="service-body">

                        <h3>Organización</h3>

                        <p>
                            Una plataforma para gestionar mascotas,
                            servicios y reservas desde un mismo lugar.
                        </p>

                    </div>

                </article>

            </div>

        </div>

    </section>


    <!-- Cierre -->
    <section class="section">

        <div class="container">

            <div class="section-heading centered">

                <span class="eyebrow">DIVERPOOL MASCOTAS</span>

                <h2>
                    Porque cada mascota merece un buen cuidado.
                </h2>

                <p>
                    Conozca nuestros servicios y encuentre la atención
                    que necesita su mascota.
                </p>

                <a
                    class="btn btn-primary"
                    href="<?= SITE_URL ?>/pages/public/servicios.php"
                >
                    Ver servicios
                </a>

            </div>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

