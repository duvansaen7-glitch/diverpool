<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Usuario.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado() || !tieneRol('Administrador')) {
    redirect(SITE_URL . '/login.php');
    exit;
}

$usuarioModel = new Usuario();

$errores = [];

$nombres = '';
$apellidos = '';
$documento = '';
$telefono = '';
$correo = '';
$rolId = 2;
$estado = 'activo';

$roles = $usuarioModel->obtenerRoles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = strtolower(trim($_POST['correo'] ?? ''));
    $password = $_POST['password'] ?? '';
    $passwordConfirmacion = $_POST['password_confirmacion'] ?? '';
    $rolId = (int) ($_POST['rol_id'] ?? 0);
    $estado = $_POST['estado'] ?? 'activo';

    /*
     * VALIDACIONES
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
    } elseif ($usuarioModel->correoExiste($correo)) {
        $errores[] = 'Ya existe un usuario registrado con ese correo.';
    }

    if ($documento !== '' && $usuarioModel->documentoExiste($documento)) {
        $errores[] = 'Ya existe un usuario registrado con ese documento.';
    }

    if ($password === '') {
        $errores[] = 'La contraseña es obligatoria.';
    } elseif (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener mínimo 8 caracteres.';
    }

    if ($password !== $passwordConfirmacion) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    $rolesValidos = array_map(
        static fn($rol) => (int) $rol['id'],
        $roles
    );

    if (!in_array($rolId, $rolesValidos, true)) {
        $errores[] = 'Debe seleccionar un rol válido.';
    }

    $estadosValidos = [
        'activo',
        'inactivo',
        'bloqueado'
    ];

    if (!in_array($estado, $estadosValidos, true)) {
        $errores[] = 'Debe seleccionar un estado válido.';
    }

    /*
     * CREAR USUARIO
     */

    if (empty($errores)) {

        try {

            $usuarioId = $usuarioModel->crearAdmin(
                $rolId,
                $nombres,
                $apellidos,
                $correo,
                $password,
                $telefono !== '' ? $telefono : null,
                $documento !== '' ? $documento : null,
                $estado
            );

            /*
             * Si el nuevo usuario es Profesional,
             * también se crea su perfil profesional.
             */
            if ($rolId === 3) {

                require_once __DIR__ . '/../../config/database.php';

                global $pdo;

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
                    ':usuario_id' => $usuarioId
                ]);
            }

            redirect(
                SITE_URL .
                '/pages/admin/usuarios.php?creado=1'
            );

        } catch (PDOException $e) {

            $errores[] = 'No fue posible crear el usuario.';

        }
    }
}

$pageTitle = 'Crear usuario | Administración | Diverpool Mascotas';
$pageDescription = 'Crear una nueva cuenta de usuario.';

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
                        Crear usuario
                    </h1>

                    <p>
                        Registre una nueva cuenta para el sistema.
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
                    action=""
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

                        </div>


                        <div class="form-group">

                            <label for="password">
                                Contraseña *
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                minlength="8"
                                required
                            >

                            <small>
                                Mínimo 8 caracteres.
                            </small>

                        </div>


                        <div class="form-group">

                            <label for="password_confirmacion">
                                Confirmar contraseña *
                            </label>

                            <input
                                type="password"
                                id="password_confirmacion"
                                name="password_confirmacion"
                                minlength="8"
                                required
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

                        </div>

                    </div>


                    <div style="margin-top: 2rem; display:flex; gap:1rem;">

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
                            Crear usuario
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
