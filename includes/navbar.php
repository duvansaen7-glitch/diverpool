<?php

require_once __DIR__ . '/funciones.php';

?>

<header class="site-header">

    <div class="container nav-wrap">

        <a
            class="brand"
            href="index.php"
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

            <a href="index.php">
                Inicio
            </a>

            <a href="index.php#servicios">
                Servicios
            </a>

            <a href="index.php#como-funciona">
                Cómo funciona
            </a>

            <a href="index.php#nosotros">
                Nosotros
            </a>

            <a href="index.php#contacto">
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

                    <a href="#">
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
                    href="logout.php"
                >
                    Cerrar sesión
                </a>

            <?php else: ?>

                <a
                    class="login-link"
                    href="login.php"
                >
                    Iniciar sesión
                </a>

                <a
                    class="btn btn-primary btn-small"
                    href="registro.php"
                >
                    Registrarse
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>