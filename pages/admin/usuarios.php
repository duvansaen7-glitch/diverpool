<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado() || !tieneRol('Administrador')) {
    redirect(SITE_URL . '/login.php');
}

$usuarioModel = new Usuario();

$usuarios = $usuarioModel->obtenerTodos();

$pageTitle = 'Usuarios | Administración | Diverpool Mascotas';
$pageDescription = 'Gestión de usuarios de Diverpool Mascotas.';

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
                        Usuarios
                    </h1>

                    <p>
                        Gestione las cuentas de clientes,
                        profesionales y administradores.
                    </p>

                </div>

                <div>

                    <a href="<?= SITE_URL ?>/pages/admin/usuario_crear.php" class="btn btn-primary">
                        + Crear usuario
                    </a>

                </div>

            </div>

            <?php if (empty($usuarios)): ?>

                <div class="about-card">

                    <div>

                        <span class="eyebrow">
                            SIN USUARIOS
                        </span>

                        <h2>
                            No hay usuarios registrados
                        </h2>

                        <p>
                            Cree el primer usuario desde el botón
                            de administración.
                        </p>

                        <a href="<?= SITE_URL ?>/pages/admin/usuario_crear.php" class="btn btn-primary">
                            Crear usuario
                        </a>

                    </div>

                </div>

            <?php else: ?>

                <div class="admin-table-card">

                    <div class="admin-table-header">

                        <div>

                            <span class="eyebrow">
                                CUENTAS DEL SISTEMA
                            </span>

                            <h2>
                                Usuarios registrados
                            </h2>

                        </div>

                        <span class="admin-table-count">
                            <?= count($usuarios) ?>
                            usuario<?= count($usuarios) !== 1 ? 's' : '' ?>
                        </span>

                    </div>

                    <div class="admin-table-wrapper">

                        <table class="admin-table">

                            <thead>

                                <tr>

                                    <th>
                                        Usuario
                                    </th>

                                    <th>
                                        Correo
                                    </th>

                                    <th>
                                        Rol
                                    </th>

                                    <th>
                                        Estado
                                    </th>

                                    <th>
                                        Último acceso
                                    </th>

                                    <th>
                                        Acciones
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($usuarios as $usuario): ?>

                                    <tr>

                                        <td>

                                            <div class="admin-user-cell">

                                                <div class="admin-user-avatar">

                                                    <?= e(
                                                        strtoupper(
                                                            substr(
                                                                $usuario['nombres'],
                                                                0,
                                                                1
                                                            )
                                                        )
                                                    ) ?>

                                                </div>

                                                <div>

                                                    <strong>
                                                        <?= e($usuario['nombres']) ?>
                                                        <?= e($usuario['apellidos']) ?>
                                                    </strong>

                                                    <small>
                                                        ID #<?= (int) $usuario['id'] ?>
                                                    </small>

                                                </div>

                                            </div>

                                        </td>

                                        <td>
                                            <?= e($usuario['correo']) ?>
                                        </td>

                                        <td>

                                            <span class="admin-role-badge">

                                                <?= e($usuario['rol_nombre']) ?>

                                            </span>

                                        </td>

                                        <td>

                                            <span class="admin-status-badge admin-status-<?= e($usuario['estado']) ?>">
                                                <?= e(ucfirst($usuario['estado'])) ?>
                                            </span>

                                        </td>

                                        <td>

                                            <?php if (!empty($usuario['ultimo_acceso'])): ?>

                                                <?= e($usuario['ultimo_acceso']) ?>

                                            <?php else: ?>

                                                <span class="admin-muted">
                                                    Nunca

                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <a href="<?= SITE_URL ?>/pages/admin/usuario_editar.php?id=<?= (int) $usuario['id'] ?>"
                                                class="btn btn-small btn-outline">
                                                Editar
                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>