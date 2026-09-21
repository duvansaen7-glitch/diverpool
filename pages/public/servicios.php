<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/funciones.php';

$pageTitle = 'Servicios | Diverpool Mascotas';
$pageDescription = 'Conozca los servicios de Diverpool Mascotas para el cuidado y bienestar de su mascota.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

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

                </div>

            </div>


            <div class="service-grid">

                <?php

                $services = [

                    [
                        'icon' => '✂',
                        'name' => 'Peluquería canina',
                        'description' => 'Baño, corte, cepillado y cuidado estético.',
                        'class' => 'peluqueria'
                    ],

                    [
                        'icon' => '♥',
                        'name' => 'Terapias',
                        'description' => 'Fisioterapia, rehabilitación y terapias especializadas.',
                        'class' => 'terapias'
                    ],

                    [
                        'icon' => '+',
                        'name' => 'Veterinaria',
                        'description' => 'Consultas, vacunación, desparasitación y más.',
                        'class' => 'veterinaria'
                    ],

                    [
                        'icon' => '⌂',
                        'name' => 'Guardería',
                        'description' => 'Un espacio seguro y divertido mientras no está.',
                        'class' => 'guarderia'
                    ],

                    [
                        'icon' => '♣',
                        'name' => 'Guardería campestre',
                        'description' => 'Espacios amplios y naturales para su bienestar.',
                        'class' => 'campestre'
                    ],

                    [
                        'icon' => '▣',
                        'name' => 'Consultas',
                        'description' => 'Valoraciones generales y asesorías especializadas.',
                        'class' => 'consultas'
                    ]

                ];

                foreach ($services as $service):

                ?>

                    <article class="service-card">

                        <div
                            class="service-image service-<?= e($service['class']) ?>"
                        >

                            <span>
                                <?= e($service['icon']) ?>
                            </span>

                        </div>

                        <div class="service-body">

                            <h3>
                                <?= e($service['name']) ?>
                            </h3>

                            <p>
                                <?= e($service['description']) ?>
                            </p>

                            <a href="../../registro.php">
                                Agendar
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </div>

    </section>

</main>

<?php

require_once __DIR__ . '/../../includes/footer.php';

?>

