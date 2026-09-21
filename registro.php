<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Usuario.php';
require_once __DIR__ . '/includes/funciones.php';

$errores = [];

$datos = [
    'nombres' => '',
    'apellidos' => '',
    'correo' => '',
    'telefono' => '',
    'documento' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datos['nombres'] = trim($_POST['nombres'] ?? '');
    $datos['apellidos'] = trim($_POST['apellidos'] ?? '');
    $datos['correo'] = strtolower(trim($_POST['correo'] ?? ''));
    $datos['telefono'] = trim($_POST['telefono'] ?? '');
    $datos['documento'] = trim($_POST['documento'] ?? '');

    $password = $_POST['password'] ?? '';
    $passwordConfirmacion = $_POST['password_confirmacion'] ?? '';

    /*
     * Validaciones básicas
     */
    if ($datos['nombres'] === '') {
        $errores[] = 'Ingrese sus nombres.';
    }

    if ($datos['apellidos'] === '') {
        $errores[] = 'Ingrese sus apellidos.';
    }

    if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingrese un correo electrónico válido.';
    }

    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener mínimo 8 caracteres.';
    }

    if ($password !== $passwordConfirmacion) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    /*
     * Comprobar si el correo ya existe
     */
    if (empty($errores)) {

        try {

            $usuarioModel = new Usuario();

            $usuarioExistente = $usuarioModel->buscarPorCorreo(
                $datos['correo']
            );

            if ($usuarioExistente !== null) {
                $errores[] = 'Ya existe una cuenta registrada con ese correo.';
            }

        } catch (Throwable $e) {

            $errores[] = 'No fue posible verificar el correo. Intente nuevamente.';
        }
    }

    /*
     * Crear usuario
     */
    if (empty($errores)) {

        try {

            $usuarioModel = new Usuario();

            /*
             * Obtener dinámicamente el rol Cliente.
             */
            $rolCliente = $usuarioModel->obtenerRolIdPorNombre('Cliente');

            if ($rolCliente === null) {

                $errores[] = 'No se encontró el rol Cliente en el sistema.';

            } else {

                /*
                 * Crear el usuario.
                 */
                $usuarioId = $usuarioModel->crear(
                    $rolCliente,
                    $datos['nombres'],
                    $datos['apellidos'],
                    $datos['correo'],
                    $password,
                    $datos['telefono'] !== ''
                        ? $datos['telefono']
                        : null,
                    $datos['documento'] !== ''
                        ? $datos['documento']
                        : null
                );

                /*
                 * Recuperar el usuario recién creado.
                 */
                $usuario = $usuarioModel->buscarPorId($usuarioId);

                if ($usuario === null) {

                    $errores[] =
                        'La cuenta fue creada, pero no pudo recuperarse.';

                } else {

                    /*
                     * Iniciar sesión automáticamente.
                     */
                    iniciarSesionUsuario($usuario);

                    redirect('index.php');
                }
            }

        } catch (PDOException $e) {

            /*
             * 23000 = violación de restricción SQL.
             * Puede ocurrir si el correo o documento ya existe.
             */
            if ($e->getCode() === '23000') {

                $errores[] =
                    'El correo o documento ya está registrado.';

            } else {

                $errores[] =
                    'No fue posible crear la cuenta. Intente nuevamente.';
            }

        } catch (Throwable $e) {

            $errores[] =
                'Ocurrió un error al crear la cuenta.';
        }
    }
}

$pageTitle = 'Crear cuenta | Diverpool Mascotas';

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
        href="public/css/style.css"
    >

</head>

<body class="auth-page">

    <main class="auth-card">

        <a
            class="brand auth-brand"
            href="index.php"
            aria-label="Diverpool Mascotas"
        >

            <span class="brand-mark">D</span>

            <span>

                <strong>Diverpool</strong>

                <small>MASCOTAS</small>

            </span>

        </a>

        <h1>Crear cuenta</h1>

        <p>
            Regístrese para poder agendar servicios,
            gestionar sus mascotas y consultar sus reservas.
        </p>

        <?php if (!empty($errores)): ?>

            <div
                class="form-errors"
                role="alert"
            >

                <?php foreach ($errores as $error): ?>

                    <p><?= e($error) ?></p>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <form
            method="POST"
            action="registro.php"
        >

            <label>

                Nombres

                <input
                    type="text"
                    name="nombres"
                    value="<?= e($datos['nombres']) ?>"
                    maxlength="100"
                    autocomplete="given-name"
                    required
                >

            </label>

            <label>

                Apellidos

                <input
                    type="text"
                    name="apellidos"
                    value="<?= e($datos['apellidos']) ?>"
                    maxlength="100"
                    autocomplete="family-name"
                    required
                >

            </label>

            <label>

                Correo electrónico

                <input
                    type="email"
                    name="correo"
                    value="<?= e($datos['correo']) ?>"
                    maxlength="150"
                    autocomplete="email"
                    required
                >

            </label>

            <label>

                Teléfono

                <input
                    type="tel"
                    name="telefono"
                    value="<?= e($datos['telefono']) ?>"
                    maxlength="30"
                    autocomplete="tel"
                >

            </label>

            <label>

                Documento

                <input
                    type="text"
                    name="documento"
                    value="<?= e($datos['documento']) ?>"
                    maxlength="30"
                    autocomplete="off"
                >

            </label>

            <label>

                Contraseña

                <input
                    type="password"
                    name="password"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

            </label>

            <label>

                Confirmar contraseña

                <input
                    type="password"
                    name="password_confirmacion"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

            </label>

            <button
                class="btn btn-primary"
                type="submit"
            >
                Crear cuenta
            </button>

        </form>

        <p class="auth-footer">

            ¿Ya tiene una cuenta?

            <a href="login.php">
                Iniciar sesión
            </a>

        </p>

        <p class="auth-footer">

            <a href="index.php">
                Volver al inicio
            </a>

        </p>

    </main>

</body>

</html>