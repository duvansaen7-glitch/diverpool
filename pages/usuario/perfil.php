<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../models/Usuario.php';

if (!usuarioAutenticado() || !tieneRol('Cliente')) {
    redirect(SITE_URL . '/login.php');
}

$usuarioModel = new Usuario();

$usuario = $usuarioModel->buscarPorId(usuarioId());

if ($usuario === null) {
    cerrarSesionUsuario();
    redirect(SITE_URL . '/login.php');
}

$pageTitle = 'Mi perfil | ' . SITE_NAME;
$pageDescription = 'Información personal de su cuenta en Diverpool Mascotas.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

    <section class="section">

        <div class="container">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        MI CUENTA
                    </span>

                    <h1>
                        Mi perfil
                    </h1>

                    <p>
                        Consulte la información asociada a su cuenta.
                    </p>

                </div>

            </div>


            <div class="profile-card">

                <div class="profile-header">

                    <div class="profile-avatar">

                        <?php if (!empty($usuario['foto_perfil'])): ?>

                            <img src="<?= SITE_URL . '/' . e($usuario['foto_perfil']) ?>"
                                alt="Foto de perfil de <?= e($usuario['nombres']) ?>">

                        <?php else: ?>

                            <?= e(strtoupper(substr($usuario['nombres'], 0, 1))) ?>

                        <?php endif; ?>

                    </div>

                    <div>

                        <h2>
                            <?= e($usuario['nombres'] . ' ' . $usuario['apellidos']) ?>
                        </h2>

                        <p>
                            Cliente de Diverpool Mascotas
                        </p>

                    </div>

                </div>


                <div class="profile-grid">


                    <div class="profile-field">

                        <span class="profile-label">
                            Nombres
                        </span>

                        <strong>
                            <?= e($usuario['nombres']) ?>
                        </strong>

                    </div>


                    <div class="profile-field">

                        <span class="profile-label">
                            Apellidos
                        </span>

                        <strong>
                            <?= e($usuario['apellidos']) ?>
                        </strong>

                    </div>


                    <div class="profile-field">

                        <span class="profile-label">
                            Correo electrónico
                        </span>

                        <strong>
                            <?= e($usuario['correo']) ?>
                        </strong>

                    </div>


                    <div class="profile-field">

                        <span class="profile-label">
                            Teléfono
                        </span>

                        <strong>
                            <?= $usuario['telefono']
                                ? e($usuario['telefono'])
                                : 'No registrado' ?>
                        </strong>

                    </div>


                    <div class="profile-field">

                        <span class="profile-label">
                            Documento
                        </span>

                        <strong>
                            <?= $usuario['documento']
                                ? e($usuario['documento'])
                                : 'No registrado' ?>
                        </strong>

                    </div>


                    <div class="profile-field">

                        <span class="profile-label">
                            Estado de la cuenta
                        </span>

                        <strong class="profile-status">
                            <?= e(ucfirst($usuario['estado'])) ?>
                        </strong>

                    </div>


                </div>


                <div class="profile-actions">

                    <a href="<?= SITE_URL ?>/pages/usuario/dashboard.php" class="btn btn-outline">
                        Volver a mi cuenta
                    </a>

                    <a href="<?= SITE_URL ?>/pages/usuario/perfil_editar.php" class="btn btn-primary">
                        Editar perfil
                    </a>

                </div>

            </div>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>