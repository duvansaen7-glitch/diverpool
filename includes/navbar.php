<?php

require_once __DIR__ . '/funciones.php';

?>

<header class="site-header">

    <div class="container nav-wrap">

        <a
            class="brand"
            href="<?= SITE_URL ?>/index.php"
            aria-label="Diverpool Mascotas"
        >

            <span class="brand-mark">D</span>

            <span>
                <strong>Diverpool</strong>
                <small>MASCOTAS</small>
            </span>

        </a>

        <button
            class="menu-toggle"
            id="menuToggle"
            aria-label="Abrir menú"
            type="button"
        >
            ☰
        </button>

        <nav id="mainNav">

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

                <span class="nav-user">
                    Hola,
                    <strong>
                        <?= e($_SESSION['usuario_nombres']) ?>
                    </strong>
                </span>

                <?php if (tieneRol('Cliente')): ?>

                    <a href="<?= SITE_URL ?>/pages/usuario/mascotas.php">
                        Mis mascotas
                    </a>

                    <a href="#">
                        Mis reservas
                    </a>

                <?php elseif (tieneRol('Profesional')): ?>

                    <a href="#">
                        Panel profesional
                    </a>

                <?php elseif (tieneRol('Administrador')): ?>

                    <a href="#">
                        Panel administrativo
                    </a>

                <?php endif; ?>

                <a
                    class="login-link"
                    href="<?= SITE_URL ?>/logout.php"
                >
                    Cerrar sesión
                </a>

            <?php else: ?>

                <a
                    class="login-link"
                    href="<?= SITE_URL ?>/login.php"
                >
                    Iniciar sesión
                </a>

                <a
                    class="btn btn-primary btn-small"
                    href="<?= SITE_URL ?>/registro.php"
                >
                    Registrarse
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>