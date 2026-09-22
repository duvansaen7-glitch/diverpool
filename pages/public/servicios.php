<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../models/Servicio.php';

$pageTitle = 'Servicios | Diverpool Mascotas';

$pageDescription =
    'Conozca los servicios de Diverpool Mascotas para el cuidado y bienestar de su mascota.';

$servicioModel = new Servicio();

$services = $servicioModel->obtenerActivos();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

    <!-- =====================================================
         HERO
         ===================================================== -->

    <section class="hero">

        <div class="container">

            <div class="hero-copy">

                <span class="eyebrow">
                    NUESTROS SERVICIOS
                </span>

                <h1>
                    Todo lo que su mascota necesita,
                    <span>en un solo lugar.</span>
                </h1>

                <p>
                    Encuentre los servicios que necesita para
                    cuidar, consentir y mantener saludable a su mascota.
                </p>

            </div>

        </div>

    </section>


    <!-- =====================================================
         SERVICIOS
         ===================================================== -->

    <section class="services section">

        <div class="container">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        DIVERPOOL MASCOTAS
                    </span>

                    <h2>
                        Nuestros servicios
                    </h2>

                    <p>
                        Seleccione el servicio que necesita para su mascota.
                    </p>

                </div>

                <div class="services-count">

                    <strong>
                        <?= count($services) ?>
                    </strong>

                    <span>
                        servicios disponibles
                    </span>

                </div>

            </div>


            <?php if (empty($services)): ?>

                <div class="alert alert-info">

                    Actualmente no hay servicios disponibles.

                </div>

            <?php else: ?>


                <div class="service-grid">

                    <?php foreach ($services as $service): ?>

                        <?php

                        $categoria = strtolower(
                            trim($service['categoria_nombre'] ?? '')
                        );

                        /*
                         * Icono y clase visual según categoría.
                         */

                        $icon = '🐾';

                        $clase = 'general';

                        switch ($categoria) {

                            case 'peluquería':
                                $icon = '✂';
                                $clase = 'peluqueria';
                                break;

                            case 'veterinaria':
                                $icon = '+';
                                $clase = 'veterinaria';
                                break;

                            case 'terapias':
                                $icon = '♥';
                                $clase = 'terapias';
                                break;

                            case 'guardería':
                                $icon = '⌂';
                                $clase = 'guarderia';
                                break;

                            case 'guardería campestre':
                                $icon = '♣';
                                $clase = 'campestre';
                                break;

                            case 'consultas':
                                $icon = '▣';
                                $clase = 'consultas';
                                break;
                        }

                        ?>


                        <article class="service-card">

                            <!-- Imagen / icono -->

                            <div class="service-image service-<?= e($clase) ?>">

                                <span>
                                    <?= e($icon) ?>
                                </span>

                            </div>


                            <!-- Contenido -->

                            <div class="service-body">

                                <span class="service-category">
                                    <?= e($service['categoria_nombre']) ?>
                                </span>


                                <h3>
                                    <?= e($service['nombre']) ?>
                                </h3>


                                <p>

                                    <?= e(
                                        $service['descripcion']
                                        ?: 'Servicio especializado para el cuidado y bienestar de su mascota.'
                                    ) ?>

                                </p>


                                <!-- Información -->

                                <div class="service-meta">

                                    <span>
                                        <strong>
                                            $
                                        </strong>

                                        <?= number_format(
                                            (float) $service['precio'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>

                                    </span>


                                    <span>
                                        <?= (int) $service['duracion_minutos'] ?>
                                        min
                                    </span>

                                </div>


                                <!-- Acción -->

                                <?php if (usuarioAutenticado()): ?>

                                    <a href="<?= SITE_URL ?>/pages/usuario/nueva_reserva.php" class="service-action">
                                        Agendar
                                        <span>→</span>
                                    </a>

                                <?php else: ?>

                                    <a href="<?= SITE_URL ?>/login.php" class="service-action">
                                        Iniciar sesión
                                        <span>→</span>
                                    </a>

                                <?php endif; ?>


                            </div>

                        </article>


                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 MENSAJE PARA VISITANTES
                 ================================================= -->

            <?php if (!usuarioAutenticado()): ?>

                <div class="services-login-box">

                    <div class="services-login-icon">
                        🐾
                    </div>

                    <div class="services-login-content">

                        <strong>
                            ¿Ya encontró el servicio que necesita?
                        </strong>

                        <p>
                            Inicie sesión para seleccionar su mascota,
                            consultar los horarios disponibles y realizar
                            su reserva.
                        </p>

                    </div>

                    <div class="services-login-actions">

                        <a href="<?= SITE_URL ?>/login.php" class="btn btn-outline">
                            Iniciar sesión
                        </a>

                        <a href="<?= SITE_URL ?>/registro.php" class="btn btn-primary">
                            Crear cuenta
                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </section>


    <!-- =====================================================
         CIERRE
         ===================================================== -->

    <section class="section services-final">

        <div class="container">

            <div class="section-heading centered">

                <span class="eyebrow">
                    BIENESTAR Y CUIDADO
                </span>

                <h2>
                    Cuidamos a quienes hacen parte de su familia.
                </h2>

                <p>
                    Todo nuestro sistema está pensado para que pueda
                    organizar los servicios de su mascota de manera
                    sencilla y segura.
                </p>

            </div>

        </div>

    </section>

</main>


<?php

require_once __DIR__ . '/../../includes/footer.php';

?>