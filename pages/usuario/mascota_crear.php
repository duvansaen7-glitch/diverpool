<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Mascota.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado()) {
    redirect(SITE_URL . '/login.php');
}

$usuarioId = usuarioId();
$mascotaModel = new Mascota();

$errores = [];

$especies = $mascotaModel->obtenerEspecies();

$nombre = '';
$especieId = '';
$razaId = '';
$sexo = '';
$fechaNacimiento = '';
$peso = '';
$color = '';
$microchip = '';
$esterilizado = 0;
$observaciones = '';
$fotoRuta = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $especieId = (int) ($_POST['especie_id'] ?? 0);
    $razaId = !empty($_POST['raza_id'])
        ? (int) $_POST['raza_id']
        : null;

    $sexo = $_POST['sexo'] ?? '';

    $fechaNacimiento = trim(
        $_POST['fecha_nacimiento'] ?? ''
    );

    $peso = trim($_POST['peso'] ?? '');
    $color = trim($_POST['color'] ?? '');
    $microchip = trim($_POST['microchip'] ?? '');

    /*
     * Esterilizado es opcional.
     * Si no se marca, queda en 0.
     */
    $esterilizado =
        (int) ($_POST['esterilizado'] ?? 0) === 1
        ? 1
        : 0;

    $observaciones = trim(
        $_POST['observaciones'] ?? ''
    );

    /*
     * =========================
     * VALIDACIONES
     * =========================
     */

    if ($nombre === '') {
        $errores[] =
            'Ingrese el nombre de la mascota.';
    }

    if ($especieId <= 0) {
        $errores[] =
            'Seleccione la especie.';
    }

    if (
        !in_array(
            $sexo,
            ['macho', 'hembra'],
            true
        )
    ) {
        $errores[] =
            'Seleccione el sexo de la mascota.';
    }

    if (
        $peso !== '' &&
        (
            !is_numeric($peso) ||
            (float) $peso <= 0
        )
    ) {
        $errores[] =
            'El peso debe ser un número mayor que cero.';
    }

    if ($fechaNacimiento !== '') {

        $fechaValida =
            DateTime::createFromFormat(
                'Y-m-d',
                $fechaNacimiento
            );

        if (
            !$fechaValida ||
            $fechaValida->format('Y-m-d')
            !== $fechaNacimiento
        ) {
            $errores[] =
                'La fecha de nacimiento no es válida.';
        }
    }

    /*
     * =========================
     * PROCESAR FOTOGRAFÍA
     * =========================
     */

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
                'No fue posible cargar la fotografía.';

        } else {

            $archivo = $_FILES['foto'];

            if (
                $archivo['size'] >
                5 * 1024 * 1024
            ) {
                $errores[] =
                    'La fotografía no puede superar los 5 MB.';
            }

            $tiposPermitidos = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            $finfo =
                finfo_open(FILEINFO_MIME_TYPE);

            $tipoMime =
                finfo_file(
                    $finfo,
                    $archivo['tmp_name']
                );

            finfo_close($finfo);

            if (
                !isset(
                $tiposPermitidos[$tipoMime]
            )
            ) {

                $errores[] =
                    'El archivo debe ser una imagen JPG, PNG o WEBP.';
            }

            if (!$errores) {

                $directorioFotos =
                    __DIR__ .
                    '/../../uploads/mascotas';

                if (
                    !is_dir($directorioFotos)
                ) {

                    mkdir(
                        $directorioFotos,
                        0755,
                        true
                    );
                }

                $extension =
                    $tiposPermitidos[$tipoMime];

                $nombreArchivo =
                    'mascota_' .
                    $usuarioId .
                    '_' .
                    bin2hex(random_bytes(8)) .
                    '.' .
                    $extension;

                $rutaFisica =
                    $directorioFotos .
                    '/' .
                    $nombreArchivo;

                if (
                    move_uploaded_file(
                        $archivo['tmp_name'],
                        $rutaFisica
                    )
                ) {

                    $fotoRuta =
                        'uploads/mascotas/' .
                        $nombreArchivo;

                } else {

                    $errores[] =
                        'No fue posible guardar la fotografía.';
                }
            }
        }
    }

    /*
     * =========================
     * REGISTRAR MASCOTA
     * =========================
     */

    if (!$errores) {

        try {

            $mascotaModel->crear(
                $usuarioId,
                $especieId,
                $razaId,
                $nombre,
                $sexo,
                $fechaNacimiento !== ''
                ? $fechaNacimiento
                : null,
                $peso !== ''
                ? (float) $peso
                : null,
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

            redirect(
                SITE_URL .
                '/pages/usuario/mascotas.php'
            );

        } catch (PDOException $e) {

            if (
                $e->getCode() === '23000'
            ) {

                $errores[] =
                    'El microchip ingresado ya está registrado.';

            } else {

                $errores[] =
                    'No fue posible registrar la mascota. Intente nuevamente.';
            }
        }
    }
}

