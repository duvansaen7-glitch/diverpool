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

$errores = [];

$nombres = $usuario['nombres'];
$apellidos = $usuario['apellidos'];
$telefono = $usuario['telefono'] ?? '';
$documento = $usuario['documento'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres = trim($_POST['nombres'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $documento = trim($_POST['documento'] ?? '');

    /*
     * Validar nombres
     */

    if ($nombres === '') {
        $errores[] = 'Los nombres son obligatorios.';
    } elseif (mb_strlen($nombres) < 2) {
        $errores[] = 'Los nombres deben tener al menos 2 caracteres.';
    } elseif (mb_strlen($nombres) > 100) {
        $errores[] = 'Los nombres no pueden superar los 100 caracteres.';
    }

    /*
     * Validar apellidos
     */

    if ($apellidos === '') {
        $errores[] = 'Los apellidos son obligatorios.';
    } elseif (mb_strlen($apellidos) < 2) {
        $errores[] = 'Los apellidos deben tener al menos 2 caracteres.';
    } elseif (mb_strlen($apellidos) > 100) {
        $errores[] = 'Los apellidos no pueden superar los 100 caracteres.';
    }

    /*
     * Validar teléfono
     */

    if ($telefono !== '' && mb_strlen($telefono) > 30) {
        $errores[] = 'El teléfono no puede superar los 30 caracteres.';
    }

    /*
     * Validar documento
     */

    if ($documento !== '' && mb_strlen($documento) > 30) {
        $errores[] = 'El documento no puede superar los 30 caracteres.';
    }

    /*
     * Preparar nueva fotografía
     */

    $nuevaFoto = null;
    $eliminarFotoAnterior = false;

    if (
        isset($_FILES['foto_perfil']) &&
        $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $archivo = $_FILES['foto_perfil'];

        /*
         * Validar error de subida
         */

        if ($archivo['error'] !== UPLOAD_ERR_OK) {

            $errores[] =
                'No fue posible subir la fotografía.';

        } elseif ($archivo['size'] > 2 * 1024 * 1024) {

            $errores[] =
                'La fotografía no puede superar los 2 MB.';

        } else {

            /*
             * Detectar MIME real de la imagen.
             */

            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $mime = $finfo->file($archivo['tmp_name']);

            $tiposPermitidos = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($tiposPermitidos[$mime])) {

                $errores[] =
                    'La fotografía debe ser JPG, PNG o WEBP.';

            } else {

                /*
                 * Crear nombre único.
                 */

                $extension = $tiposPermitidos[$mime];

                $nombreArchivo =
                    'usuario_' .
                    usuarioId() .
                    '_' .
                    bin2hex(random_bytes(8)) .
                    '.' .
                    $extension;

                $directorio =
                    __DIR__ . '/../../uploads/perfiles/';

                $rutaFisica =
                    $directorio . $nombreArchivo;

                if (!is_dir($directorio)) {

                    mkdir(
                        $directorio,
                        0755,
                        true
                    );
                }

                if (
                    !move_uploaded_file(
                        $archivo['tmp_name'],
                        $rutaFisica
                    )
                ) {

                    $errores[] =
                        'No fue posible guardar la fotografía.';

                } else {

                    $nuevaFoto =
                        'uploads/perfiles/' . $nombreArchivo;

                    $eliminarFotoAnterior = true;
                }
            }
        }
    }

    /*
     * Guardar información.
     */

    if (empty($errores)) {

        try {

            $usuarioModel->actualizarPerfil(
                usuarioId(),
                $nombres,
                $apellidos,
                $telefono !== '' ? $telefono : null,
                $documento !== '' ? $documento : null
            );

            /*
             * Guardar nueva foto si existe.
             */

            if ($nuevaFoto !== null) {

                $fotoAnterior =
                    $usuario['foto_perfil'] ?? null;

                $usuarioModel->actualizarFotoPerfil(
                    usuarioId(),
                    $nuevaFoto
                );

                /*
                 * Eliminar fotografía anterior.
                 */

                if (
                    $eliminarFotoAnterior &&
                    $fotoAnterior
                ) {

                    $rutaAnterior =
                        __DIR__ .
                        '/../../' .
                        ltrim($fotoAnterior, '/');

                    if (
                        is_file($rutaAnterior) &&
                        realpath($rutaAnterior) !== false
                    ) {

                        unlink($rutaAnterior);
                    }
                }
            }

            /*
             * Volver al perfil.
             */

            redirect(
                SITE_URL .
                '/pages/usuario/perfil.php?actualizado=1'
            );

        } catch (Throwable $e) {

            /*
             * Si la foto nueva se guardó pero falló
             * la actualización de la base de datos,
             * eliminar el archivo recién creado.
             */

            if ($nuevaFoto !== null) {

                $rutaNueva =
                    __DIR__ .
                    '/../../' .
                    ltrim($nuevaFoto, '/');

                if (is_file($rutaNueva)) {
                    unlink($rutaNueva);
                }
            }

            $errores[] =
                'No fue posible actualizar el perfil.';
        }
    }
}

$pageTitle = 'Editar perfil | ' . SITE_NAME;
$pageDescription =
    'Actualice su información personal en Diverpool Mascotas.';

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
                        Editar perfil
                    </h1>

                    <p>
                        Actualice sus datos personales y fotografía.
                    </p>

                </div>

            </div>


            <?php if (!empty($errores)): ?>

                <div class="form-errors" role="alert">

                    <?php foreach ($errores as $error): ?>

                        <p>
                            <?= e($error) ?>
                        </p>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <div class="profile-edit-card">


                <!-- FOTO -->

                <div class="profile-photo-section">

                    <div class="profile-photo-preview">

                        <?php if (!empty($usuario['foto_perfil'])): ?>

                            <img
                                src="<?= SITE_URL . '/' . e($usuario['foto_perfil']) ?>"
                                alt="Foto de perfil"
                            >

                        <?php else: ?>

                            <span>
                                <?= e(
                                    strtoupper(
                                        substr(
                                            $usuario['nombres'],
                                            0,
                                            1
                                        )
                                    )
                                ) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                    <div>

                        <h2>
                            Foto de perfil
                        </h2>

                        <p>
                            JPG, PNG o WEBP. Máximo 2 MB.
                        </p>

                        <label class="btn btn-outline photo-upload-button">

                            Seleccionar fotografía

                            <input
                                type="file"
                                name="foto_perfil"
                                form="perfilForm"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            >

                        </label>

                    </div>

                </div>


                <!-- FORMULARIO -->

                <form
                    id="perfilForm"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <div class="profile-form-grid">


                        <div class="form-group">

                            <label for="nombres">
                                Nombres
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
                                Apellidos
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

                            <label for="correo">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                id="correo"
                                value="<?= e($usuario['correo']) ?>"
                                disabled
                            >

                            <small>
                                El correo electrónico no puede modificarse desde aquí.
                            </small>

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


                    </div>


                    <div class="profile-edit-actions">

                        <a
                            href="<?= SITE_URL ?>/pages/usuario/perfil.php"
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
