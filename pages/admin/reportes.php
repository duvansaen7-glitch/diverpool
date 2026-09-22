<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado() || !tieneRol('Administrador')) {
    redirect(SITE_URL . '/login.php');
}

$pageTitle = 'Reportes | Administración';

$pageDescription =
    'Reportes y estadísticas de Diverpool Mascotas.';

global $pdo;


/*
|--------------------------------------------------------------------------
| FILTROS
|--------------------------------------------------------------------------
*/

$fechaDesde = trim($_GET['desde'] ?? '');
$fechaHasta = trim($_GET['hasta'] ?? '');

if ($fechaDesde === '') {
    $fechaDesde = date('Y-m-01');
}

if ($fechaHasta === '') {
    $fechaHasta = date('Y-m-d');
}


/*
|--------------------------------------------------------------------------
| VALIDAR FECHAS
|--------------------------------------------------------------------------
*/

$fechaDesdeValida = DateTime::createFromFormat(
    'Y-m-d',
    $fechaDesde
);

$fechaHastaValida = DateTime::createFromFormat(
    'Y-m-d',
    $fechaHasta
);

if (
    !$fechaDesdeValida ||
    !$fechaHastaValida ||
    $fechaDesdeValida->format('Y-m-d') !== $fechaDesde ||
    $fechaHastaValida->format('Y-m-d') !== $fechaHasta
) {
    $fechaDesde = date('Y-m-01');
    $fechaHasta = date('Y-m-d');
}

if ($fechaDesde > $fechaHasta) {
    $temporal = $fechaDesde;
    $fechaDesde = $fechaHasta;
    $fechaHasta = $temporal;
}


/*
|--------------------------------------------------------------------------
| CONDICIÓN DE FECHAS
|--------------------------------------------------------------------------
*/

$whereFecha = "
    r.fecha BETWEEN ? AND ?
";

$paramsFecha = [
    $fechaDesde,
    $fechaHasta
];


/*
|--------------------------------------------------------------------------
| TOTAL DE RESERVAS
|--------------------------------------------------------------------------
*/

