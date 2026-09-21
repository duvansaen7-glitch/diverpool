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

            <!-- Encabezado -->

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

                    <a
                        href="<?= SITE_URL ?>/pages/usuario/mascota_crear.php"
                        class="btn btn-primary"
                    >
                        + Agregar mascota
                    </a>

                </div>

            </div>


            <?php if (!$mascotas): ?>

                <!-- Estado vacío -->

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

                        <a
                            href="<?= SITE_URL ?>/pages/usuario/mascota_crear.php"
                            class="btn btn-primary"
                        >
                            Registrar mascota
                        </a>

                    </div>

                    <div class="about-pets">
                        🐶
                        <span>♡</span>
                        🐱
                    </div>

                </div>


            <?php else: ?>

                <!-- Lista de mascotas -->

                <div class="service-grid mascotas-grid">

                    <?php foreach ($mascotas as $mascota): ?>

                        <article class="service-card mascota-card">

                            <!-- Foto -->

                            <div class="mascota-photo">

                                <?php if (!empty($mascota['foto'])): ?>

                                    <img
                                        src="<?= SITE_URL . '/' . e($mascota['foto']) ?>"
                                        alt="Foto de <?= e($mascota['nombre']) ?>"
                                    >

                                <?php else: ?>

                                    <div class="mascota-photo-placeholder">
                                        <?= $mascota['sexo'] === 'hembra' ? '🐶' : '🐕' ?>
                                    </div>

                                <?php endif; ?>

                            </div>


                            <!-- Información -->

                            <div class="service-body">

                                <h3>
                                    <?= e($mascota['nombre']) ?>
                                </h3>

                                <p>
                                    <strong>
                                        <?= e($mascota['especie_nombre']) ?>
                                    </strong>

                                    <?php if (!empty($mascota['raza_nombre'])): ?>

                                        · <?= e($mascota['raza_nombre']) ?>

                                    <?php endif; ?>
                                </p>


                                <p>

                                    <?= $mascota['sexo'] === 'macho'
                                        ? 'Macho'
                                        : 'Hembra'
                                    ?>

                                    <?php if (!empty($mascota['peso'])): ?>

                                        · <?= e($mascota['peso']) ?> kg

                                    <?php endif; ?>

                                </p>


                                <?php if (!empty($mascota['color'])): ?>

                                    <p>
                                        Color:
                                        <?= e($mascota['color']) ?>
                                    </p>

                                <?php endif; ?>


                                <?php if ((int) $mascota['esterilizado'] === 1): ?>

                                    <p>
                                        Esterilizado
                                    </p>

                                <?php endif; ?>


                                <div class="hero-actions">

                                    <a
                                        href="<?= SITE_URL ?>/pages/usuario/mascota_editar.php?id=<?= (int) $mascota['id'] ?>"
                                        class="btn btn-outline"
                                    >
                                        Editar
                                    </a>

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