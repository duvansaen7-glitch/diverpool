<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/funciones.php';

$pageTitle = 'Diverpool Mascotas | Cuidado y bienestar';

$pageDescription =
    'Servicios y bienestar para mascotas en un solo lugar.';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

?>

<main>

    <!-- =====================================================
         HERO
         ===================================================== -->

    <section class="hero">

        <div class="container hero-grid">

            <div class="hero-copy">

                <span class="eyebrow">
                    Bienestar para quienes hacen feliz su vida
                </span>

                <h1>
                    Todo el cuidado que su mascota necesita,
                    <span>en un solo lugar.</span>
                </h1>

                <p>
                    Encuentre y agende servicios para su mascota
                    de forma sencilla, segura y organizada.
                </p>

                <div class="hero-actions">

                    <a class="btn btn-primary" href="<?= SITE_URL ?>/pages/public/servicios.php">
                        Explorar servicios
                        <span>→</span>
                    </a>

                    <?php if (usuarioAutenticado()): ?>

                        <a class="btn btn-outline" href="<?= SITE_URL ?>/pages/usuario/mascotas.php">
                            Mis mascotas
                        </a>

                    <?php else: ?>

                        <a class="btn btn-outline" href="<?= SITE_URL ?>/registro.php">
                            Crear cuenta
                        </a>

                    <?php endif; ?>

                </div>


                <div class="trust-row">

                    <div>

                        <b>✓</b>

                        <span>
                            Profesionales<br>
                            especializados
                        </span>

                    </div>


                    <div>

                        <b>♡</b>

                        <span>
                            Atención<br>
                            personalizada
                        </span>

                    </div>


                    <div>

                        <b>✦</b>

                        <span>
                            Espacios seguros<br>
                            y confiables
                        </span>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 VISUAL DEL HERO
                 ================================================= -->

            <div class="hero-art">

                <div class="hero-circle hero-circle-one"></div>

                <div class="hero-circle hero-circle-two"></div>

                <div class="blob"></div>


                <div class="pet-card dog">

                    <div class="pet-emoji">
                        🐶
                    </div>

                    <strong>
                        Peluditos felices
                    </strong>

                    <small>
                        Cuidado con cariño
                    </small>

                </div>


                <div class="pet-card cat">

                    <div class="pet-emoji">
                        🐱
                    </div>

                    <strong>
                        Familias tranquilas
                    </strong>

                    <small>
                        Todo en un solo lugar
                    </small>

                </div>


                <div class="hero-note">

                    Pequeñas patas,<br>

                    <b>
                        grandes historias.
                    </b>

                    ♡

                </div>


                <div class="hero-floating-icon icon-heart">
                    ♥
                </div>


                <div class="hero-floating-icon icon-paw">
                    🐾
                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         SLIDER DE IMÁGENES
         ===================================================== -->

    <section class="home-slider-section">

        <div class="container">

            <div class="home-slider">

                <div class="slider-track">


                    <!-- =================================================
                         SLIDE 1
                         ================================================= -->

                    <article class="home-slide active">

                        <img src="<?= SITE_URL ?>/public/img/slider/piscina.jpeg" alt="Mascota recibiendo cuidados">

                        <div class="slide-overlay"></div>

                        <div class="slide-content">

                            <span>
                                DIVERPOOL MASCOTAS
                            </span>

                            <h2>
                                Cuidado que se siente.
                            </h2>

                            <p>
                                Todo lo que su mascota necesita
                                para estar feliz y saludable.
                            </p>

                        </div>

                    </article>


                    <!-- =================================================
                         SLIDE 2
                         ================================================= -->

                    <article class="home-slide">

                        <img src="<?= SITE_URL ?>/public/img/slider/perro.jpeg" alt="Mascota recibiendo atención">

                        <div class="slide-overlay"></div>

                        <div class="slide-content">

                            <span>
                                BIENESTAR
                            </span>

                            <h2>
                                Porque también es parte de la familia.
                            </h2>

                            <p>
                                Servicios pensados para cada etapa
                                de la vida de su mascota.
                            </p>

                        </div>

                    </article>


                    <!-- =================================================
                         SLIDE 3
                         ================================================= -->

                    <article class="home-slide">

                        <img src="<?= SITE_URL ?>/public/img/slider/mascota-3.jpg"
                            alt="Mascota disfrutando de sus cuidados">

                        <div class="slide-overlay"></div>

                        <div class="slide-content">

                            <span>
                                SERVICIOS
                            </span>

                            <h2>
                                Un solo lugar para cuidarla.
                            </h2>

                            <p>
                                Peluquería, veterinaria, terapias,
                                guardería y mucho más.
                            </p>

                        </div>

                    </article>


                    <!-- =================================================
                         SLIDE 4
                         ================================================= -->

                    <article class="home-slide">

                        <img src="<?= SITE_URL ?>/public/img/slider/mascota-4.jpg" alt="Mascota feliz">

                        <div class="slide-overlay"></div>

                        <div class="slide-content">

                            <span>
                                DIVERPOOL
                            </span>

                            <h2>
                                Pequeñas patas, grandes historias.
                            </h2>

                            <p>
                                Queremos acompañarle en cada momento
                                del cuidado de su mascota.
                            </p>

                        </div>

                    </article>

                </div>


                <!-- =================================================
                     BOTÓN ANTERIOR
                     ================================================= -->

                <button type="button" class="slider-button slider-prev" aria-label="Imagen anterior">
                    ‹
                </button>


                <!-- =================================================
                     BOTÓN SIGUIENTE
                     ================================================= -->

                <button type="button" class="slider-button slider-next" aria-label="Imagen siguiente">
                    ›
                </button>


                <!-- =================================================
                     INDICADORES
                     ================================================= -->

                <div class="slider-dots">

                    <button type="button" class="slider-dot active" aria-label="Mostrar imagen 1"></button>

                    <button type="button" class="slider-dot" aria-label="Mostrar imagen 2"></button>

                    <button type="button" class="slider-dot" aria-label="Mostrar imagen 3"></button>

                    <button type="button" class="slider-dot" aria-label="Mostrar imagen 4"></button>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         EXPLORADOR DE SERVICIOS
         ===================================================== -->

    <section class="service-explorer-section">

        <div class="container">

            <div class="service-explorer">

                <div class="service-explorer-header">

                    <div>

                        <span class="eyebrow">
                            DIVERPOOL MASCOTAS
                        </span>

                        <h2>
                            ¿Qué necesita su mascota?
                        </h2>

                        <p>
                            Explore nuestros servicios y encuentre
                            la atención adecuada para su compañero.
                        </p>

                    </div>


                    <span class="service-explorer-paw">
                        🐾
                    </span>

                </div>


                <!-- =================================================
                     MENÚ DESPLEGABLE DE SERVICIOS
                     ================================================= -->

                <details class="service-dropdown">

                    <summary>

                        <span class="summary-icon">
                            ✦
                        </span>

                        <span class="summary-text">

                            <strong>
                                Explorar servicios
                            </strong>

                            <small>
                                Ver todas las categorías disponibles
                            </small>

                        </span>

                        <span class="summary-arrow">
                            ↓
                        </span>

                    </summary>


                    <div class="service-option-grid">


                        <!-- PELUQUERÍA -->

                        <a href="<?= SITE_URL ?>/pages/public/servicios.php"
                            class="service-option service-option-purple">

                            <span class="service-option-icon">
                                ✂
                            </span>

                            <span>

                                <strong>
                                    Peluquería
                                </strong>

                                <small>
                                    Baño, secado y corte
                                </small>

                            </span>

                        </a>


                        <!-- VETERINARIA -->

                        <a href="<?= SITE_URL ?>/pages/public/servicios.php" class="service-option service-option-blue">

                            <span class="service-option-icon">
                                ✚
                            </span>

                            <span>

                                <strong>
                                    Veterinaria
                                </strong>

                                <small>
                                    Salud y valoración
                                </small>

                            </span>

                        </a>


                        <!-- TERAPIAS -->

                        <a href="<?= SITE_URL ?>/pages/public/servicios.php"
                            class="service-option service-option-green">

                            <span class="service-option-icon">
                                ♡
                            </span>

                            <span>

                                <strong>
                                    Terapias
                                </strong>

                                <small>
                                    Recuperación y bienestar
                                </small>

                            </span>

                        </a>


                        <!-- GUARDERÍA -->

                        <a href="<?= SITE_URL ?>/pages/public/servicios.php"
                            class="service-option service-option-orange">

                            <span class="service-option-icon">
                                🏠
                            </span>

                            <span>

                                <strong>
                                    Guardería
                                </strong>

                                <small>
                                    Cuidado durante el día
                                </small>

                            </span>

                        </a>


                        <!-- GUARDERÍA CAMPESTRE -->

                        <a href="<?= SITE_URL ?>/pages/public/servicios.php"
                            class="service-option service-option-green-dark">

                            <span class="service-option-icon">
                                🌳
                            </span>

                            <span>

                                <strong>
                                    Guardería campestre
                                </strong>

                                <small>
                                    Espacios al aire libre
                                </small>

                            </span>

                        </a>


                        <!-- CONSULTAS -->

                        <a href="<?= SITE_URL ?>/pages/public/servicios.php" class="service-option service-option-pink">

                            <span class="service-option-icon">
                                💬
                            </span>

                            <span>

                                <strong>
                                    Consultas
                                </strong>

                                <small>
                                    Comportamiento y nutrición
                                </small>

                            </span>

                        </a>


                    </div>

                </details>

            </div>

        </div>

    </section>


    <!-- =====================================================
         PRESENTACIÓN
         ===================================================== -->

    <section class="section home-presentation">

        <div class="container">

            <div class="section-heading centered">

                <span class="eyebrow">
                    UNA PLATAFORMA PENSADA PARA USTED
                </span>

                <h2>
                    Cuidamos lo que más quiere.
                </h2>

                <p>
                    Organice los servicios, citas y cuidados de
                    su mascota desde un solo lugar.
                </p>

            </div>

        </div>

    </section>


</main>


<!-- =========================================================
     JAVASCRIPT DEL SLIDER
     Se carga antes del footer para evitar problemas de ruta.
     ========================================================= -->

<script src="<?= SITE_URL ?>/public/js/slider.js"></script>


<?php

require_once __DIR__ . '/includes/footer.php';

?>