<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../models/Reserva.php';


/*
 * =========================================================
 * PROTECCIÓN
 * =========================================================
 */

if (!usuarioAutenticado()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}


$usuarioId = usuarioId();


if ($usuarioId === null) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}


/*
 * =========================================================
 * OBTENER ID DE LA RESERVA
 * =========================================================
 */

$reservaId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);


if (!$reservaId || $reservaId <= 0) {

    header(
        'Location: ' .
        SITE_URL .
        '/pages/usuario/reservas.php'
    );

    exit;
}


/*
 * =========================================================
 * CONSULTAR RESERVA
 *
 * obtenerPorId() ya comprueba:
 *
 * reserva.id
 * +
 * reserva.usuario_id
 *
 * Por tanto, el usuario solamente puede
 * consultar sus propias reservas.
 * =========================================================
 */

$reservaModel = new Reserva();

$reserva = $reservaModel->obtenerPorId(
    $reservaId,
    $usuarioId
);


if (!$reserva) {

    header(
        'Location: ' .
        SITE_URL .
        '/pages/usuario/reservas.php?error=reserva_no_encontrada'
    );

    exit;
}


/*
 * =========================================================
 * DATOS PREPARADOS
 * =========================================================
 */

$estado = $reserva['estado_nombre'] ?? 'Sin estado';

$fecha = $reserva['fecha'] ?? '';

$fechaFormateada = $fecha;

if (!empty($fecha)) {

    $fechaObj = DateTime::createFromFormat(
        'Y-m-d',
        $fecha
    );

    if ($fechaObj) {

        $fechaFormateada =
            $fechaObj->format('d/m/Y');
    }
}


$horaInicio = !empty($reserva['hora_inicio'])
    ? substr($reserva['hora_inicio'], 0, 5)
    : '--:--';


$horaFin = !empty($reserva['hora_fin'])
    ? substr($reserva['hora_fin'], 0, 5)
    : '--:--';


$profesionalNombre = trim(
    ($reserva['profesional_nombres'] ?? '') .
    ' ' .
    ($reserva['profesional_apellidos'] ?? '')
);


if ($profesionalNombre === '') {
    $profesionalNombre = 'Profesional pendiente de asignación';
}


$precio = number_format(
    (float) ($reserva['precio'] ?? 0),
    0,
    ',',
    '.'
);


$descripcionServicio =
    $reserva['servicio_descripcion'] ??
    'Servicio para mascotas.';


$observaciones =
    trim($reserva['observaciones'] ?? '');


$motivoCancelacion =
    trim($reserva['motivo_cancelacion'] ?? '');


/*
 * Estados en los que todavía se puede cancelar.
 */

$puedeCancelar = in_array(
    mb_strtolower($estado),
    [
        'pendiente',
        'confirmada'
    ],
    true
);


require_once __DIR__ . '/../../includes/header.php';

?>

