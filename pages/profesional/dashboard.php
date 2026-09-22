<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado() || !tieneRol('Profesional')) {
    redirect(SITE_URL . '/login.php');
    exit;
}

$usuarioId = (int) usuarioId();

try {

    /*
     * Información del profesional.
     */
    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.numero_tarjeta_profesional,
            p.descripcion,
            p.experiencia_anios,
            p.foto,
            p.estado,
            u.nombres,
            u.apellidos,
            u.correo,
            u.telefono
        FROM profesionales p
        INNER JOIN usuarios u
            ON u.id = p.usuario_id
        WHERE p.usuario_id = :usuario_id
        LIMIT 1
    ");

    $stmt->execute([
        ':usuario_id' => $usuarioId
    ]);

    $profesional = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profesional) {
        redirect(
            SITE_URL .
            '/pages/usuario/dashboard.php?error=perfil_profesional'
        );
        exit;
    }

    $profesionalId = (int) $profesional['id'];


    /*
     * Servicios asignados al profesional.
     */
    $stmt = $pdo->prepare("
        SELECT
            s.id,
            s.nombre,
            s.descripcion,
            s.precio
        FROM servicio_profesionales sp
        INNER JOIN servicios s
            ON s.id = sp.servicio_id
        WHERE sp.profesional_id = :profesional_id
        ORDER BY s.nombre ASC
    ");

    $stmt->execute([
        ':profesional_id' => $profesionalId
    ]);

    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
     * Cantidad total de reservas.
     */
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM reservas
        WHERE profesional_id = :profesional_id
    ");

    $stmt->execute([
        ':profesional_id' => $profesionalId
    ]);

    $totalReservas = (int) $stmt->fetchColumn();


    /*
     * Reservas pendientes.
     */
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM reservas r
        INNER JOIN estados_reserva er
            ON er.id = r.estado_id
        WHERE r.profesional_id = :profesional_id
        AND LOWER(er.nombre) IN (
            'pendiente',
            'confirmada',
            'confirmado'
        )
    ");

    $stmt->execute([
        ':profesional_id' => $profesionalId
    ]);

    $reservasPendientes = (int) $stmt->fetchColumn();


    /*
     * Reservas recientes.
     */
    $stmt = $pdo->prepare("
        SELECT
            r.id,
            r.fecha,
            r.hora_inicio,
            r.hora_fin,
            r.mascota_id,
            s.nombre AS servicio_nombre,
            er.nombre AS estado_nombre,
            m.nombre AS mascota_nombre
        FROM reservas r
        INNER JOIN servicios s
            ON s.id = r.servicio_id
        INNER JOIN estados_reserva er
            ON er.id = r.estado_id
        LEFT JOIN mascotas m
            ON m.id = r.mascota_id
        WHERE r.profesional_id = :profesional_id
        ORDER BY
            r.fecha DESC,
            r.hora_inicio DESC
        LIMIT 5
    ");

    $stmt->execute([
        ':profesional_id' => $profesionalId
    ]);

    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $profesional = null;
    $servicios = [];
    $reservas = [];
    $totalReservas = 0;
    $reservasPendientes = 0;

    $errorDashboard =
        'No fue posible cargar toda la información del panel.';
}


$pageTitle = 'Panel Profesional | Diverpool Mascotas';
$pageDescription = 'Panel de gestión para profesionales de Diverpool Mascotas.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

    <section class="section">

        <div class="container">

            <?php if (isset($errorDashboard)): ?>

                <div class="alert alert-error">
                    <?= e($errorDashboard) ?>
                </div>

            <?php endif; ?>


            <?php if ($profesional): ?>

                <div class="section-heading">

                    <div>

                        <span class="eyebrow">
                            PANEL PROFESIONAL
                        </span>

                        <h1>
                            Bienvenido,
                            <?= e($profesional['nombres']) ?>
                        </h1>

                        <p>
                            Gestione sus servicios y consulte sus reservas.
                        </p>

                    </div>

                    <div>

                        <a
                            href="<?= SITE_URL ?>/pages/usuario/perfil.php"
                            class="btn btn-outline"
                        >
                            Mi perfil
                        </a>

                    </div>

                </div>


                <!-- RESUMEN -->

                <div class="admin-stats">

                    <div class="admin-stat-card">

                        <span class="admin-stat-label">
                            Reservas
                        </span>

                        <strong class="admin-stat-number">
                            <?= $totalReservas ?>
                        </strong>

                    </div>


                    <div class="admin-stat-card">

                        <span class="admin-stat-label">
                            Pendientes
                        </span>

                        <strong class="admin-stat-number">
                            <?= $reservasPendientes ?>
                        </strong>

                    </div>


                    <div class="admin-stat-card">

                        <span class="admin-stat-label">
                            Servicios
                        </span>

                        <strong class="admin-stat-number">
                            <?= count($servicios) ?>
                        </strong>

                    </div>

                </div>


                <!-- INFORMACIÓN PROFESIONAL -->

                <div class="about-card">

                    <div class="section-heading">

                        <div>

                            <span class="eyebrow">
                                MI INFORMACIÓN
                            </span>

                            <h2>
                                Perfil profesional
                            </h2>

                        </div>

                    </div>


                    <div class="form-grid">

                        <div>

                            <strong>
                                Nombre
                            </strong>

                            <p>
                                <?= e(
                                    $profesional['nombres'] .
                                    ' ' .
                                    $profesional['apellidos']
                                ) ?>
                            </p>

                        </div>


                        <div>

                            <strong>
                                Correo
                            </strong>

                            <p>
                                <?= e($profesional['correo']) ?>
                            </p>

                        </div>


                        <div>

                            <strong>
                                Teléfono
                            </strong>

                            <p>
                                <?= e(
                                    $profesional['telefono'] ?: 'No registrado'
                                ) ?>
                            </p>

                        </div>


                        <div>

                            <strong>
                                Tarjeta profesional
                            </strong>

                            <p>
                                <?= e(
                                    $profesional['numero_tarjeta_profesional']
                                    ?: 'No registrada'
                                ) ?>
                            </p>

                        </div>


                        <div>

                            <strong>
                                Experiencia
                            </strong>

                            <p>
                                <?= (int) $profesional['experiencia_anios'] ?>
                                años
                            </p>

                        </div>


                        <div>

                            <strong>
                                Estado
                            </strong>

                            <p>
                                <?= e(ucfirst($profesional['estado'])) ?>
                            </p>

                        </div>

                    </div>


                    <?php if (!empty($profesional['descripcion'])): ?>

                        <div style="margin-top: 1.5rem;">

                            <strong>
                                Descripción
                            </strong>

                            <p>
                                <?= nl2br(
                                    e($profesional['descripcion'])
                                ) ?>
                            </p>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- SERVICIOS -->

                <div class="about-card">

                    <div class="section-heading">

                        <div>

                            <span class="eyebrow">
                                SERVICIOS
                            </span>

                            <h2>
                                Servicios asignados
                            </h2>

                        </div>

                    </div>


                    <?php if (empty($servicios)): ?>

                        <p>
                            Actualmente no tiene servicios asignados.
                        </p>

                    <?php else: ?>

                        <div class="admin-table-wrapper">

                            <table class="admin-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Servicio
                                        </th>

                                        <th>
                                            Descripción
                                        </th>

                                        <th>
                                            Precio
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php foreach ($servicios as $servicio): ?>

                                        <tr>

                                            <td>
                                                <strong>
                                                    <?= e($servicio['nombre']) ?>
                                                </strong>
                                            </td>

                                            <td>
                                                <?= e(
                                                    $servicio['descripcion']
                                                    ?: 'Sin descripción'
                                                ) ?>
                                            </td>

                                            <td>
                                                $<?= number_format(
                                                    (float) $servicio['precio'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- RESERVAS -->

                <div class="about-card">

                    <div class="section-heading">

                        <div>

                            <span class="eyebrow">
                                AGENDA
                            </span>

                            <h2>
                                Reservas recientes
                            </h2>

                        </div>

                    </div>


                    <?php if (empty($reservas)): ?>

                        <p>
                            No tiene reservas registradas todavía.
                        </p>

                    <?php else: ?>

                        <div class="admin-table-wrapper">

                            <table class="admin-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Fecha
                                        </th>

                                        <th>
                                            Hora
                                        </th>

                                        <th>
                                            Mascota
                                        </th>

                                        <th>
                                            Servicio
                                        </th>

                                        <th>
                                            Estado
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php foreach ($reservas as $reserva): ?>

                                        <tr>

                                            <td>
                                                <?= e(
                                                    date(
                                                        'd/m/Y',
                                                        strtotime(
                                                            $reserva['fecha_reserva']
                                                        )
                                                    )
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= e(
                                                    substr(
                                                        $reserva['hora_inicio'],
                                                        0,
                                                        5
                                                    )
                                                ) ?>

                                                <?php if (
                                                    !empty(
                                                        $reserva['hora_fin']
                                                    )
                                                ): ?>

                                                    -
                                                    <?= e(
                                                        substr(
                                                            $reserva['hora_fin'],
                                                            0,
                                                            5
                                                        )
                                                    ) ?>

                                                <?php endif; ?>

                                            </td>

                                            <td>
                                                <?= e(
                                                    $reserva['mascota_nombre']
                                                    ?: 'Sin mascota'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= e(
                                                    $reserva['servicio_nombre']
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= e(
                                                    $reserva['estado_nombre']
                                                ) ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
