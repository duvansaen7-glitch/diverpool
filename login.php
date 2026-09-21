<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Usuario.php';
require_once __DIR__ . '/includes/funciones.php';

$errores = [];

$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = strtolower(trim($_POST['correo'] ?? ''));
    $password = $_POST['password'] ?? '';

    /*
     * Validaciones básicas
     */
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingrese un correo electrónico válido.';
    }

    if ($password === '') {
        $errores[] = 'Ingrese su contraseña.';
    }

    /*
     * Intentar iniciar sesión
     */
    if (empty($errores)) {

        try {

            $usuarioModel = new Usuario();

            /*
             * Buscar usuario por correo.
             */
            $usuario = $usuarioModel->buscarPorCorreo($correo);

            /*
             * No revelar si el correo existe o no.
             */
            if ($usuario === null) {

                $errores[] =
                    'El correo o la contraseña son incorrectos.';

            } elseif (
                !$usuarioModel->verificarPassword(
                    $password,
                    $usuario['password_hash']
                )
            ) {

                $errores[] =
                    'El correo o la contraseña son incorrectos.';

            } elseif ($usuario['estado'] !== 'activo') {

                $errores[] =
                    'Esta cuenta no está activa.';

            } else {

                /*
                 * Actualizar último acceso.
                 */
                $usuarioModel->actualizarUltimoAcceso(
                    (int) $usuario['id']
                );

                /*
                 * Crear sesión.
                 */
                iniciarSesionUsuario($usuario);

                /*
                 * Ir al inicio.
                 */
                if ($usuario['rol_nombre'] === 'Administrador') {
                    redirect(SITE_URL . '/pages/admin/dashboard.php');
                }

                if ($usuario['rol_nombre'] === 'Profesional') {
                    redirect(SITE_URL . '/pages/profesional/dashboard.php');
                }

                redirect(SITE_URL . '/pages/usuario/dashboard.php');
            }

        } catch (Throwable $e) {

            /*
             * No mostrar detalles internos del error.
             */
            $errores[] =
                'No fue posible iniciar sesión. Intente nuevamente.';
        }
    }
}

$pageTitle = 'Iniciar sesión | Diverpool Mascotas';

?>
<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($pageTitle) ?></title>

    <link rel="stylesheet" href="public/css/style.css">

</head>

<body class="auth-page">

    <main class="auth-card">

        <a class="brand auth-brand" href="index.php" aria-label="Diverpool Mascotas">

            <span class="brand-mark">D</span>

            <span>

                <strong>Diverpool</strong>

                <small>MASCOTAS</small>

            </span>

        </a>

        <h1>Bienvenido de nuevo</h1>

        <p>
            Ingrese a su cuenta para gestionar sus mascotas,
            reservas y servicios.
        </p>

        <?php if (!empty($errores)): ?>

            <div class="form-errors" role="alert">

                <?php foreach ($errores as $error): ?>

                    <p><?= e($error) ?></p>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <form method="POST" action="login.php">

            <label>

                Correo electrónico

                <input type="email" name="correo" value="<?= e($correo) ?>" maxlength="150" autocomplete="email"
                    required>

            </label>

            <label>

                Contraseña

                <input type="password" name="password" autocomplete="current-password" required>

            </label>

            <button class="btn btn-primary" type="submit">
                Iniciar sesión
            </button>

        </form>

        <p class="auth-footer">

            ¿No tiene una cuenta?

            <a href="registro.php">
                Regístrese
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