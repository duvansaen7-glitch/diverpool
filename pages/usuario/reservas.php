<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../models/Reserva.php';

if (!usuarioAutenticado() || !tieneRol('Cliente')) {
    redirect(SITE_URL . '/login.php');
}

$reservaModel = new Reserva();

$reservas = $reservaModel->obtenerPorUsuario(
    usuarioId()
);

$pageTitle = 'Mis reservas | ' . SITE_NAME;
$pageDescription = 'Consulte sus reservas y servicios programados.';

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
                        Mis reservas
                    </h1>

                    <p>
                        Consulte y gestione sus citas y servicios.
                    </p>

                </div>

                <div>

                    <a
                        href="<?= SITE_URL ?>/pages/usuario/nueva_reserva.php"
                        class="btn btn-primary"
                    >
                        Agendar servicio
                    </a>

                </div>

            </div>


            <?php if (empty($reservas)): ?>

                <div class="empty-state">

                    <div class="empty-state-icon">
                        📅
                    </div>

                    <h2>
                        No tiene reservas todavía
                    </h2>

                    <p>
                        Cuando agende un servicio para una de sus
                        mascotas, aparecerá aquí.
                    </p>

                    <a
                        href="<?= SITE_URL ?>/pages/usuario/nueva_reserva.php"
                        class="btn btn-primary"
                    >
                        Agendar mi primera cita
                    </a>

                </div>

            <?php else: ?>


                <div class="reservas-list">

                    <?php foreach ($reservas as $reserva): ?>

                        <?php

                        $estado = strtolower(
                            trim($reserva['estado_nombre'])
                        );

                        $claseEstado = match ($estado) {

                            'pendiente' =>
                                'reserva-estado pendiente',

                            'confirmada' =>
                                'reserva-estado confirmada',

                            'en proceso' =>
                                'reserva-estado proceso',

                            'completada' =>
                                'reserva-estado completada',

                            'cancelada' =>
                                'reserva-estado cancelada',

                            default =>
                                'reserva-estado'
                        };

                        $fecha = date(
                            'd/m/Y',
                            strtotime($reserva['fecha'])
                        );

                        $horaInicio = date(
                            'H:i',
                            strtotime($reserva['hora_inicio'])
                        );

                        $horaFin = date(
                            'H:i',
                            strtotime($reserva['hora_fin'])
                        );

                        $profesional = 'Por asignar';

                        if (
                            !empty($reserva['profesional_nombres'])
                        ) {

                            $profesional =
                                $reserva['profesional_nombres'] .
                                ' ' .
                                $reserva['profesional_apellidos'];
                        }

                        ?>

                        <article class="reserva-card">


                            <div class="reserva-card-header">

                                <div>

                                    <span class="reserva-label">
                                        Servicio
                                    </span>

                                    <h2>
                                        <?= e($reserva['servicio_nombre']) ?>
                                    </h2>

                                </div>

                                <span class="<?= e($claseEstado) ?>">
                                    <?= e($reserva['estado_nombre']) ?>
                                </span>

                            </div>


                            <div class="reserva-card-body">


                                <div class="reserva-info">

                                    <span class="reserva-label">
                                        Mascota
                                    </span>

                                    <strong>
                                        <?= e($reserva['mascota_nombre']) ?>
                                    </strong>

                                </div>


                                <div class="reserva-info">

                                    <span class="reserva-label">
                                        Fecha
                                    </span>

                                    <strong>
                                        <?= e($fecha) ?>
                                    </strong>

                                </div>


                                <div class="reserva-info">

                                    <span class="reserva-label">
                                        Horario
                                    </span>

                                    <strong>
                                        <?= e($horaInicio) ?>
                                        -
                                        <?= e($horaFin) ?>
                                    </strong>

                                </div>


                                <div class="reserva-info">

                                    <span class="reserva-label">
                                        Profesional
                                    </span>

                                    <strong>
                                        <?= e($profesional) ?>
                                    </strong>

                                </div>


                                <div class="reserva-info">

                                    <span class="reserva-label">
                                        Sede
                                    </span>

                                    <strong>
                                        <?= e($reserva['sede_nombre']) ?>
                                    </strong>

                                </div>


                                <div class="reserva-info">

                                    <span class="reserva-label">
                                        Precio
                                    </span>

                                    <strong>
                                        $
                                        <?= number_format(
                                            (float) $reserva['precio'],
                                            0,
                                            ',',
                                            '.'
                                        ) ?>
                                    </strong>

                                </div>


                            </div>


                            <div class="reserva-card-footer">

                                <a
                                    href="<?= SITE_URL ?>/pages/usuario/reserva_ver.php?id=<?= (int) $reserva['id'] ?>"
                                    class="btn btn-outline"
                                >
                                    Ver detalle
                                </a>


                                <?php if (
                                    in_array(
                                        $estado,
                                        ['pendiente', 'confirmada'],
                                        true
                                    )
                                ): ?>

                                    <a
                                        href="<?= SITE_URL ?>/pages/usuario/reserva_cancelar.php?id=<?= (int) $reserva['id'] ?>"
                                        class="btn btn-danger"
                                    >
                                        Cancelar reserva
                                    </a>

                                <?php endif; ?>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

