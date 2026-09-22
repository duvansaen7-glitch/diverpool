<?php

require_once __DIR__ . '/funciones.php';

?>

<header class="site-header">

    <div class="container nav-wrap">

        <!-- ==========================================
             LOGO
        =========================================== -->

        <a
            class="brand"
            href="<?= SITE_URL ?>/index.php"
            aria-label="Diverpool Mascotas"
        >

            <span class="brand-mark">
                D
            </span>

            <span>
                <strong>Diverpool</strong>
                <small>MASCOTAS</small>
            </span>

        </a>


        <!-- ==========================================
             BOTÓN MENÚ MÓVIL
        =========================================== -->

        <button
            class="menu-toggle"
            id="menuToggle"
            aria-label="Abrir menú"
            type="button"
        >
            ☰
        </button>


        <!-- ==========================================
             NAVEGACIÓN
        =========================================== -->

        <nav id="mainNav">

            <!-- ======================================
                 ENLACES PÚBLICOS
            ======================================= -->

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


                <!-- ==================================
                     USUARIO AUTENTICADO
                =================================== -->

                <span class="nav-user">

                    Hola,

                    <strong>
                        <?= e($_SESSION['usuario_nombres']) ?>
                    </strong>

                </span>


                <?php if (tieneRol('Cliente')): ?>


                    <!-- ==============================
                         MENÚ CLIENTE
                    =============================== -->

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


                    <!-- ==============================
                         MENÚ PROFESIONAL
                    =============================== -->

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


                    <!-- ==============================
                         MENÚ ADMINISTRADOR
                    =============================== -->

                    <div class="nav-dropdown">

                        <button
                            type="button"
                            class="nav-dropdown-toggle"
                            aria-expanded="false"
                        >

                            Administración

                            <span class="nav-dropdown-arrow">
                                ▾
                            </span>

                        </button>


                        <div class="nav-dropdown-menu">

                            <a
                                href="<?= SITE_URL ?>/pages/admin/dashboard.php"
                            >
                                <span class="dropdown-icon">
                                    ▦
                                </span>

                                <span>
                                    <strong>
                                        Panel administrativo
                                    </strong>

                                    <small>
                                        Vista general
                                    </small>
                                </span>
                            </a>


                            <a
                                href="<?= SITE_URL ?>/pages/admin/usuarios.php"
                            >
                                <span class="dropdown-icon">
                                    ◉
                                </span>

                                <span>
                                    <strong>
                                        Usuarios
                                    </strong>

                                    <small>
                                        Gestionar usuarios
                                    </small>
                                </span>
                            </a>


                            <a
                                href="<?= SITE_URL ?>/pages/admin/profesionales.php"
                            >
                                <span class="dropdown-icon">
                                    ♟
                                </span>

                                <span>
                                    <strong>
                                        Profesionales
                                    </strong>

                                    <small>
                                        Gestionar profesionales
                                    </small>
                                </span>
                            </a>


                            <a
                                href="<?= SITE_URL ?>/pages/admin/servicios.php"
                            >
                                <span class="dropdown-icon">
                                    ✦
                                </span>

                                <span>
                                    <strong>
                                        Servicios
                                    </strong>

                                    <small>
                                        Gestionar servicios
                                    </small>
                                </span>
                            </a>


                            <a
                                href="<?= SITE_URL ?>/pages/admin/reservas.php"
                            >
                                <span class="dropdown-icon">
                                    ◷
                                </span>

                                <span>
                                    <strong>
                                        Reservas
                                    </strong>

                                    <small>
                                        Gestionar reservas
                                    </small>
                                </span>
                            </a>


                            <a
                                href="<?= SITE_URL ?>/pages/admin/reportes.php"
                            >
                                <span class="dropdown-icon">
                                    ▥
                                </span>

                                <span>
                                    <strong>
                                        Reportes
                                    </strong>

                                    <small>
                                        Consultar reportes
                                    </small>
                                </span>
                            </a>

                        </div>

                    </div>


                <?php endif; ?>


                <!-- ==================================
                     CERRAR SESIÓN
                =================================== -->

                <a
                    class="login-link"
                    href="<?= SITE_URL ?>/logout.php"
                >
                    Cerrar sesión
                </a>


            <?php else: ?>


                <!-- ==================================
                     USUARIO NO AUTENTICADO
                =================================== -->

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