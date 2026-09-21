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

    <!-- ==========================================
         INICIO
    =========================================== -->

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

                    <a
                        class="btn btn-primary"
                        href="<?= SITE_URL ?>/pages/public/servicios.php"
                    >
                        Conocer servicios
                        <span>→</span>
                    </a>

                    <?php if (usuarioAutenticado()): ?>

                        <a
                            class="btn btn-outline"
                            href="<?= SITE_URL ?>/pages/usuario/mascotas.php"
                        >
                            Mis mascotas
                        </a>

                    <?php else: ?>

                        <a
                            class="btn btn-outline"
                            href="<?= SITE_URL ?>/registro.php"
                        >
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


            <!-- Ilustración principal -->

            <div class="hero-art">

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

            </div>

        </div>

    </section>


    <!-- ==========================================
         PRESENTACIÓN
    =========================================== -->

    <section class="section">

        <div class="container">

            <div class="section-heading centered">

                <span class="eyebrow">
                    DIVERPOOL MASCOTAS
                </span>

                <h2>
                    Cuidamos lo que más quiere.
                </h2>

                <p>
                    Una plataforma pensada para facilitar la gestión
                    de los servicios y el bienestar de su mascota.
                </p>

            </div>

        </div>

    </section>

</main>

<?php

require_once __DIR__ . '/includes/footer.php';

?>