$pageTitle =
    'Registrar mascota | Diverpool Mascotas';

$pageDescription =
    'Registre una mascota en su cuenta de Diverpool Mascotas.';

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
                        Registrar mascota
                    </h1>

                    <p>
                        Complete la información de su mascota
                        para asociarla a su cuenta.
                    </p>

                </div>

            </div>

            <?php if ($errores): ?>

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

            <form method="POST" class="mascota-form-card" enctype="multipart/form-data">

                <div class="mascota-form-grid">

                    <div class="form-group">

                        <label for="nombre">
                            Nombre de la mascota *
                        </label>

                        <input type="text" id="nombre" name="nombre" value="<?= e($nombre) ?>" maxlength="100"
                            placeholder="Ej. Max" required>

                    </div>

                    <div class="form-group">

                        <label for="foto">
                            Foto de la mascota
                        </label>

                        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">

                        <small>
                            JPG, PNG o WEBP. Máximo 5 MB.
                        </small>

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

                                <option value="<?= (int) $especie['id'] ?>" <?= $especieId === (int) $especie['id']
                                       ? 'selected'
                                       : '' ?>
                                    >
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
                                Seleccione primero una especie
                            </option>

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

                            <option value="macho" <?= $sexo === 'macho'
                                ? 'selected'
                                : '' ?>
                                >
                                Macho
                            </option>

                            <option value="hembra" <?= $sexo === 'hembra'
                                ? 'selected'
                                : '' ?>
                                >
                                Hembra
                            </option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="fecha_nacimiento">
                            Fecha de nacimiento
                        </label>

                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                            value="<?= e($fechaNacimiento) ?>">

                    </div>

                    <div class="form-group">

                        <label for="peso">
                            Peso (kg)
                        </label>

                        <input type="number" id="peso" name="peso" value="<?= e($peso) ?>" min="0.01" step="0.01"
                            placeholder="Ej. 8.50">

                    </div>

                    <div class="form-group">

                        <label for="color">
                            Color
                        </label>

                        <input type="text" id="color" name="color" value="<?= e($color) ?>" maxlength="80"
                            placeholder="Ej. Café y blanco">

                    </div>

                    <div class="form-group">

                        <label for="microchip">
                            Microchip
                        </label>

                        <input type="text" id="microchip" name="microchip" value="<?= e($microchip) ?>" maxlength="100"
                            placeholder="Opcional">

                    </div>

                </div>

                <div class="mascota-check">

                    <input type="hidden" name="esterilizado" value="0">

                    <label>

                        <input type="checkbox" name="esterilizado" value="1" <?= $esterilizado
                            ? 'checked'
                            : '' ?>
                        >

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
                        placeholder="Información adicional sobre su mascota..."><?= e($observaciones) ?></textarea>

                </div>

                <div class="mascota-form-actions">

                    <button type="submit" class="btn btn-primary">
                        Registrar mascota
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