<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado() || !tieneRol('Cliente')) {
    redirect(SITE_URL . '/login.php');
}

$pageTitle = 'Mi cuenta | ' . SITE_NAME;
$pageDescription = 'Panel personal de usuario de Diverpool Mascotas.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main class="container">

    <section class="page-header">

        <div>

            <h1>
                Mi cuenta
            </h1>

            <p>
                Bienvenido,
                <strong>
                    <?= e($_SESSION['usuario_nombres']) ?>
                </strong>.
            </p>

        </div>

    </section>


    <section class="dashboard-grid">


        <!-- MI PERFIL -->

        <article class="dashboard-card">

            <div class="dashboard-card-icon">
                👤
            </div>

            <div>

                <h2>
                    Mi perfil
                </h2>

                <p>
                    Consulte y actualice sus datos personales.
                </p>

                <a class="btn btn-primary btn-small" href="<?= SITE_URL ?>/pages/usuario/perfil.php">
                    Ver perfil
                </a>

            </div>

        </article>


        <!-- MIS MASCOTAS -->

        <article class="dashboard-card">

            <div class="dashboard-card-icon">
                🐾
            </div>

            <div>

                <h2>
                    Mis mascotas
                </h2>

                <p>
                    Administre la información de sus mascotas.
                </p>

                <a class="btn btn-primary btn-small" href="<?= SITE_URL ?>/pages/usuario/mascotas.php">
                    Ver mascotas
                </a>

            </div>

        </article>


        <!-- MIS RESERVAS -->

        <article class="dashboard-card">

            <div class="dashboard-card-icon">
                📅
            </div>

            <div>

                <h2>
                    Mis reservas
                </h2>

                <p>
                    Consulte y gestione sus citas y servicios.
                </p>

                <a class="btn btn-primary btn-small" href="<?= SITE_URL ?>/pages/usuario/reservas.php">
                    Ver reservas
                </a>

            </div>

        </article>


        <!-- AGENDAR SERVICIO -->

        <article class="dashboard-card">

            <div class="dashboard-card-icon">
                🩺
            </div>

            <div>

                <h2>
                    Agendar servicio
                </h2>

                <p>
                    Solicite una nueva cita para su mascota.
                </p>

                <a class="btn btn-primary btn-small" href="<?= SITE_URL ?>/pages/usuario/nueva_reserva.php">
                    Agendar cita
                </a>

            </div>

        </article>


    </section>

</main>


<?php require_once __DIR__ . '/../../includes/footer.php'; ?>