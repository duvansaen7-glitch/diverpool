<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Mascota.php';
require_once __DIR__ . '/../../includes/funciones.php';

/*
 * Esta página requiere que el usuario haya iniciado sesión.
 */
if (!usuarioAutenticado()) {
    redirect('../../login.php');
}

$usuarioId = usuarioId();

$mascotaModel = new Mascota();
$mascotas = $mascotaModel->obtenerPorUsuario($usuarioId);

$pageTitle = 'Mis mascotas | Diverpool Mascotas';

?>
<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title><?= e($pageTitle) ?></title>

    <link
        rel="stylesheet"
        href="../../public/css/style.css"
    >

</head>

<body>

    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <main>

        <section class="section">

            <div class="container">

                <div class="section-heading">

                    <span class="section-eyebrow">
                        Mi cuenta
                    </span>

                    <h1>
                        Mis mascotas
                    </h1>

                    <p>
                        Consulte y gestione las mascotas asociadas
                        a su cuenta.
                    </p>

                </div>

                <?php if (empty($mascotas)): ?>

                    <div class="auth-card">

                        <h2>
                            Aún no tiene mascotas registradas
                        </h2>

                        <p>
                            Cuando registre una mascota,
                            aparecerá aquí junto con su información.
                        </p>

                        <a
                            class="btn btn-primary"
                            href="mascota_crear.php"
                        >
                            Registrar mascota
                        </a>

                    </div>

                <?php else: ?>

                    <div class="services-grid">

                        <?php foreach ($mascotas as $mascota): ?>

                            <article class="service-card">

                                <div class="service-icon">
                                    <?= $mascota['especie_nombre'] === 'Gato'
                                        ? '🐱'
                                        : '🐶' ?>
                                </div>

                                <h2>
                                    <?= e($mascota['nombre']) ?>
                                </h2>

                                <p>
                                    <strong>Especie:</strong>
                                    <?= e($mascota['especie_nombre']) ?>
                                </p>

                                <?php if (!empty($mascota['raza_nombre'])): ?>

                                    <p>
                                        <strong>Raza:</strong>
                                        <?= e($mascota['raza_nombre']) ?>
                                    </p>

                                <?php endif; ?>

                                <p>
                                    <strong>Sexo:</strong>
                                    <?= e(ucfirst($mascota['sexo'])) ?>
                                </p>

                                <?php if (!empty($mascota['peso'])): ?>

                                    <p>
                                        <strong>Peso:</strong>
                                        <?= e($mascota['peso']) ?> kg
                                    </p>

                                <?php endif; ?>

                                <div class="service-actions">

                                    <a
                                        class="btn btn-secondary"
                                        href="mascota_editar.php?id=<?= (int) $mascota['id'] ?>"
                                    >
                                        Editar
                                    </a>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>

                    <div class="section-actions">

                        <a
                            class="btn btn-primary"
                            href="mascota_crear.php"
                        >
                            Registrar otra mascota
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </section>

    </main>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>

</body>

</html>