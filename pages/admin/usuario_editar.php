<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado() || !tieneRol('Administrador')) {
    redirect(SITE_URL . '/login.php');
    exit;
}

$usuarioModel = new Usuario();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    redirect(SITE_URL . '/pages/admin/usuarios.php');
    exit;
}

$usuario = $usuarioModel->buscarPorId($id);

if (!$usuario) {
    redirect(SITE_URL . '/pages/admin/usuarios.php?error=no_encontrado');
    exit;
}

$roles = $usuarioModel->obtenerRoles();

$errores = [];

$nombres = $usuario['nombres'];
$apellidos = $usuario['apellidos'];
$documento = $usuario['documento'] ?? '';
$telefono = $usuario['telefono'] ?? '';
$correo = $usuario['correo'];
$rolId = (int) $usuario['rol_id'];
$estado = $usuario['estado'];

/*
 * ID del administrador actualmente conectado.
 */
$adminActualId = (int) usuarioId();

/*
 * Procesar actualización.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = strtolower(trim($_POST['correo'] ?? ''));
    $password = $_POST['password'] ?? '';
    $passwordConfirmacion = $_POST['password_confirmacion'] ?? '';
    $rolId = (int) ($_POST['rol_id'] ?? 0);
    $estado = $_POST['estado'] ?? '';

    /*
     * Validaciones básicas.
     */

    if ($nombres === '') {
        $errores[] = 'Los nombres son obligatorios.';
    }

    if ($apellidos === '') {
        $errores[] = 'Los apellidos son obligatorios.';
    }

    if ($correo === '') {
        $errores[] = 'El correo electrónico es obligatorio.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no tiene un formato válido.';
    } elseif ($usuarioModel->correoExiste($correo, $id)) {
        $errores[] = 'Ya existe otro usuario con ese correo.';
    }

    if (
        $documento !== '' &&
        $usuarioModel->documentoExiste($documento, $id)
    ) {
        $errores[] = 'Ya existe otro usuario con ese documento.';
    }

    /*
     * Validar rol.
     */
    $rolesValidos = array_map(
        static fn($rol) => (int) $rol['id'],
        $roles
    );

    if (!in_array($rolId, $rolesValidos, true)) {
        $errores[] = 'Debe seleccionar un rol válido.';
    }

    /*
     * Validar estado.
     */
    $estadosValidos = [
        'activo',
        'inactivo',
        'bloqueado'
    ];

    if (!in_array($estado, $estadosValidos, true)) {
        $errores[] = 'Debe seleccionar un estado válido.';
    }

    /*
     * La contraseña es opcional al editar.
     */
    if ($password !== '') {

        if (strlen($password) < 8) {
            $errores[] = 'La nueva contraseña debe tener mínimo 8 caracteres.';
        }

        if ($password !== $passwordConfirmacion) {
            $errores[] = 'Las contraseñas no coinciden.';
        }
    }

    /*
     * Protección:
     * el administrador actual no puede quitarse
     * su propio rol de Administrador.
     */
    if ($id === $adminActualId && $rolId !== 1) {
        $errores[] =
            'No puede cambiar su propia cuenta de Administrador a otro rol.';
    }

    /*
     * Protección:
     * el administrador actual no puede desactivar
     * o bloquear su propia cuenta.
     */
    if (
        $id === $adminActualId &&
        $estado !== 'activo'
    ) {
        $errores[] =
            'No puede desactivar o bloquear su propia cuenta.';
    }

    /*
     * Actualizar.
     */
    if (empty($errores)) {

        try {

            $usuarioModel->actualizarAdmin(
                $id,
                $rolId,
                $nombres,
                $apellidos,
                $correo,
                $telefono !== '' ? $telefono : null,
                $documento !== '' ? $documento : null,
                $estado,
                $password !== '' ? $password : null
            );

            /*
             * Si pasa a Profesional, crear su perfil
             * si todavía no existe.
             */
            if ($rolId === 3) {

                require_once __DIR__ . '/../../config/database.php';

                global $pdo;

                $stmt = $pdo->prepare("
                    SELECT id
                    FROM profesionales
                    WHERE usuario_id = :usuario_id
                    LIMIT 1
                ");

                $stmt->execute([
                    ':usuario_id' => $id
                ]);

                $profesionalExiste = $stmt->fetchColumn();

                if (!$profesionalExiste) {

                    $stmt = $pdo->prepare("
                        INSERT INTO profesionales (
                            usuario_id,
                            estado
                        )
                        VALUES (
                            :usuario_id,
                            'activo'
                        )
                    ");

                    $stmt->execute([
                        ':usuario_id' => $id
                    ]);
                }
            }

            /*
             * Si deja de ser Profesional,
             * no eliminamos su historial.
             *
             * Solamente dejamos su perfil profesional
             * como inactivo.
             */
            if ($rolId !== 3) {

                require_once __DIR__ . '/../../config/database.php';

                global $pdo;

                $stmt = $pdo->prepare("
                    UPDATE profesionales
                    SET estado = 'inactivo'
                    WHERE usuario_id = :usuario_id
                ");

                $stmt->execute([
                    ':usuario_id' => $id
                ]);
            }

            redirect(
                SITE_URL .
                '/pages/admin/usuarios.php?actualizado=1'
            );

        } catch (PDOException $e) {

            $errores[] =
                'No fue posible actualizar el usuario.';
        }
    }
}

$pageTitle = 'Editar usuario | Administración | Diverpool Mascotas';
$pageDescription = 'Editar una cuenta de usuario.';

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
                        Editar usuario
                    </h1>

                    <p>
                        Modifique los datos y permisos de la cuenta.
                    </p>

                </div>

                <div>

                    <a
                        href="<?= SITE_URL ?>/pages/admin/usuarios.php"
                        class="btn btn-outline"
                    >
                        ← Volver
                    </a>

                </div>

            </div>


            <?php if (!empty($errores)): ?>

                <div class="alert alert-error">

                    <?php foreach ($errores as $error): ?>

                        <div>
                            <?= e($error) ?>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <div class="about-card">

                <form
                    method="POST"
                    action="<?= SITE_URL ?>/pages/admin/usuario_editar.php?id=<?= $id ?>"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $id ?>"
                    >

                    <div class="form-grid">

                        <div class="form-group">

                            <label for="nombres">
                                Nombres *
                            </label>

                            <input
                                type="text"
                                id="nombres"
                                name="nombres"
                                value="<?= e($nombres) ?>"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="apellidos">
                                Apellidos *
                            </label>

                            <input
                                type="text"
                                id="apellidos"
                                name="apellidos"
                                value="<?= e($apellidos) ?>"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="documento">
                                Documento
                            </label>

                            <input
                                type="text"
                                id="documento"
                                name="documento"
                                value="<?= e($documento) ?>"
                                maxlength="30"
                            >

                        </div>


                        <div class="form-group">

                            <label for="telefono">
                                Teléfono
                            </label>

                            <input
                                type="text"
                                id="telefono"
                                name="telefono"
                                value="<?= e($telefono) ?>"
                                maxlength="30"
                            >

                        </div>


                        <div class="form-group">

                            <label for="correo">
                                Correo electrónico *
                            </label>

                            <input
                                type="email"
                                id="correo"
                                name="correo"
                                value="<?= e($correo) ?>"
                                maxlength="150"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="rol_id">
                                Rol *
                            </label>

                            <select
                                id="rol_id"
                                name="rol_id"
                                required
                            >

                                <?php foreach ($roles as $rol): ?>

                                    <option
                                        value="<?= (int) $rol['id'] ?>"
                                        <?= $rolId === (int) $rol['id'] ? 'selected' : '' ?>
                                    >
                                        <?= e($rol['nombre']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <?php if ($id === $adminActualId): ?>

                                <small>
                                    Su propia cuenta debe conservar el rol Administrador.
                                </small>

                            <?php endif; ?>

                        </div>


                        <div class="form-group">

                            <label for="password">
                                Nueva contraseña
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                minlength="8"
                            >

                            <small>
                                Déjelo vacío para conservar la contraseña actual.
                            </small>

                        </div>


                        <div class="form-group">

                            <label for="password_confirmacion">
                                Confirmar nueva contraseña
                            </label>

                            <input
                                type="password"
                                id="password_confirmacion"
                                name="password_confirmacion"
                                minlength="8"
                            >

                        </div>


                        <div class="form-group">

                            <label for="estado">
                                Estado *
                            </label>

                            <select
                                id="estado"
                                name="estado"
                                required
                            >

                                <option
                                    value="activo"
                                    <?= $estado === 'activo' ? 'selected' : '' ?>
                                >
                                    Activo
                                </option>

                                <option
                                    value="inactivo"
                                    <?= $estado === 'inactivo' ? 'selected' : '' ?>
                                >
                                    Inactivo
                                </option>

                                <option
                                    value="bloqueado"
                                    <?= $estado === 'bloqueado' ? 'selected' : '' ?>
                                >
                                    Bloqueado
                                </option>

                            </select>

                            <?php if ($id === $adminActualId): ?>

                                <small>
                                    Su propia cuenta debe permanecer activa.
                                </small>

                            <?php endif; ?>

                        </div>

                    </div>


                    <div
                        style="
                            margin-top: 2rem;
                            display:flex;
                            gap:1rem;
                        "
                    >

                        <a
                            href="<?= SITE_URL ?>/pages/admin/usuarios.php"
                            class="btn btn-outline"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Guardar cambios
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
