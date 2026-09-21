<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Mascota.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado()) {
    redirect(SITE_URL . '/login.php');
}

$usuarioId = usuarioId();

if (
    !isset($_GET['id']) ||
    !is_numeric($_GET['id'])
) {
    redirect(
        SITE_URL .
        '/pages/usuario/mascotas.php'
    );
}

$mascotaId = (int) $_GET['id'];

$mascotaModel = new Mascota($pdo);

$mascota =
    $mascotaModel->obtenerPorId(
        $mascotaId,
        $usuarioId
    );

if (!$mascota) {
    redirect(
        SITE_URL .
        '/pages/usuario/mascotas.php'
    );
}

$especies =
    $mascotaModel->obtenerEspecies();

$razas =
    $mascotaModel->obtenerRazasPorEspecie(
        (int) $mascota['especie_id']
    );

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre =
        trim($_POST['nombre'] ?? '');

    $especieId =
        (int) ($_POST['especie_id'] ?? 0);

    $razaId =
        !empty($_POST['raza_id'])
        ? (int) $_POST['raza_id']
        : null;

    $sexo =
        $_POST['sexo'] ?? '';

    $fechaNacimiento =
        !empty($_POST['fecha_nacimiento'])
        ? $_POST['fecha_nacimiento']
        : null;

    $peso =
        !empty($_POST['peso'])
        ? (float) $_POST['peso']
        : null;

    $color =
        trim($_POST['color'] ?? '');

    $microchip =
        trim($_POST['microchip'] ?? '');

    /*
     * Esterilizado:
     * 0 = no
     * 1 = sí
     */
    $esterilizado =
        (int) ($_POST['esterilizado'] ?? 0) === 1
        ? 1
        : 0;

    $observaciones =
        trim($_POST['observaciones'] ?? '');

    /*
     * Conservamos la fotografía actual
     * si no se hace ningún cambio.
     */
    $fotoRuta =
        $mascota['foto'];

    $eliminarFoto =
        isset($_POST['eliminar_foto']);

    if ($eliminarFoto) {
        $fotoRuta = null;
    }

    /*
     * =========================
     * VALIDACIONES
     * =========================
     */

    if ($nombre === '') {
        $errores[] =
            'El nombre de la mascota es obligatorio.';
    }

    if ($especieId <= 0) {
        $errores[] =
            'Debe seleccionar una especie.';
    }

    if (
        !in_array(
            $sexo,
            ['macho', 'hembra'],
            true
        )
    ) {
        $errores[] =
            'Debe seleccionar un sexo válido.';
    }

    if (
        $peso !== null &&
        $peso <= 0
    ) {
        $errores[] =
            'El peso debe ser mayor que cero.';
    }

    /*
     * =========================
     * NUEVA FOTOGRAFÍA
     * =========================
     */

    $nuevaFotoGuardada = null;

    if (
        isset($_FILES['foto']) &&
        $_FILES['foto']['error']
        !== UPLOAD_ERR_NO_FILE
    ) {

        if (
            $_FILES['foto']['error']
            !== UPLOAD_ERR_OK
        ) {

            $errores[] =
                'No se pudo subir la nueva fotografía.';

        } else {

            $archivo = $_FILES['foto'];

            if (
                $archivo['size'] >
                5 * 1024 * 1024
            ) {

                $errores[] =
                    'La fotografía no puede superar los 5 MB.';

            } else {

                $finfo =
                    new finfo(FILEINFO_MIME_TYPE);

                $mime =
                    $finfo->file(
                        $archivo['tmp_name']
                    );

                $tiposPermitidos = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                if (
                    !isset(
                    $tiposPermitidos[$mime]
                )
                ) {

                    $errores[] =
                        'La fotografía debe ser JPG, PNG o WEBP.';

                } else {

                    $directorio =
                        __DIR__ .
                        '/../../uploads/mascotas/';

                    if (!is_dir($directorio)) {

                        mkdir(
                            $directorio,
                            0775,
                            true
                        );
                    }

                    $nombreArchivo =
                        'mascota_' .
                        $mascotaId .
                        '_' .
                        bin2hex(random_bytes(8)) .
                        '.' .
                        $tiposPermitidos[$mime];

                    $rutaFisica =
                        $directorio .
                        $nombreArchivo;

                    if (
                        move_uploaded_file(
                            $archivo['tmp_name'],
                            $rutaFisica
                        )
                    ) {

                        $nuevaFotoGuardada =
                            'uploads/mascotas/' .
                            $nombreArchivo;

                        $fotoRuta =
                            $nuevaFotoGuardada;

                    } else {

                        $errores[] =
                            'No se pudo guardar la nueva fotografía.';
                    }
                }
            }
        }
    }

    /*
     * =========================
     * GUARDAR CAMBIOS
     * =========================
     */

    if (empty($errores)) {

        $actualizado =
            $mascotaModel->actualizar(
                $mascotaId,
                $usuarioId,
                $especieId,
                $razaId,
                $nombre,
                $sexo,
                $fechaNacimiento,
                $peso,
                $color !== ''
                ? $color
                : null,
                $microchip !== ''
                ? $microchip
                : null,
                $esterilizado,
                $observaciones !== ''
                ? $observaciones
                : null,
                $fotoRuta
            );

        if ($actualizado) {

            /*
             * Eliminar foto anterior si
             * se reemplazó por una nueva.
             */
            if (
                $nuevaFotoGuardada !== null &&
                !empty($mascota['foto']) &&
                $mascota['foto']
                !== $nuevaFotoGuardada
            ) {

                $fotoAnterior =
                    __DIR__ .
                    '/../../' .
                    $mascota['foto'];

                if (
                    is_file($fotoAnterior)
                ) {
                    unlink($fotoAnterior);
                }
            }

            /*
             * Eliminar foto anterior si
             * el usuario pidió quitarla.
             */
            if (
                $eliminarFoto &&
                $nuevaFotoGuardada === null &&
                !empty($mascota['foto'])
            ) {

                $fotoAnterior =
                    __DIR__ .
                    '/../../' .
                    $mascota['foto'];

                if (
                    is_file($fotoAnterior)
                ) {
                    unlink($fotoAnterior);
                }
            }

            redirect(
                SITE_URL .
                '/pages/usuario/mascotas.php?actualizado=1'
            );
        }

        /*
         * Si falló la actualización después
         * de guardar una foto nueva,
         * eliminamos esa foto.
         */
        if (
            $nuevaFotoGuardada !== null
        ) {

            $fotoNuevaFisica =
                __DIR__ .
                '/../../' .
                $nuevaFotoGuardada;

            if (
                is_file($fotoNuevaFisica)
            ) {
                unlink($fotoNuevaFisica);
            }
        }

        $errores[] =
            'No se pudieron guardar los cambios.';
    }

    /*
     * Mantener datos escritos
     * si ocurrió algún error.
     */
    $mascota['nombre'] =
        $nombre;

    $mascota['especie_id'] =
        $especieId;

    $mascota['raza_id'] =
        $razaId;

    $mascota['sexo'] =
        $sexo;

    $mascota['fecha_nacimiento'] =
        $fechaNacimiento;

    $mascota['peso'] =
        $peso;

    $mascota['color'] =
        $color;

    $mascota['microchip'] =
        $microchip;

    $mascota['esterilizado'] =
        $esterilizado;

    $mascota['observaciones'] =
        $observaciones;

    $razas =
        $mascotaModel->obtenerRazasPorEspecie(
            $especieId
        );
}

