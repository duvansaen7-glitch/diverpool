<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Mascota.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado()) {
    redirect(SITE_URL . '/login.php');
}

$usuarioId = usuarioId();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(SITE_URL . '/pages/usuario/mascotas.php');
}

$mascotaId = (int) $_GET['id'];

$mascotaModel = new Mascota($pdo);

/*
 * Obtener la mascota perteneciente al usuario.
 */
$mascota = $mascotaModel->obtenerPorId($mascotaId, $usuarioId);

if (!$mascota) {
    redirect(SITE_URL . '/pages/usuario/mascotas.php');
}

/*
 * Solo eliminar cuando se confirme mediante POST.
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/pages/usuario/mascotas.php');
}

/*
 * Desactivar la mascota.
 */
$eliminada = $mascotaModel->desactivar(
    $mascotaId,
    $usuarioId
);

if ($eliminada) {

    /*
     * Eliminar la fotografía física,
     * si la mascota tenía una.
     */
    if (!empty($mascota['foto'])) {

        $rutaFoto = __DIR__ . '/../../' . $mascota['foto'];

        /*
         * Seguridad: solo eliminamos archivos
         * que estén dentro de uploads/mascotas.
         */
        $directorioPermitido = realpath(
            __DIR__ . '/../../uploads/mascotas'
        );

        $archivoReal = is_file($rutaFoto)
            ? realpath($rutaFoto)
            : false;

        if (
            $directorioPermitido !== false &&
            $archivoReal !== false &&
            str_starts_with(
                $archivoReal,
                $directorioPermitido . DIRECTORY_SEPARATOR
            )
        ) {
            unlink($archivoReal);
        }
    }

    redirect(
        SITE_URL .
        '/pages/usuario/mascotas.php?eliminada=1'
    );
}

redirect(
    SITE_URL .
    '/pages/usuario/mascotas.php?error=1'
);
