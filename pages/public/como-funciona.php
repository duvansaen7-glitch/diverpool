<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/funciones.php';

$pageTitle = 'Cómo funciona | Diverpool Mascotas';

$pageDescription =
    'Conozca cómo agendar y gestionar servicios para su mascota en Diverpool Mascotas.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

    <!-- ==========================================
         CÓMO FUNCIONA
    =========================================== -->

    <section class="hero">

        <div class="container">

            <div class="hero-copy">

                <span class="eyebrow">
                    ¿CÓMO FUNCIONA?
                </span>

                <h1>
                    Agendar el cuidado de su mascota
                    <span>es sencillo.</span>
                </h1>

                <p>
                    Siga unos sencillos pasos para encontrar
                    el servicio que necesita y gestionar su reserva.
                </p>

            </div>

        </div>

    </section>


    <section class="steps section">

        <div class="container">

            <div class="section-heading centered">

                <span class="eyebrow">
                    DIVERPOOL MASCOTAS
                </span>

                <h2>
                    Agende en 4 simples pasos
                </h2>

                <p>
                    Desde crear su cuenta hasta confirmar su reserva,
                    todo el proceso está pensado para ser sencillo.
                </p>

            </div>


            <div class="steps-grid">

                <div class="step">

                    <i>1</i>

                    <b>
                        Regístrese
                    </b>

                    <p>
                        Cree su cuenta en Diverpool Mascotas
                        para comenzar a gestionar sus servicios.
                    </p>

                </div>


                <div class="step">

                    <i>2</i>

                    <b>
                        Registre su mascota
                    </b>

                    <p>
                        Agregue la información de su mascota
                        para tenerla asociada a su cuenta.
                    </p>

                </div>


                <div class="step">

                    <i>3</i>

                    <b>
                        Elija el servicio
                    </b>

                    <p>
                        Seleccione el servicio que necesita
                        para su mascota.
                    </p>

                </div>


                <div class="step">

                    <i>4</i>

                    <b>
                        Confirme
                    </b>

                    <p>
                        Seleccione la fecha y hora disponibles
                        y confirme su reserva.
                    </p>

                </div>

            </div>

        </div>

    </section>


    <section class="section">

        <div class="container">

            <div class="about-card">

                <div>

                    <span class="eyebrow">
                        TODO EN UN SOLO LUGAR
                    </span>

                    <h2>
                        Gestione el cuidado de su mascota
                        de forma organizada.
                    </h2>

                    <p>
                        Desde su cuenta podrá administrar sus mascotas,
                        consultar sus reservas y acceder a los servicios
                        disponibles en Diverpool Mascotas.
                    </p>

                    <?php if (usuarioAutenticado()): ?>

                        <a
                            class="btn btn-primary"
                            href="<?= SITE_URL ?>/pages/usuario/mascotas.php"
                        >
                            Ver mis mascotas
                        </a>

                    <?php else: ?>

                        <a
                            class="btn btn-primary"
                            href="<?= SITE_URL ?>/registro.php"
                        >
                            Crear cuenta
                        </a>

                    <?php endif; ?>

                </div>


                <div class="about-pets">

                    🐶

                    <span>
                        ♡
                    </span>

                    🐱

                </div>

            </div>

        </div>

    </section>

</main>

<?php

require_once __DIR__ . '/../../includes/footer.php';

?>