$pageTitle =
    'Editar mascota | Diverpool Mascotas';

$pageDescription =
    'Actualice la información de su mascota.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

    <section class="section mascota-form-page">

        <div class="container">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        MIS MASCOTAS
                    </span>

                    <h1>
                        Editar mascota
                    </h1>

                    <p>
                        Actualice la información de
                        <?= e($mascota['nombre']) ?>.
                    </p>

                </div>

            </div>

            <?php if (!empty($errores)): ?>

                <div class="alert alert-error">

                    <ul>

                        <?php foreach ($errores as $error): ?>

                            <li>
                                <?= e($error) ?>
                            </li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="mascota-form-card">

                <div class="mascota-form-grid">

                    <div class="form-group">

                        <label for="nombre">
                            Nombre de la mascota *
                        </label>

                        <input type="text" id="nombre" name="nombre" value="<?= e($mascota['nombre']) ?>"
                            maxlength="100" required>

                    </div>

                    <div class="form-group">

                        <label for="especie_id">
                            Especie *
                        </label>

                        <select id="especie_id" name="especie_id" required>

                            <option value="">
                                Seleccione una especie
                            </option>

                            <?php foreach ($especies as $especie): ?>

                                <option value="<?= (int) $especie['id'] ?>" <?= (int) $mascota['especie_id']
                                       === (int) $especie['id']
                                       ? 'selected'
                                       : '' ?>>
                                    <?= e($especie['nombre']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="raza_id">
                            Raza
                        </label>

                        <select id="raza_id" name="raza_id">

                            <option value="">
                                Seleccione una raza
                            </option>

                            <?php foreach ($razas as $raza): ?>

                                <option value="<?= (int) $raza['id'] ?>" <?= !empty($mascota['raza_id']) &&
                                       (int) $mascota['raza_id']
                                       === (int) $raza['id']
                                       ? 'selected'
                                       : '' ?>>
                                    <?= e($raza['nombre']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="sexo">
                            Sexo *
                        </label>

                        <select id="sexo" name="sexo" required>

                            <option value="">
                                Seleccione
                            </option>

                            <option value="macho" <?= $mascota['sexo'] === 'macho'
                                ? 'selected'
                                : '' ?>>
                                Macho
                            </option>

                            <option value="hembra" <?= $mascota['sexo'] === 'hembra'
                                ? 'selected'
                                : '' ?>>
                                Hembra
                            </option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="fecha_nacimiento">
                            Fecha de nacimiento
                        </label>

                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= e(
                            $mascota['fecha_nacimiento'] ?? ''
                        ) ?>">

                    </div>

                    <div class="form-group">

                        <label for="peso">
                            Peso (kg)
                        </label>

                        <input type="number" id="peso" name="peso" step="0.01" min="0.01" value="<?= e(
                            $mascota['peso'] ?? ''
                        ) ?>">

                    </div>

                    <div class="form-group">

                        <label for="color">
                            Color
                        </label>

                        <input type="text" id="color" name="color" maxlength="80" value="<?= e(
                            $mascota['color'] ?? ''
                        ) ?>" placeholder="Ej. Café y blanco">

                    </div>

                    <div class="form-group">

                        <label for="microchip">
                            Microchip
                        </label>

                        <input type="text" id="microchip" name="microchip" maxlength="100" value="<?= e(
                            $mascota['microchip'] ?? ''
                        ) ?>" placeholder="Opcional">

                    </div>

                </div>

                <div class="mascota-check">

                    <input type="hidden" name="esterilizado" value="0">

                    <label>

                        <input type="checkbox" name="esterilizado" value="1" <?= !empty(
                            $mascota['esterilizado']
                        )
                            ? 'checked'
                            : '' ?>>

                        <span>
                            La mascota está esterilizada
                        </span>

                    </label>

                </div>

                <div class="form-group">

                    <label for="observaciones">
                        Observaciones
                    </label>

                    <textarea id="observaciones" name="observaciones" rows="5"
                        placeholder="Información adicional sobre su mascota..."><?= e(
                            $mascota['observaciones'] ?? ''
                        ) ?></textarea>

                </div>

                <div class="mascota-photo-section">

                    <label>
                        Foto de la mascota
                    </label>

                    <?php if (!empty($mascota['foto'])): ?>

                        <div class="mascota-photo-current">

                            <img src="<?= SITE_URL . '/' . e($mascota['foto']) ?>"
                                alt="Foto de <?= e($mascota['nombre']) ?>">

                        </div>

                        <label class="mascota-check">

                            <input type="checkbox" name="eliminar_foto" value="1">

                            <span>
                                Quitar fotografía actual
                            </span>

                        </label>

                        <p class="form-help">
                            Si selecciona una nueva fotografía,
                            esta reemplazará la actual.
                        </p>

                    <?php endif; ?>

                    <input type="file" id="foto" name="foto"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                    <small>
                        JPG, PNG o WEBP. Máximo 5 MB.
                    </small>

                </div>

                <div class="mascota-form-actions">

                    <button type="submit" class="btn btn-primary">
                        Guardar cambios
                    </button>

                    <a href="<?= SITE_URL ?>/pages/usuario/mascotas.php" class="btn btn-outline">
                        Cancelar
                    </a>

                </div>

            </form>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>