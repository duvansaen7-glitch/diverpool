<?php

require_once __DIR__ . '/funciones.php';

?>

<header class="site-header">

    <div class="container nav-wrap">

        <a class="brand" href="<?= SITE_URL ?>/index.php" aria-label="Diverpool Mascotas">

            <span class="brand-mark">D</span>

            <span>
                <strong>Diverpool</strong>
                <small>MASCOTAS</small>
            </span>

        </a>

        <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú" type="button">
            ☰
        </button>

        <nav id="mainNav">

            <!-- ENLACES PÚBLICOS -->

            <a href="<?= SITE_URL ?>/index.php">
                Inicio
            </a>

            <a href="<?= SITE_URL ?>/pages/public/servicios.php">
                Servicios
            </a>

            <a href="<?= SITE_URL ?>/pages/public/como-funciona.php">
                Cómo funciona
            </a>

            <a href="<?= SITE_URL ?>/pages/public/nosotros.php">
                Nosotros
            </a>

            <a href="<?= SITE_URL ?>/pages/public/contacto.php">
                Contacto
            </a>


            <?php if (usuarioAutenticado()): ?>

                <!-- USUARIO AUTENTICADO -->

                <span class="nav-user">
                    Hola,
                    <strong>
                        <?= e($_SESSION['usuario_nombres']) ?>
                    </strong>
                </span>


                <?php if (tieneRol('Cliente')): ?>

                    <!-- MENÚ CLIENTE -->

                    <a href="<?= SITE_URL ?>/pages/usuario/dashboard.php">
                        Mi cuenta
                    </a>

                    <a href="<?= SITE_URL ?>/pages/usuario/mascotas.php">
                        Mis mascotas
                    </a>

                    <a href="<?= SITE_URL ?>/pages/usuario/reservas.php">
                        Mis reservas
                    </a>


                <?php elseif (tieneRol('Profesional')): ?>

                    <!-- MENÚ PROFESIONAL -->

                    <a href="<?= SITE_URL ?>/pages/profesional/dashboard.php">
                        Panel profesional
                    </a>

                    <a href="<?= SITE_URL ?>/pages/profesional/agenda.php">
                        Mi agenda
                    </a>

                    <a href="<?= SITE_URL ?>/pages/profesional/reservas.php">
                        Mis reservas
                    </a>

                    <a href="<?= SITE_URL ?>/pages/profesional/perfil.php">
                        Mi perfil
                    </a>


                <?php elseif (tieneRol('Administrador')): ?>

                    <!-- MENÚ ADMINISTRADOR -->

                    <a href="<?= SITE_URL ?>/pages/admin/dashboard.php">
                        Administración
                    </a>

                    <a href="<?= SITE_URL ?>/pages/admin/usuarios.php">
                        Usuarios
                    </a>

                    <a href="<?= SITE_URL ?>/pages/admin/profesionales.php">
                        Profesionales
                    </a>

                    <a href="<?= SITE_URL ?>/pages/admin/servicios.php">
                        Servicios
                    </a>

                    <a href="<?= SITE_URL ?>/pages/admin/reservas.php">
                        Reservas
                    </a>

                    <a href="<?= SITE_URL ?>/pages/admin/reportes.php">
                        Reportes
                    </a>

                <?php endif; ?>


                <!-- CERRAR SESIÓN -->

                <a class="login-link" href="<?= SITE_URL ?>/logout.php">
                    Cerrar sesión
                </a>


            <?php else: ?>

                <!-- USUARIO NO AUTENTICADO -->

                <a class="login-link" href="<?= SITE_URL ?>/login.php">
                    Iniciar sesión
                </a>

                <a class="btn btn-primary btn-small" href="<?= SITE_URL ?>/registro.php">
                    Registrarse
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>