<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Mascota.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado()) {
    redirect(SITE_URL . '/login.php');
}

$usuarioId = usuarioId();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(SITE_URL . '/pages/usuario/mascotas.php');
}

$mascotaId = (int) $_GET['id'];

$mascotaModel = new Mascota();

$mascota = $mascotaModel->obtenerPorId(
    $mascotaId,
    $usuarioId
);

if (!$mascota) {
    redirect(SITE_URL . '/pages/usuario/mascotas.php');
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main class="container">

    <section class="page-header">

        <h1><?= e($mascota['nombre']) ?></h1>

        <p>
            Información completa de su mascota.
        </p>

    </section>

    <section class="mascota-detail">

        <div class="mascota-detail-photo">

            <?php if (!empty($mascota['foto'])): ?>

                <img src="<?= SITE_URL . '/' . e($mascota['foto']) ?>" alt="Foto de <?= e($mascota['nombre']) ?>">

            <?php else: ?>

                <div class="mascota-detail-placeholder">
                    🐾
                </div>

            <?php endif; ?>

        </div>

        <div class="mascota-detail-info">

            <h2>
                <?= e($mascota['nombre']) ?>
            </h2>

            <div class="mascota-data">

                <div>
                    <strong>Especie</strong>
                    <span>
                        <?= e($mascota['especie_nombre'] ?? 'No registrada') ?>
                    </span>
                </div>

                <div>
                    <strong>Raza</strong>
                    <span>
                        <?= e($mascota['raza_nombre'] ?? 'No registrada') ?>
                    </span>
                </div>

                <div>
                    <strong>Sexo</strong>
                    <span>
                        <?= e(ucfirst($mascota['sexo'])) ?>
                    </span>
                </div>

                <div>
                    <strong>Fecha de nacimiento</strong>
                    <span>
                        <?= !empty($mascota['fecha_nacimiento'])
                            ? e($mascota['fecha_nacimiento'])
                            : 'No registrada'
                            ?>
                    </span>
                </div>

                <div>
                    <strong>Peso</strong>
                    <span>
                        <?= $mascota['peso'] !== null
                            ? e($mascota['peso']) . ' kg'
                            : 'No registrado'
                            ?>
                    </span>
                </div>

                <div>
                    <strong>Color</strong>
                    <span>
                        <?= !empty($mascota['color'])
                            ? e($mascota['color'])
                            : 'No registrado'
                            ?>
                    </span>
                </div>

                <div>
                    <strong>Microchip</strong>
                    <span>
                        <?= !empty($mascota['microchip'])
                            ? e($mascota['microchip'])
                            : 'No registrado'
                            ?>
                    </span>
                </div>

                <div>
                    <strong>Esterilizado</strong>
                    <span>
                        <?= !empty($mascota['esterilizado'])
                            ? 'Sí'
                            : 'No'
                            ?>
                    </span>
                </div>

            </div>

            <div class="mascota-observaciones">

                <strong>Observaciones</strong>

                <p>
                    <?= !empty($mascota['observaciones'])
                        ? nl2br(e($mascota['observaciones']))
                        : 'No hay observaciones registradas.'
                        ?>
                </p>

            </div>

            <div class="mascota-detail-actions">

                <a href="<?= SITE_URL ?>/pages/usuario/mascota_editar.php?id=<?= (int) $mascota['id'] ?>"
                    class="btn btn-primary">
                    Editar mascota
                </a>

                <a href="<?= SITE_URL ?>/pages/usuario/mascotas.php" class="btn btn-outline">
                    Volver a mis mascotas
                </a>

            </div>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>