$stmtTotal = $pdo->prepare("
    SELECT COUNT(*)
    FROM reservas r
    WHERE $whereFecha
");

$stmtTotal->execute($paramsFecha);

$totalReservas = (int) $stmtTotal->fetchColumn();


/*
|--------------------------------------------------------------------------
| INGRESOS
|--------------------------------------------------------------------------
*/

$stmtIngresos = $pdo->prepare("
    SELECT COALESCE(SUM(r.precio), 0)
    FROM reservas r
    WHERE $whereFecha
");

$stmtIngresos->execute($paramsFecha);

$totalIngresos = (float) $stmtIngresos->fetchColumn();


/*
|--------------------------------------------------------------------------
| PENDIENTES
|--------------------------------------------------------------------------
*/

$stmtPendientes = $pdo->prepare("
    SELECT COUNT(*)
    FROM reservas r
    INNER JOIN estados_reserva er
        ON er.id = r.estado_id
    WHERE $whereFecha
      AND LOWER(er.nombre) LIKE '%pendiente%'
");

$stmtPendientes->execute($paramsFecha);

$totalPendientes = (int) $stmtPendientes->fetchColumn();


/*
|--------------------------------------------------------------------------
| CONFIRMADAS
|--------------------------------------------------------------------------
*/

$stmtConfirmadas = $pdo->prepare("
    SELECT COUNT(*)
    FROM reservas r
    INNER JOIN estados_reserva er
        ON er.id = r.estado_id
    WHERE $whereFecha
      AND LOWER(er.nombre) LIKE '%confirm%'
");

$stmtConfirmadas->execute($paramsFecha);

$totalConfirmadas = (int) $stmtConfirmadas->fetchColumn();


/*
|--------------------------------------------------------------------------
| COMPLETADAS
|--------------------------------------------------------------------------
*/

$stmtCompletadas = $pdo->prepare("
    SELECT COUNT(*)
    FROM reservas r
    INNER JOIN estados_reserva er
        ON er.id = r.estado_id
    WHERE $whereFecha
      AND LOWER(er.nombre) LIKE '%complet%'
");

$stmtCompletadas->execute($paramsFecha);

$totalCompletadas = (int) $stmtCompletadas->fetchColumn();


/*
|--------------------------------------------------------------------------
| CANCELADAS
|--------------------------------------------------------------------------
*/

$stmtCanceladas = $pdo->prepare("
    SELECT COUNT(*)
    FROM reservas r
    INNER JOIN estados_reserva er
        ON er.id = r.estado_id
    WHERE $whereFecha
      AND LOWER(er.nombre) LIKE '%cancel%'
");

$stmtCanceladas->execute($paramsFecha);

$totalCanceladas = (int) $stmtCanceladas->fetchColumn();


/*
|--------------------------------------------------------------------------
| SERVICIOS MÁS SOLICITADOS
|--------------------------------------------------------------------------
*/

$stmtServicios = $pdo->prepare("
    SELECT
        s.nombre,
        COUNT(r.id) AS total_reservas,
        COALESCE(SUM(r.precio), 0) AS ingresos
    FROM reservas r
    INNER JOIN servicios s
        ON s.id = r.servicio_id
    WHERE $whereFecha
    GROUP BY
        s.id,
        s.nombre
    ORDER BY
        total_reservas DESC,
        ingresos DESC,
        s.nombre ASC
    LIMIT 10
");

$stmtServicios->execute($paramsFecha);

$serviciosReporte = $stmtServicios->fetchAll();


/*
|--------------------------------------------------------------------------
| PROFESIONALES
|--------------------------------------------------------------------------
*/

$stmtProfesionales = $pdo->prepare("
    SELECT
        p.id,
        CONCAT(
            u.nombres,
            ' ',
            u.apellidos
        ) AS profesional_nombre,
        COUNT(r.id) AS total_reservas,
        COALESCE(SUM(r.precio), 0) AS ingresos
    FROM profesionales p
    INNER JOIN usuarios u
        ON u.id = p.usuario_id
    LEFT JOIN reservas r
        ON r.profesional_id = p.id
        AND r.fecha BETWEEN ? AND ?
    GROUP BY
        p.id,
        u.nombres,
        u.apellidos
    ORDER BY
        total_reservas DESC,
        ingresos DESC,
        profesional_nombre ASC
");

$stmtProfesionales->execute([
    $fechaDesde,
    $fechaHasta
]);

$profesionalesReporte = $stmtProfesionales->fetchAll();


/*
|--------------------------------------------------------------------------
| RESERVAS POR ESTADO
|--------------------------------------------------------------------------
*/

$stmtEstados = $pdo->prepare("
    SELECT
        er.nombre,
        COUNT(r.id) AS total
    FROM estados_reserva er
    LEFT JOIN reservas r
        ON r.estado_id = er.id
        AND r.fecha BETWEEN ? AND ?
    GROUP BY
        er.id,
        er.nombre
    ORDER BY
        total DESC,
        er.nombre ASC
");

$stmtEstados->execute([
    $fechaDesde,
    $fechaHasta
]);

$estadosReporte = $stmtEstados->fetchAll();


/*
|--------------------------------------------------------------------------
| RESERVAS POR DÍA
|--------------------------------------------------------------------------
*/

$stmtDias = $pdo->prepare("
    SELECT
        r.fecha,
        COUNT(*) AS total_reservas,
        COALESCE(SUM(r.precio), 0) AS ingresos
    FROM reservas r
    WHERE $whereFecha
    GROUP BY r.fecha
    ORDER BY r.fecha ASC
");

$stmtDias->execute($paramsFecha);

$reservasPorDia = $stmtDias->fetchAll();


/*
|--------------------------------------------------------------------------
| PROMEDIO
|--------------------------------------------------------------------------
*/

$promedioReserva = $totalReservas > 0
    ? $totalIngresos / $totalReservas
    : 0;


require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main class="admin-reportes-page">

    <!-- =====================================================
         ENCABEZADO
         ===================================================== -->

    <section class="admin-page-header">

        <div class="admin-page-header-content">

            <p class="admin-eyebrow">
                Administración
            </p>

            <h1>
                Reportes
            </h1>

            <p>
                Consulte estadísticas y comportamiento de las
                reservas de Diverpool Mascotas.
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
         FILTRO DE FECHAS
         ===================================================== -->

    <section class="reportes-filter-card">

        <div class="reportes-filter-header">

            <div>

                <h2>
                    Periodo del reporte
                </h2>

                <p>
                    Seleccione las fechas que desea analizar.
                </p>

            </div>

        </div>

        <form
            method="GET"
            class="reportes-filter-form"
        >

            <div class="reportes-field">

                <label for="desde">
                    Desde
                </label>

                <input
                    type="date"
                    id="desde"
                    name="desde"
                    value="<?= e($fechaDesde) ?>"
                >

            </div>


            <div class="reportes-field">

                <label for="hasta">
                    Hasta
                </label>

                <input
                    type="date"
                    id="hasta"
                    name="hasta"
                    value="<?= e($fechaHasta) ?>"
                >

            </div>


            <button
                type="submit"
                class="reportes-filter-button"
            >
                Generar reporte
            </button>


            <a
                href="<?= SITE_URL ?>/pages/admin/reportes.php"
                class="reportes-clear-button"
            >
                Periodo actual
            </a>

        </form>

    </section>


    <!-- =====================================================
         ESTADÍSTICAS
         ===================================================== -->

    <section class="reportes-stats">

        <div class="reporte-stat">

            <div class="reporte-stat-icon">
                #
            </div>

            <div>

                <span class="reporte-stat-value">
                    <?= $totalReservas ?>
                </span>

                <span class="reporte-stat-label">
                    Reservas
                </span>

            </div>

        </div>


        <div class="reporte-stat">

            <div class="reporte-stat-icon">
                $
            </div>

            <div>

                <span class="reporte-stat-value">
                    $<?= number_format(
                        $totalIngresos,
                        0,
                        ',',
                        '.'
                    ) ?>
                </span>

                <span class="reporte-stat-label">
                    Ingresos
                </span>

            </div>

        </div>


        <div class="reporte-stat">

            <div class="reporte-stat-icon">
                ✓
            </div>

            <div>

                <span class="reporte-stat-value">
                    <?= $totalConfirmadas ?>
                </span>

                <span class="reporte-stat-label">
                    Confirmadas
                </span>

            </div>

        </div>


        <div class="reporte-stat">

            <div class="reporte-stat-icon">
                !
            </div>

            <div>

                <span class="reporte-stat-value">
                    <?= $totalPendientes ?>
                </span>

                <span class="reporte-stat-label">
                    Pendientes
                </span>

            </div>

        </div>


        <div class="reporte-stat">

            <div class="reporte-stat-icon">
                OK
            </div>

            <div>

                <span class="reporte-stat-value">
                    <?= $totalCompletadas ?>
                </span>

                <span class="reporte-stat-label">
                    Completadas
                </span>

            </div>

        </div>


        <div class="reporte-stat">

            <div class="reporte-stat-icon">
                X
            </div>

            <div>

                <span class="reporte-stat-value">
                    <?= $totalCanceladas ?>
                </span>

                <span class="reporte-stat-label">
                    Canceladas
                </span>

            </div>

        </div>

    </section>


    <!-- =====================================================
         RESUMEN
         ===================================================== -->

    <section class="reportes-summary">

        <div class="reportes-summary-card">

            <span>
                Promedio por reserva
            </span>

            <strong>
                $<?= number_format(
                    $promedioReserva,
                    0,
                    ',',
                    '.'
                ) ?>
            </strong>

        </div>


        <div class="reportes-summary-card">

            <span>
                Periodo analizado
            </span>

            <strong>
                <?= date('d/m/Y', strtotime($fechaDesde)) ?>
                -
                <?= date('d/m/Y', strtotime($fechaHasta)) ?>
            </strong>

        </div>

    </section>


    <!-- =====================================================
         COLUMNAS DE REPORTES
         ===================================================== -->

    <section class="reportes-columns">

        <!-- SERVICIOS -->

        <div class="reportes-panel">

            <div class="reportes-panel-header">

                <div>

                    <p>
                        Demanda
                    </p>

                    <h2>
                        Servicios más solicitados
                    </h2>

                </div>

            </div>


            <?php if ($serviciosReporte): ?>

                <div class="reportes-table-wrapper">

                    <table class="reportes-table">

                        <thead>

                            <tr>

                                <th>
                                    Servicio
                                </th>

                                <th>
                                    Reservas
                                </th>

                                <th>
                                    Ingresos
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($serviciosReporte as $servicio): ?>

                                <tr>

                                    <td>
                                        <strong>
                                            <?= e($servicio['nombre']) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= (int) $servicio['total_reservas'] ?>
                                    </td>

                                    <td>
                                        $<?= number_format(
                                            (float) $servicio['ingresos'],
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

            <?php else: ?>

                <div class="reportes-empty-small">
                    No hay datos para este periodo.
                </div>

            <?php endif; ?>

        </div>


        <!-- PROFESIONALES -->

        <div class="reportes-panel">

            <div class="reportes-panel-header">

                <div>

                    <p>
                        Atención
                    </p>

                    <h2>
                        Reservas por profesional
                    </h2>

                </div>

            </div>


            <?php if ($profesionalesReporte): ?>

                <div class="reportes-table-wrapper">

                    <table class="reportes-table">

                        <thead>

                            <tr>

                                <th>
                                    Profesional
                                </th>

                                <th>
                                    Reservas
                                </th>

                                <th>
                                    Ingresos
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($profesionalesReporte as $profesional): ?>

                                <tr>

                                    <td>
                                        <strong>
                                            <?= e($profesional['profesional_nombre']) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= (int) $profesional['total_reservas'] ?>
                                    </td>

                                    <td>
                                        $<?= number_format(
                                            (float) $profesional['ingresos'],
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

            <?php else: ?>

                <div class="reportes-empty-small">
                    No hay profesionales registrados.
                </div>

            <?php endif; ?>

        </div>

    </section>


    <!-- =====================================================
         ESTADOS
         ===================================================== -->

    <section class="reportes-panel reportes-panel-full">

        <div class="reportes-panel-header">

            <div>

                <p>
                    Estado de las reservas
                </p>

                <h2>
                    Distribución por estado
                </h2>

            </div>

        </div>


        <?php if ($estadosReporte): ?>

            <div class="reportes-status-list">

                <?php foreach ($estadosReporte as $estado): ?>

                    <?php
                    $totalEstado = (int) $estado['total'];

                    $porcentaje = $totalReservas > 0
                        ? ($totalEstado / $totalReservas) * 100
                        : 0;
                    ?>

                    <div class="reportes-status-item">

                        <div class="reportes-status-top">

                            <strong>
                                <?= e($estado['nombre']) ?>
                            </strong>

                            <span>
                                <?= $totalEstado ?>
                                reserva<?= $totalEstado === 1 ? '' : 's' ?>
                                ·
                                <?= number_format(
                                    $porcentaje,
                                    1,
                                    ',',
                                    '.'
                                ) ?>%
                            </span>

                        </div>

                        <div class="reportes-progress">

                            <div
                                class="reportes-progress-bar"
                                style="width: <?= min(100, $porcentaje) ?>%;"
                            ></div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="reportes-empty-small">
                No hay información de estados.
            </div>

        <?php endif; ?>

    </section>


    <!-- =====================================================
         RESERVAS POR DÍA
         ===================================================== -->

    <section class="reportes-panel reportes-panel-full">

        <div class="reportes-panel-header">

            <div>

                <p>
                    Evolución
                </p>

                <h2>
                    Reservas por día
                </h2>

            </div>

        </div>


        <?php if ($reservasPorDia): ?>

            <div class="reportes-table-wrapper">

                <table class="reportes-table">

                    <thead>

                        <tr>

                            <th>
                                Fecha
                            </th>

                            <th>
                                Reservas
                            </th>

                            <th>
                                Ingresos
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($reservasPorDia as $dia): ?>

                            <tr>

                                <td>
                                    <?= date(
                                        'd/m/Y',
                                        strtotime($dia['fecha'])
                                    ) ?>
                                </td>

                                <td>
                                    <?= (int) $dia['total_reservas'] ?>
                                </td>

                                <td>
                                    $<?= number_format(
                                        (float) $dia['ingresos'],
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

        <?php else: ?>

            <div class="reportes-empty-small">
                No hay reservas en el periodo seleccionado.
            </div>

        <?php endif; ?>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
