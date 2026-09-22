<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado() || !tieneRol('Administrador')) {
    redirect(SITE_URL . '/login.php');
}

$pageTitle = 'Reservas | Administración';
$pageDescription =
    'Gestión de reservas de Diverpool Mascotas.';

$mensaje = '';
$tipoMensaje = '';

/*
|--------------------------------------------------------------------------
| CAMBIAR ESTADO DE RESERVA
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'cambiar_estado') {

        $reservaId = (int) ($_POST['reserva_id'] ?? 0);
        $estadoId = (int) ($_POST['estado_id'] ?? 0);

        if ($reservaId <= 0 || $estadoId <= 0) {

            $mensaje = 'Datos inválidos para actualizar la reserva.';
            $tipoMensaje = 'error';

        } else {

            $stmtEstado = $pdo->prepare("
                SELECT id, nombre
                FROM estados_reserva
                WHERE id = ?
                LIMIT 1
            ");

            $stmtEstado->execute([$estadoId]);
            $estado = $stmtEstado->fetch();

            if (!$estado) {

                $mensaje = 'El estado seleccionado no existe.';
                $tipoMensaje = 'error';

            } else {

                $stmtReserva = $pdo->prepare("
                    SELECT id
                    FROM reservas
                    WHERE id = ?
                    LIMIT 1
                ");

                $stmtReserva->execute([$reservaId]);

                if (!$stmtReserva->fetch()) {

                    $mensaje = 'La reserva no existe.';
                    $tipoMensaje = 'error';

                } else {

                    $stmtUpdate = $pdo->prepare("
                        UPDATE reservas
                        SET estado_id = ?,
                            updated_at = NOW()
                        WHERE id = ?
                    ");

                    $stmtUpdate->execute([
                        $estadoId,
                        $reservaId
                    ]);

                    $mensaje = 'Estado de la reserva actualizado correctamente.';
                    $tipoMensaje = 'success';
                }
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| ESTADOS
|--------------------------------------------------------------------------
*/

