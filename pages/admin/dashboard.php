<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado() || !tieneRol('Administrador')) {
    redirect(SITE_URL . '/login.php');
}

$pageTitle = 'Panel administrativo | Diverpool Mascotas';
$pageDescription = 'Administración general de Diverpool Mascotas.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

    <section class="section">

        <div class="container">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        ADMINISTRACIÓN
                    </span>

                    <h1>
                        Panel administrativo
                    </h1>

                    <p>
                        Gestione usuarios, profesionales, servicios
                        y reservas de Diverpool Mascotas.
                    </p>

                </div>

            </div>

            <div class="service-grid">

                <article class="service-card">

                    <div class="service-icon">
                        👥
                    </div>

                    <div class="service-body">

                        <h3>
                            Usuarios
                        </h3>

                        <p>
                            Gestione clientes, profesionales y
                            administradores del sistema.
                        </p>

                        <a href="<?= SITE_URL ?>/pages/admin/usuarios.php" class="btn btn-primary">
                            Gestionar usuarios
                        </a>

                    </div>

                </article>

                <article class="service-card">

                    <div class="service-icon">
                        👨‍⚕️
                    </div>

                    <div class="service-body">

                        <h3>
                            Profesionales
                        </h3>

                        <p>
                            Gestione especialistas, especialidades,
                            servicios y disponibilidad.
                        </p>

                        <a href="<?= SITE_URL ?>/pages/admin/profesionales.php" class="btn btn-primary">
                            Gestionar profesionales
                        </a>

                    </div>

                </article>

                <article class="service-card">

                    <div class="service-icon">
                        🐾
                    </div>

                    <div class="service-body">

                        <h3>
                            Servicios
                        </h3>

                        <p>
                            Administre los servicios ofrecidos por
                            Diverpool Mascotas.
                        </p>

                        <a href="<?= SITE_URL ?>/pages/admin/servicios.php" class="btn btn-primary">
                            Gestionar servicios
                        </a>

                    </div>

                </article>

                <article class="service-card">

                    <div class="service-icon">
                        📅
                    </div>

                    <div class="service-body">

                        <h3>
                            Reservas
                        </h3>

                        <p>
                            Consulte y gestione las reservas
                            realizadas por los clientes.
                        </p>

                        <a href="<?= SITE_URL ?>/pages/admin/reservas.php" class="btn btn-primary">
                            Gestionar reservas
                        </a>

                    </div>

                </article>

                <article class="service-card">

                    <div class="service-icon">
                        📊
                    </div>

                    <div class="service-body">

                        <h3>
                            Reportes
                        </h3>

                        <p>
                            Consulte información y estadísticas
                            del funcionamiento del sistema.
                        </p>

                        <a href="<?= SITE_URL ?>/pages/admin/reportes.php" class="btn btn-primary">
                            Ver reportes
                        </a>

                    </div>

                </article>

            </div>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>