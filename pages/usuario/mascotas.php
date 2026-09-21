<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Mascota.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado()) {
    redirect(SITE_URL . '/login.php');
}

$usuarioId = usuarioId();

$mascotaModel = new Mascota();

$mascotas = $mascotaModel->obtenerPorUsuario($usuarioId);

$pageTitle = 'Mis mascotas | Diverpool Mascotas';
$pageDescription = 'Gestione las mascotas asociadas a su cuenta.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

    <section class="section">

        <div class="container">

            <!-- =====================================================
                 ENCABEZADO
                 ===================================================== -->

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        MI CUENTA
                    </span>

                    <h1>
                        Mis mascotas
                    </h1>

                    <p>
                        Administre la información de sus mascotas
                        y manténgala siempre actualizada.
                    </p>

                </div>

                <div>

                    <a href="<?= SITE_URL ?>/pages/usuario/mascota_crear.php" class="btn btn-primary">
                        + Agregar mascota
                    </a>

                </div>

            </div>


            <!-- =====================================================
                 SIN MASCOTAS
                 ===================================================== -->

            <?php if (!$mascotas): ?>

                <div class="about-card">

                    <div>

                        <span class="eyebrow">
                            TODAVÍA NO TIENE MASCOTAS
                        </span>

                        <h2>
                            Registre su primera mascota
                        </h2>

                        <p>
                            Agregue la información de su mascota para
                            poder utilizarla posteriormente al solicitar
                            nuestros servicios.
                        </p>

                        <a href="<?= SITE_URL ?>/pages/usuario/mascota_crear.php" class="btn btn-primary">
                            Registrar mascota
                        </a>

                    </div>

                    <div class="about-pets">
                        🐶
                        <span>♡</span>
                        🐱
                    </div>

                </div>


                <!-- =====================================================
                 LISTADO DE MASCOTAS
                 ===================================================== -->

            <?php else: ?>

                <div class="mascotas-grid">

                    <?php foreach ($mascotas as $mascota): ?>

                        <article class="mascota-card">


                            <!-- =================================================
                                 FOTO
                                 ================================================= -->

                            <div class="mascota-card-photo">

                                <?php if (!empty($mascota['foto'])): ?>

                                    <img src="<?= SITE_URL . '/' . e($mascota['foto']) ?>"
                                        alt="Foto de <?= e($mascota['nombre']) ?>">

                                <?php else: ?>

                                    <div class="mascota-card-photo-placeholder">

                                        <?= $mascota['sexo'] === 'hembra'
                                            ? '🐶'
                                            : '🐕'
                                            ?>

                                    </div>

                                <?php endif; ?>

                            </div>


                            <!-- =================================================
                                 INFORMACIÓN
                                 ================================================= -->

                            <div class="mascota-card-content">

                                <h3>
                                    <?= e($mascota['nombre']) ?>
                                </h3>


                                <p class="mascota-card-species">

                                    <?= e($mascota['especie_nombre']) ?>

                                    <?php if (!empty($mascota['raza_nombre'])): ?>

                                        ·
                                        <?= e($mascota['raza_nombre']) ?>

                                    <?php endif; ?>

                                </p>


                                <div class="mascota-card-data">

                                    <p>

                                        <strong>
                                            Sexo:
                                        </strong>

                                        <?= $mascota['sexo'] === 'macho'
                                            ? 'Macho'
                                            : 'Hembra'
                                            ?>

                                    </p>


                                    <?php if (!empty($mascota['peso'])): ?>

                                        <p>

                                            <strong>
                                                Peso:
                                            </strong>

                                            <?= e($mascota['peso']) ?> kg

                                        </p>

                                    <?php endif; ?>


                                    <?php if (!empty($mascota['color'])): ?>

                                        <p>

                                            <strong>
                                                Color:
                                            </strong>

                                            <?= e($mascota['color']) ?>

                                        </p>

                                    <?php endif; ?>


                                    <p>

                                        <strong>
                                            Esterilización:
                                        </strong>

                                        <?= (int) $mascota['esterilizado'] === 1
                                            ? 'Sí'
                                            : 'No'
                                            ?>

                                    </p>

                                </div>


                                <!-- =================================================
                                     BOTÓN VER INFORMACIÓN
                                     ================================================= -->

                                <a href="<?= SITE_URL ?>/pages/usuario/mascota_ver.php?id=<?= (int) $mascota['id'] ?>"
                                    class="mascota-info-btn">
                                    Ver información
                                </a>


                                <!-- =================================================
                                     EDITAR / ELIMINAR
                                     ================================================= -->

                                <div class="mascota-actions">

                                    <a href="<?= SITE_URL ?>/pages/usuario/mascota_editar.php?id=<?= (int) $mascota['id'] ?>"
                                        class="btn-edit">
                                        Editar
                                    </a>


                                    <form method="POST"
                                        action="<?= SITE_URL ?>/pages/usuario/mascota_eliminar.php?id=<?= (int) $mascota['id'] ?>"
                                        onsubmit="return confirm('¿Está seguro de que desea eliminar esta mascota?');">

                                        <button type="submit" class="btn btn-danger">
                                            Eliminar
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>