$stmtEstados = $pdo->query("
    SELECT
        id,
        nombre,
        descripcion,
        estado
    FROM estados_reserva
    ORDER BY id ASC
");

$estados = $stmtEstados->fetchAll();

/*
|--------------------------------------------------------------------------
| FILTROS
|--------------------------------------------------------------------------
*/

$filtroEstado = isset($_GET['estado']) ? (int) $_GET['estado'] : 0;
$filtroFecha = trim($_GET['fecha'] ?? '');
$filtroBusqueda = trim($_GET['buscar'] ?? '');

/*
|--------------------------------------------------------------------------
| RESERVAS
|--------------------------------------------------------------------------
*/

$sqlReservas = "
    SELECT
        r.id,
        r.usuario_id,
        r.mascota_id,
        r.servicio_id,
        r.profesional_id,
        r.sede_id,
        r.estado_id,
        r.fecha,
        r.hora_inicio,
        r.hora_fin,
        r.precio,
        r.observaciones,
        r.motivo_cancelacion,
        r.created_at,
        r.updated_at,

        CONCAT(u.nombres, ' ', u.apellidos) AS cliente_nombre,
        u.correo AS cliente_correo,
        u.telefono AS cliente_telefono,

        m.nombre AS mascota_nombre,

        s.nombre AS servicio_nombre,
        s.duracion_minutos,

        CONCAT(
            p_usuario.nombres,
            ' ',
            p_usuario.apellidos
        ) AS profesional_nombre,

        sede.nombre AS sede_nombre,

        er.nombre AS estado_nombre,
        er.descripcion AS estado_descripcion

    FROM reservas r

    INNER JOIN usuarios u
        ON u.id = r.usuario_id

    INNER JOIN mascotas m
        ON m.id = r.mascota_id

    INNER JOIN servicios s
        ON s.id = r.servicio_id

    LEFT JOIN profesionales p
        ON p.id = r.profesional_id

    LEFT JOIN usuarios p_usuario
        ON p_usuario.id = p.usuario_id

    LEFT JOIN sedes sede
        ON sede.id = r.sede_id

    INNER JOIN estados_reserva er
        ON er.id = r.estado_id

    WHERE 1 = 1
";

$params = [];

/*
|--------------------------------------------------------------------------
| FILTRO ESTADO
|--------------------------------------------------------------------------
*/

if ($filtroEstado > 0) {
    $sqlReservas .= " AND r.estado_id = ?";
    $params[] = $filtroEstado;
}

/*
|--------------------------------------------------------------------------
| FILTRO FECHA
|--------------------------------------------------------------------------
*/

if ($filtroFecha !== '') {
    $sqlReservas .= " AND r.fecha = ?";
    $params[] = $filtroFecha;
}

/*
|--------------------------------------------------------------------------
| BUSCADOR
|--------------------------------------------------------------------------
*/

if ($filtroBusqueda !== '') {

    $sqlReservas .= "
        AND (
            CONCAT(u.nombres, ' ', u.apellidos) LIKE ?
            OR m.nombre LIKE ?
            OR s.nombre LIKE ?
            OR CONCAT(
                p_usuario.nombres,
                ' ',
                p_usuario.apellidos
            ) LIKE ?
        )
    ";

    $busqueda = '%' . $filtroBusqueda . '%';

    $params[] = $busqueda;
    $params[] = $busqueda;
    $params[] = $busqueda;
    $params[] = $busqueda;
}

/*
|--------------------------------------------------------------------------
| ORDEN
|--------------------------------------------------------------------------
*/

$sqlReservas .= "
    ORDER BY
        r.fecha DESC,
        r.hora_inicio DESC,
        r.id DESC
";

$stmtReservas = $pdo->prepare($sqlReservas);
$stmtReservas->execute($params);

$reservas = $stmtReservas->fetchAll();

/*
|--------------------------------------------------------------------------
| ESTADÍSTICAS
|--------------------------------------------------------------------------
*/

$stmtTotal = $pdo->query("
    SELECT COUNT(*)
    FROM reservas
");

$totalReservas = (int) $stmtTotal->fetchColumn();

$stmtHoy = $pdo->query("
    SELECT COUNT(*)
    FROM reservas
    WHERE fecha = CURDATE()
");

$reservasHoy = (int) $stmtHoy->fetchColumn();

$stmtPendientes = $pdo->query("
    SELECT COUNT(*)
    FROM reservas r
    INNER JOIN estados_reserva er
        ON er.id = r.estado_id
    WHERE LOWER(er.nombre) LIKE '%pendiente%'
");

$reservasPendientes = (int) $stmtPendientes->fetchColumn();

$stmtConfirmadas = $pdo->query("
    SELECT COUNT(*)
    FROM reservas r
    INNER JOIN estados_reserva er
        ON er.id = r.estado_id
    WHERE LOWER(er.nombre) LIKE '%confirm%'
");

$reservasConfirmadas = (int) $stmtConfirmadas->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main class="admin-reservas-page">

    <!-- =====================================================
         ENCABEZADO
         ===================================================== -->

    <section class="admin-page-header">

        <div class="admin-page-header-content">

            <p class="admin-eyebrow">
                Administración
            </p>

            <h1>
                Reservas
            </h1>

            <p>
                Consulte y gestione las reservas realizadas
                por los clientes de Diverpool Mascotas.
            </p>

        </div>

        <div class="admin-header-actions">

            <a
                href="<?= SITE_URL ?>/pages/admin/dashboard.php"
                class="admin-secondary-button"
            >
                ← Panel
            </a>

        </div>

    </section>

    <!-- =====================================================
         MENSAJE
         ===================================================== -->

    <?php if ($mensaje): ?>

        <div class="admin-reservation-message <?= e($tipoMensaje) ?>">
            <?= e($mensaje) ?>
        </div>

    <?php endif; ?>


    <!-- =====================================================
         ESTADÍSTICAS
         ===================================================== -->

    <section class="reservation-admin-stats">

        <div class="reservation-admin-stat">

            <div class="reservation-admin-stat-icon">
                #
            </div>

            <div>
                <span class="reservation-admin-stat-value">
                    <?= $totalReservas ?>
                </span>

                <span class="reservation-admin-stat-label">
                    Total reservas
                </span>
            </div>

        </div>


        <div class="reservation-admin-stat">

            <div class="reservation-admin-stat-icon">
                Hoy
            </div>

            <div>
                <span class="reservation-admin-stat-value">
                    <?= $reservasHoy ?>
                </span>

                <span class="reservation-admin-stat-label">
                    Reservas de hoy
                </span>
            </div>

        </div>


        <div class="reservation-admin-stat">

            <div class="reservation-admin-stat-icon">
                ...
            </div>

            <div>
                <span class="reservation-admin-stat-value">
                    <?= $reservasPendientes ?>
                </span>

                <span class="reservation-admin-stat-label">
                    Pendientes
                </span>
            </div>

        </div>


        <div class="reservation-admin-stat">

            <div class="reservation-admin-stat-icon">
                ✓
            </div>

            <div>
                <span class="reservation-admin-stat-value">
                    <?= $reservasConfirmadas ?>
                </span>

                <span class="reservation-admin-stat-label">
                    Confirmadas
                </span>
            </div>

        </div>

    </section>


    <!-- =====================================================
         FILTROS
         ===================================================== -->

    <form
        method="GET"
        class="reservations-admin-toolbar"
    >

        <div class="reservation-search">

            <input
                type="text"
                name="buscar"
                value="<?= e($filtroBusqueda) ?>"
                placeholder="Buscar cliente, mascota, servicio o profesional..."
            >

        </div>


        <div>

            <input
                type="date"
                name="fecha"
                value="<?= e($filtroFecha) ?>"
            >

        </div>


        <div>

            <select name="estado">

                <option value="0">
                    Todos los estados
                </option>

                <?php foreach ($estados as $estado): ?>

                    <option
                        value="<?= (int) $estado['id'] ?>"
                        <?= $filtroEstado === (int) $estado['id'] ? 'selected' : '' ?>
                    >
                        <?= e($estado['nombre']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <button
            type="submit"
            class="reservation-filter-button"
        >
            Filtrar
        </button>


        <a
            href="<?= SITE_URL ?>/pages/admin/reservas.php"
            class="reservation-clear-button"
        >
            Limpiar
        </a>

    </form>


    <!-- =====================================================
         LISTADO
         ===================================================== -->

    <section class="reservations-admin-list">

        <?php if (!$reservas): ?>

            <div class="reservation-admin-empty">

                <div class="reservation-admin-empty-icon">
                    📅
                </div>

                <h2>
                    No hay reservas
                </h2>

                <p>
                    No se encontraron reservas con los filtros seleccionados.
                </p>

            </div>

        <?php else: ?>

            <?php foreach ($reservas as $reserva): ?>

                <?php
                $estadoClase = strtolower(
                    preg_replace(
                        '/[^a-z0-9]+/i',
                        '-',
                        $reserva['estado_nombre']
                    )
                );
                ?>

                <article class="reservation-admin-card">

                    <!-- CABECERA -->

                    <div class="reservation-admin-card-header">

                        <div>

                            <span class="reservation-admin-id">
                                Reserva #<?= (int) $reserva['id'] ?>
                            </span>

                            <h2>
                                <?= e($reserva['servicio_nombre']) ?>
                            </h2>

                        </div>

                        <span class="reservation-admin-status <?= e($estadoClase) ?>">
                            <?= e($reserva['estado_nombre']) ?>
                        </span>

                    </div>


                    <!-- FECHA -->

                    <div class="reservation-admin-date">

                        <div class="reservation-admin-date-main">

                            <strong>
                                <?= date(
                                    'd/m/Y',
                                    strtotime($reserva['fecha'])
                                ) ?>
                            </strong>

                            <span>
                                <?= date(
                                    'H:i',
                                    strtotime($reserva['hora_inicio'])
                                ) ?>
                                -
                                <?= date(
                                    'H:i',
                                    strtotime($reserva['hora_fin'])
                                ) ?>
                            </span>

                        </div>

                    </div>


                    <!-- INFORMACIÓN -->

                    <div class="reservation-admin-info-grid">

                        <div class="reservation-admin-info">

                            <span>
                                Cliente
                            </span>

                            <strong>
                                <?= e($reserva['cliente_nombre']) ?>
                            </strong>

                            <small>
                                <?= e($reserva['cliente_correo']) ?>
                            </small>

                        </div>


                        <div class="reservation-admin-info">

                            <span>
                                Mascota
                            </span>

                            <strong>
                                <?= e($reserva['mascota_nombre']) ?>
                            </strong>

                        </div>


                        <div class="reservation-admin-info">

                            <span>
                                Profesional
                            </span>

                            <strong>
                                <?= $reserva['profesional_nombre']
                                    ? e($reserva['profesional_nombre'])
                                    : 'Sin asignar'
                                ?>
                            </strong>

                        </div>


                        <div class="reservation-admin-info">

                            <span>
                                Sede
                            </span>

                            <strong>
                                <?= $reserva['sede_nombre']
                                    ? e($reserva['sede_nombre'])
                                    : 'Sin sede'
                                ?>
                            </strong>

                        </div>

                    </div>


                    <!-- PRECIO -->

                    <div class="reservation-admin-price">

                        <span>
                            Precio
                        </span>

                        <strong>
                            $<?= number_format(
                                (float) $reserva['precio'],
                                0,
                                ',',
                                '.'
                            ) ?>
                        </strong>

                    </div>


                    <?php if (!empty($reserva['observaciones'])): ?>

                        <div class="reservation-admin-observations">

                            <span>
                                Observaciones
                            </span>

                            <p>
                                <?= e($reserva['observaciones']) ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if (!empty($reserva['motivo_cancelacion'])): ?>

                        <div class="reservation-admin-cancellation">

                            <span>
                                Motivo de cancelación
                            </span>

                            <p>
                                <?= e($reserva['motivo_cancelacion']) ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <!-- ACCIONES -->

                    <div class="reservation-admin-actions">

                        <form
                            method="POST"
                            class="reservation-status-form"
                        >

                            <input
                                type="hidden"
                                name="accion"
                                value="cambiar_estado"
                            >

                            <input
                                type="hidden"
                                name="reserva_id"
                                value="<?= (int) $reserva['id'] ?>"
                            >

                            <select
                                name="estado_id"
                                onchange="this.form.submit()"
                            >

                                <?php foreach ($estados as $estado): ?>

                                    <option
                                        value="<?= (int) $estado['id'] ?>"
                                        <?= (int) $reserva['estado_id'] === (int) $estado['id']
                                            ? 'selected'
                                            : ''
                                        ?>
                                    >
                                        <?= e($estado['nombre']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </form>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
