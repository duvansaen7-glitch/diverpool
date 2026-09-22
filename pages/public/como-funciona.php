<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/funciones.php';

$pageTitle = 'Cómo funciona | Diverpool Mascotas';

$pageDescription =
    'Conozca cómo agendar y gestionar servicios para su mascota en Diverpool Mascotas.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main class="how-page">

    <!-- ==========================================
         DECORACIONES DE FONDO
    =========================================== -->

    <div class="how-decoration how-decoration-one"></div>
    <div class="how-decoration how-decoration-two"></div>


    <!-- ==========================================
         INTRODUCCIÓN
    =========================================== -->

    <section class="how-intro">

        <div class="container">

            <span class="how-eyebrow">
                <span class="eyebrow-line"></span>
                ¿CÓMO FUNCIONA DIVERPOOL?
            </span>

            <h1>
                Cuidar a su mascota
                <span>es más sencillo.</span>
            </h1>

            <p>
                Desde registrar su mascota hasta confirmar la cita,
                hemos diseñado el proceso para que sea rápido y fácil.
            </p>

        </div>

    </section>


    <!-- ==========================================
         PASOS
    =========================================== -->

    <section class="how-steps-section">

        <div class="container">

            <!-- ==================================
                 SELECTOR DE PASOS
            =================================== -->

            <div class="how-steps">


                <!-- PASO 1 -->

                <button type="button" class="how-step active" data-step="0">

                    <span class="step-number">
                        01
                    </span>

                    <span class="step-icon">
                        <span>✦</span>
                    </span>

                    <span class="step-title">
                        Registre su mascota
                    </span>

                    <span class="step-short">
                        Cree su perfil
                    </span>

                </button>


                <!-- CONECTOR -->

                <span class="step-connector"></span>


                <!-- PASO 2 -->

                <button type="button" class="how-step" data-step="1">

                    <span class="step-number">
                        02
                    </span>

                    <span class="step-icon">
                        <span>＋</span>
                    </span>

                    <span class="step-title">
                        Elija el servicio
                    </span>

                    <span class="step-short">
                        Seleccione el cuidado
                    </span>

                </button>


                <!-- CONECTOR -->

                <span class="step-connector"></span>


                <!-- PASO 3 -->

                <button type="button" class="how-step" data-step="2">

                    <span class="step-number">
                        03
                    </span>

                    <span class="step-icon">
                        <span>◷</span>
                    </span>

                    <span class="step-title">
                        Seleccione el horario
                    </span>

                    <span class="step-short">
                        Elija cuándo venir
                    </span>

                </button>


                <!-- CONECTOR -->

                <span class="step-connector"></span>


                <!-- PASO 4 -->

                <button type="button" class="how-step" data-step="3">

                    <span class="step-number">
                        04
                    </span>

                    <span class="step-icon">
                        <span>✓</span>
                    </span>

                    <span class="step-title">
                        Confirme su reserva
                    </span>

                    <span class="step-short">
                        Todo listo
                    </span>

                </button>

            </div>


            <!-- ==================================
                 CONTENIDO DEL PASO
            =================================== -->

            <div class="how-content">


                <!-- ==================================
                     PARTE VISUAL
                =================================== -->

                <div class="how-visual">

                    <div class="visual-glow"></div>

                    <div class="visual-number">
                        01
                    </div>

                    <div class="visual-card">

                        <div class="visual-circle">

                            <span class="visual-symbol">
                                ✦
                            </span>

                        </div>


                        <div class="visual-lines">

                            <span></span>

                            <span></span>

                            <span></span>

                        </div>

                    </div>


                    <!-- TARJETA FLOTANTE -->

                    <div class="floating-card floating-card-top">

                        <span class="floating-dot"></span>

                        Fácil y rápido

                    </div>


                    <!-- TARJETA FLOTANTE -->

                    <div class="floating-card floating-card-bottom">

                        <span class="floating-check">
                            ✓
                        </span>

                        Experiencia personalizada

                    </div>

                </div>


                <!-- ==================================
                     INFORMACIÓN
                =================================== -->

                <div class="how-info">

                    <span class="how-info-label">
                        PASO 01
                    </span>


                    <h2>
                        Registre a su mascota
                    </h2>


                    <p>
                        Agregue la información de su mascota para
                        tener todos sus datos organizados y facilitar
                        cada visita a Diverpool.
                    </p>


                    <ul class="how-list">

                        <li>

                            <span>
                                ✓
                            </span>

                            Información básica de su mascota

                        </li>


                        <li>

                            <span>
                                ✓
                            </span>

                            Datos importantes para su cuidado

                        </li>


                        <li>

                            <span>
                                ✓
                            </span>

                            Perfil disponible para futuras reservas

                        </li>

                    </ul>


                    <?php if (usuarioAutenticado()): ?>

                        <a href="<?= SITE_URL ?>/pages/usuario/mascotas.php" class="how-button">

                            Ver mis mascotas

                            <span>
                                →
                            </span>

                        </a>

                    <?php else: ?>

                        <a href="<?= SITE_URL ?>/registro.php" class="how-button">

                            Comenzar ahora

                            <span>
                                →
                            </span>

                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </section>


    <!-- ==========================================
         SECCIÓN FINAL
    =========================================== -->

    <section class="how-final">

        <div class="container">

            <div class="how-final-box">

                <div>

                    <span class="how-final-label">
                        DIVERPOOL MASCOTAS
                    </span>

                    <h2>
                        Todo el cuidado de su mascota,
                        en un solo lugar.
                    </h2>

                </div>


                <a href="<?= SITE_URL ?>/servicios.php" class="how-button">

                    Explorar servicios

                    <span>
                        →
                    </span>

                </a>

            </div>

        </div>

    </section>

</main>


<!-- ==========================================
     JAVASCRIPT
=========================================== -->

<script src="<?= SITE_URL ?>/public/js/como-funciona.js"></script>


<?php

require_once __DIR__ . '/../../includes/footer.php';

?>