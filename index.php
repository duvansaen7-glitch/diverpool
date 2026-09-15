<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/funciones.php';

$pageTitle = 'Diverpoool Mascotas | Cuidado y bienestar';
$pageDescription = 'Encuentre y agende servicios de peluquería canina, terapias, veterinaria, consultas y guardería para su mascota.';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

?>

<main>

    <!-- ==========================================
         HERO
    =========================================== -->
    <section class="hero">

        <div class="container hero-grid">

            <div class="hero-copy">

                <span class="eyebrow">
                    🐾 Bienestar para quienes hacen feliz su vida
                </span>

                <h1>
                    Todo el cuidado que su mascota necesita,
                    <span>en un solo lugar.</span>
                </h1>

                <p>
                    Encuentre y agende servicios para su mascota
                    de forma sencilla: peluquería canina, terapias,
                    veterinaria, consultas y guardería.
                </p>

                <div class="hero-actions">

                    <a
                        class="btn btn-primary"
                        href="registro.php"
                    >
                        Agendar un servicio
                        <span>→</span>
                    </a>

                    <a
                        class="btn btn-outline"
                        href="#servicios"
                    >
                        Conocer servicios
                    </a>

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
         SERVICIOS
    =========================================== -->
    <section
        class="services section"
        id="servicios"
    >

        <div class="container">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        NUESTROS SERVICIOS
                    </span>

                    <h2>
                        Todo lo que su mascota necesita
                    </h2>

                </div>

                <a href="pages/servicios.php">
                    Ver todos →
                </a>

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

                            <a href="registro.php">
                                Agendar
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </div>

    </section>



    <!-- ==========================================
         ESTADÍSTICAS
    =========================================== -->
    <section class="stats">

        <div class="container stats-grid">

            <div>

                <strong>
                    +500
                </strong>

                <span>
                    Mascotas felices
                </span>

            </div>


            <div>

                <strong>
                    +1.000
                </strong>

                <span>
                    Servicios agendados
                </span>

            </div>


            <div>

                <strong>
                    98%
                </strong>

                <span>
                    Clientes satisfechos
                </span>

            </div>


            <div>

                <strong>
                    +5
                </strong>

                <span>
                    Años de experiencia
                </span>

            </div>

        </div>

    </section>



    <!-- ==========================================
         CÓMO FUNCIONA
    =========================================== -->
    <section
        class="steps section"
        id="como-funciona"
    >

        <div class="container">

            <div class="section-heading centered">

                <div>

                    <span class="eyebrow">
                        ¿CÓMO FUNCIONA?
                    </span>

                    <h2>
                        Agende en 4 simples pasos
                    </h2>

                </div>

            </div>


            <div class="steps-grid">

                <div class="step">

                    <i>1</i>

                    <b>
                        Regístrese
                    </b>

                    <p>
                        Cree su cuenta en Diverpoool.
                    </p>

                </div>


                <div class="step">

                    <i>2</i>

                    <b>
                        Elija el servicio
                    </b>

                    <p>
                        Seleccione lo que su mascota necesita.
                    </p>

                </div>


                <div class="step">

                    <i>3</i>

                    <b>
                        Seleccione fecha y hora
                    </b>

                    <p>
                        Elija el momento que más le convenga.
                    </p>

                </div>


                <div class="step">

                    <i>4</i>

                    <b>
                        Confirme
                    </b>

                    <p>
                        Reciba la confirmación de su reserva.
                    </p>

                </div>

            </div>

        </div>

    </section>



    <!-- ==========================================
         NOSOTROS
    =========================================== -->
    <section
        class="about section"
        id="nosotros"
    >

        <div class="container about-card">

            <div>

                <span class="eyebrow">
                    DIVERPOOOL MASCOTAS
                </span>

                <h2>
                    Más que servicios, creamos bienestar.
                </h2>

                <p>
                    Queremos facilitarle el cuidado de su mascota,
                    conectando en un mismo lugar los servicios que
                    necesita y haciendo más sencillo el proceso
                    de agendamiento.
                </p>

                <a
                    class="btn btn-primary"
                    href="#contacto"
                >
                    Conocer más
                </a>

            </div>


            <div class="about-pets">

                🐶

                <span>
                    ♡
                </span>

                🐱

            </div>

        </div>

    </section>



    <!-- ==========================================
         CONTACTO
    =========================================== -->
    <section
        class="contact section"
        id="contacto"
    >

        <div class="container contact-grid">

            <div>

                <span class="eyebrow">
                    ESTAMOS PARA AYUDARLE
                </span>

                <h2>
                    ¿Tiene alguna pregunta?
                </h2>

                <p>
                    Comuníquese con nosotros y le orientaremos
                    sobre nuestros servicios y reservas.
                </p>

            </div>


            <div class="contact-box">

                <div>

                    ✉

                    <span>
                        contacto@diverpoool.com
                    </span>

                </div>


                <div>

                    ☎

                    <span>
                        +57 300 000 0000
                    </span>

                </div>


                <div>

                    ⌖

                    <span>
                        Bogotá, Colombia
                    </span>

                </div>

            </div>

        </div>

    </section>

</main>


<?php

require_once __DIR__ . '/includes/footer.php';

?>