<main class="main-content">

    <section class="section">

        <div class="container">

            <div class="page-header">

                <div>

                    <span class="eyebrow">
                        MI CUENTA
                    </span>

                    <h1>
                        Detalle de reserva
                    </h1>

                    <p>
                        Consulte toda la información de su reserva.
                    </p>

                </div>

                <a
                    href="<?= SITE_URL ?>/pages/usuario/reservas.php"
                    class="btn btn-outline"
                >
                    Volver a mis reservas
                </a>

            </div>


            <div class="reserva-detalle-card">


                <!-- =================================================
                     CABECERA
                     ================================================= -->

                <div class="reserva-detalle-header">

                    <div>

                        <span class="reserva-detalle-label">
                            RESERVA
                        </span>

                        <h2>
                            #<?= (int) $reserva['id'] ?>
                        </h2>

                    </div>


                    <span
                        class="reserva-estado reserva-estado-<?= strtolower(
                            preg_replace(
                                '/[^a-z0-9]+/i',
                                '-',
                                $estado
                            )
                        ) ?>"
                    >
                        <?= htmlspecialchars($estado) ?>
                    </span>

                </div>


                <!-- =================================================
                     INFORMACIÓN PRINCIPAL
                     ================================================= -->

                <div class="reserva-detalle-grid">


                    <div class="reserva-detalle-item">

                        <span>
                            Mascota
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $reserva['mascota_nombre']
                            ) ?>
                        </strong>

                    </div>


                    <div class="reserva-detalle-item">

                        <span>
                            Servicio
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $reserva['servicio_nombre']
                            ) ?>
                        </strong>

                    </div>


                    <div class="reserva-detalle-item">

                        <span>
                            Fecha
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $fechaFormateada
                            ) ?>
                        </strong>

                    </div>


                    <div class="reserva-detalle-item">

                        <span>
                            Horario
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $horaInicio
                            ) ?>
                            -
                            <?= htmlspecialchars(
                                $horaFin
                            ) ?>
                        </strong>

                    </div>


                    <div class="reserva-detalle-item">

                        <span>
                            Profesional
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $profesionalNombre
                            ) ?>
                        </strong>

                    </div>


                    <div class="reserva-detalle-item">

                        <span>
                            Duración
                        </span>

                        <strong>
                            <?= (int) (
                                $reserva['duracion_minutos'] ?? 0
                            ) ?>
                            minutos
                        </strong>

                    </div>


                    <div class="reserva-detalle-item">

                        <span>
                            Sede
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $reserva['sede_nombre']
                            ) ?>
                        </strong>

                    </div>


                    <div class="reserva-detalle-item">

                        <span>
                            Ciudad
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $reserva['sede_ciudad']
                            ) ?>
                        </strong>

                    </div>


                    <div class="reserva-detalle-item">

                        <span>
                            Precio
                        </span>

                        <strong>
                            $ <?= $precio ?>
                        </strong>

                    </div>

                </div>


                <!-- =================================================
                     DIRECCIÓN
                     ================================================= -->

                <div class="reserva-detalle-bloque">

                    <span class="reserva-detalle-bloque-titulo">
                        Dirección de atención
                    </span>

                    <p>
                        <?= htmlspecialchars(
                            $reserva['sede_direccion']
                        ) ?>
                    </p>

                </div>


                <!-- =================================================
                     DESCRIPCIÓN DEL SERVICIO
                     ================================================= -->

                <div class="reserva-detalle-bloque">

                    <span class="reserva-detalle-bloque-titulo">
                        Servicio
                    </span>

                    <p>
                        <?= htmlspecialchars(
                            $descripcionServicio
                        ) ?>
                    </p>

                </div>


                <!-- =================================================
                     OBSERVACIONES
                     ================================================= -->

                <?php if ($observaciones !== ''): ?>

                    <div class="reserva-detalle-bloque">

                        <span class="reserva-detalle-bloque-titulo">
                            Observaciones
                        </span>

                        <p>
                            <?= nl2br(
                                htmlspecialchars(
                                    $observaciones
                                )
                            ) ?>
                        </p>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     CANCELACIÓN
                     ================================================= -->

                <?php if (
                    strtolower($estado) === 'cancelada'
                    && $motivoCancelacion !== ''
                ): ?>

                    <div class="reserva-detalle-cancelada">

                        <strong>
                            Motivo de cancelación
                        </strong>

                        <p>
                            <?= nl2br(
                                htmlspecialchars(
                                    $motivoCancelacion
                                )
                            ) ?>
                        </p>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     ACCIONES
                     ================================================= -->

                <div class="reserva-detalle-actions">

                    <a
                        href="<?= SITE_URL ?>/pages/usuario/reservas.php"
                        class="btn btn-outline"
                    >
                        Volver
                    </a>


                    <?php if ($puedeCancelar): ?>

                        <a
                            href="<?= SITE_URL ?>/pages/usuario/reserva_cancelar.php?id=<?= (int) $reserva['id'] ?>"
                            class="btn btn-danger"
                        >
                            Cancelar reserva
                        </a>

                    <?php endif; ?>

                </div>


            </div>

        </div>

    </section>

</main>


<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

