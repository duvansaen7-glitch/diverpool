<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado() || !tieneRol('Administrador')) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$pageTitle = 'Profesionales | Administración';
$pageDescription = 'Gestión de profesionales de Diverpool Mascotas.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

global $pdo;

$mensaje = '';
$error = '';

/*
|--------------------------------------------------------------------------
| GUARDAR PROFESIONAL
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'guardar') {

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        $usuarioId = isset($_POST['usuario_id'])
            ? (int) $_POST['usuario_id']
            : 0;

        $tarjeta = trim($_POST['numero_tarjeta_profesional'] ?? '');

        $descripcion = trim($_POST['descripcion'] ?? '');

        $experiencia = isset($_POST['experiencia_anios'])
            ? (int) $_POST['experiencia_anios']
            : 0;

        $estado = $_POST['estado'] ?? 'activo';

        $estadosPermitidos = [
            'activo',
            'inactivo',
            'vacaciones'
        ];

        if ($id > 0) {

            /*
             * EDITAR PROFESIONAL
             */

            if (!in_array($estado, $estadosPermitidos, true)) {
                $estado = 'activo';
            }

            if ($experiencia < 0) {
                $experiencia = 0;
            }

            try {

                $sql = "
                    UPDATE profesionales
                    SET
                        numero_tarjeta_profesional = :tarjeta,
                        descripcion = :descripcion,
                        experiencia_anios = :experiencia,
                        estado = :estado,
                        updated_at = NOW()
                    WHERE id = :id
                ";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':tarjeta' => $tarjeta !== '' ? $tarjeta : null,
                    ':descripcion' => $descripcion !== '' ? $descripcion : null,
                    ':experiencia' => $experiencia,
                    ':estado' => $estado,
                    ':id' => $id
                ]);

                $mensaje = 'Profesional actualizado correctamente.';

            } catch (PDOException $e) {

                $error = 'No fue posible actualizar el profesional.';
            }

        } else {

            /*
             * CREAR PROFESIONAL
             */

            if ($usuarioId <= 0) {

                $error = 'Debe seleccionar un usuario profesional.';

            } elseif (!in_array($estado, $estadosPermitidos, true)) {

                $estado = 'activo';

            } else {

                try {

                    /*
                     * Verificar que el usuario exista,
                     * tenga rol Profesional y no tenga
                     * ya un registro profesional.
                     */

                    $sqlUsuario = "
                        SELECT
                            u.id,
                            u.nombres,
                            u.apellidos,
                            u.correo
                        FROM usuarios u
                        INNER JOIN roles r
                            ON r.id = u.rol_id
                        LEFT JOIN profesionales p
                            ON p.usuario_id = u.id
                        WHERE
                            u.id = :usuario_id
                            AND r.nombre = 'Profesional'
                            AND p.id IS NULL
                        LIMIT 1
                    ";

                    $stmtUsuario = $pdo->prepare($sqlUsuario);

                    $stmtUsuario->execute([
                        ':usuario_id' => $usuarioId
                    ]);

                    $usuario = $stmtUsuario->fetch();

                    if (!$usuario) {

                        $error =
                            'El usuario seleccionado no está disponible '
                            . 'para registrarlo como profesional.';

                    } else {

                        $sql = "
                            INSERT INTO profesionales (
                                usuario_id,
                                numero_tarjeta_profesional,
                                descripcion,
                                experiencia_anios,
                                estado,
                                created_at,
                                updated_at
                            )
                            VALUES (
                                :usuario_id,
                                :tarjeta,
                                :descripcion,
                                :experiencia,
                                :estado,
                                NOW(),
                                NOW()
                            )
                        ";

                        $stmt = $pdo->prepare($sql);

                        $stmt->execute([
                            ':usuario_id' => $usuarioId,
                            ':tarjeta' => $tarjeta !== '' ? $tarjeta : null,
                            ':descripcion' => $descripcion !== '' ? $descripcion : null,
                            ':experiencia' => max(0, $experiencia),
                            ':estado' => $estado
                        ]);

                        $mensaje =
                            'Profesional registrado correctamente.';
                    }

                } catch (PDOException $e) {

                    $error =
                        'No fue posible registrar el profesional.';
                }
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| OBTENER PROFESIONAL PARA EDITAR
|--------------------------------------------------------------------------
*/

$editarId = isset($_GET['editar'])
    ? (int) $_GET['editar']
    : 0;

$profesionalEditar = null;

if ($editarId > 0) {

    $sql = "
        SELECT
            p.*,
            u.nombres,
            u.apellidos,
            u.correo,
            u.telefono,
            u.documento
        FROM profesionales p
        INNER JOIN usuarios u
            ON u.id = p.usuario_id
        WHERE p.id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id' => $editarId
    ]);

    $profesionalEditar = $stmt->fetch();

    if (!$profesionalEditar) {
        $error = 'El profesional solicitado no existe.';
    }
}


/*
|--------------------------------------------------------------------------
| USUARIOS DISPONIBLES PARA CREAR PROFESIONAL
|--------------------------------------------------------------------------
*/

$sqlUsuarios = "
    SELECT
        u.id,
        u.nombres,
        u.apellidos,
        u.correo
    FROM usuarios u
    INNER JOIN roles r
        ON r.id = u.rol_id
    LEFT JOIN profesionales p
        ON p.usuario_id = u.id
    WHERE
        r.nombre = 'Profesional'
        AND p.id IS NULL
        AND u.estado = 'activo'
    ORDER BY u.nombres ASC, u.apellidos ASC
";

$stmtUsuarios = $pdo->query($sqlUsuarios);

$usuariosDisponibles = $stmtUsuarios->fetchAll();


/*
|--------------------------------------------------------------------------
| LISTADO DE PROFESIONALES
|--------------------------------------------------------------------------
*/

$sqlProfesionales = "
    SELECT
        p.id,
        p.usuario_id,
        p.numero_tarjeta_profesional,
        p.descripcion,
        p.experiencia_anios,
        p.foto,
        p.estado,
        p.created_at,
        u.nombres,
        u.apellidos,
        u.correo,
        u.telefono
    FROM profesionales p
    INNER JOIN usuarios u
        ON u.id = p.usuario_id
    ORDER BY
        CASE p.estado
            WHEN 'activo' THEN 1
            WHEN 'vacaciones' THEN 2
            WHEN 'inactivo' THEN 3
            ELSE 4
        END,
        u.nombres ASC
";

$stmt = $pdo->query($sqlProfesionales);

$profesionales = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| ESTADÍSTICAS
|--------------------------------------------------------------------------
*/

$totalProfesionales = count($profesionales);

$activos = 0;
$vacaciones = 0;
$inactivos = 0;

foreach ($profesionales as $profesional) {

    if ($profesional['estado'] === 'activo') {
        $activos++;
    }

    if ($profesional['estado'] === 'vacaciones') {
        $vacaciones++;
    }

    if ($profesional['estado'] === 'inactivo') {
        $inactivos++;
    }
}

?>

<main class="admin-profesionales-page">

    <div class="container">

        <!-- ==========================================
             ENCABEZADO
        =========================================== -->

        <section class="admin-page-header">

            <div>

                <span class="admin-eyebrow">
                    ADMINISTRACIÓN
                </span>

                <h1>
                    Profesionales
                </h1>

                <p>
                    Gestione los profesionales encargados
                    de atender los servicios de Diverpool Mascotas.
                </p>

            </div>

            <div class="admin-header-actions">

                <a
                    href="<?= SITE_URL ?>/pages/admin/dashboard.php"
                    class="admin-secondary-button"
                >
                    ← Panel
                </a>

                <button
                    type="button"
                    class="admin-primary-button"
                    id="btnNuevoProfesional"
                >
                    + Nuevo profesional
                </button>

            </div>

        </section>


        <!-- ==========================================
             MENSAJES
        =========================================== -->

        <?php if ($mensaje): ?>

            <div class="admin-message success">
                <span>✓</span>
                <?= e($mensaje) ?>
            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="admin-message error">
                <span>!</span>
                <?= e($error) ?>
            </div>

        <?php endif; ?>


        <!-- ==========================================
             ESTADÍSTICAS
        =========================================== -->

        <section class="professional-stats">

            <article class="professional-stat">

                <div class="professional-stat-icon">
                    ✦
                </div>

                <div>
                    <strong>
                        <?= $totalProfesionales ?>
                    </strong>

                    <span>
                        Profesionales
                    </span>
                </div>

            </article>


            <article class="professional-stat">

                <div class="professional-stat-icon active">
                    ✓
                </div>

                <div>
                    <strong>
                        <?= $activos ?>
                    </strong>

                    <span>
                        Activos
                    </span>
                </div>

            </article>


            <article class="professional-stat">

                <div class="professional-stat-icon vacation">
                    ◷
                </div>

                <div>
                    <strong>
                        <?= $vacaciones ?>
                    </strong>

                    <span>
                        En vacaciones
                    </span>
                </div>

            </article>


            <article class="professional-stat">

                <div class="professional-stat-icon inactive">
                    —
                </div>

                <div>
                    <strong>
                        <?= $inactivos ?>
                    </strong>

                    <span>
                        Inactivos
                    </span>
                </div>

            </article>

        </section>


        <!-- ==========================================
             BARRA DE FILTROS
        =========================================== -->

        <section class="professional-toolbar">

            <div class="professional-search">

                <span>
                    ⌕
                </span>

                <input
                    type="search"
                    id="buscarProfesional"
                    placeholder="Buscar por nombre o correo..."
                    autocomplete="off"
                >

            </div>


            <select id="filtroEstado">

                <option value="todos">
                    Todos los estados
                </option>

                <option value="activo">
                    Activos
                </option>

                <option value="vacaciones">
                    Vacaciones
                </option>

                <option value="inactivo">
                    Inactivos
                </option>

            </select>

        </section>


        <!-- ==========================================
             LISTADO
        =========================================== -->

        <section class="professionals-list">

            <?php if (!$profesionales): ?>

                <div class="professionals-empty">

                    <div class="empty-icon">
                        ✦
                    </div>

                    <h2>
                        Aún no hay profesionales
                    </h2>

                    <p>
                        Registre el primer profesional para
                        comenzar a gestionar la atención.
                    </p>

                    <button
                        type="button"
                        class="admin-primary-button"
                        id="btnNuevoProfesionalEmpty"
                    >
                        + Registrar profesional
                    </button>

                </div>

            <?php else: ?>

                <?php foreach ($profesionales as $profesional): ?>

                    <?php

                    $nombreCompleto = trim(
                        ($profesional['nombres'] ?? '') . ' ' .
                        ($profesional['apellidos'] ?? '')
                    );

                    $iniciales = '';

                    if (!empty($profesional['nombres'])) {
                        $iniciales .= strtoupper(
                            mb_substr(
                                $profesional['nombres'],
                                0,
                                1
                            )
                        );
                    }

                    if (!empty($profesional['apellidos'])) {
                        $iniciales .= strtoupper(
                            mb_substr(
                                $profesional['apellidos'],
                                0,
                                1
                            )
                        );
                    }

                    if ($iniciales === '') {
                        $iniciales = 'PR';
                    }

                    $estadoTexto = match ($profesional['estado']) {
                        'activo' => 'Activo',
                        'vacaciones' => 'Vacaciones',
                        'inactivo' => 'Inactivo',
                        default => ucfirst($profesional['estado'])
                    };

                    ?>

                    <article
                        class="professional-card"
                        data-name="<?= e(strtolower($nombreCompleto)) ?>"
                        data-email="<?= e(strtolower($profesional['correo'])) ?>"
                        data-status="<?= e($profesional['estado']) ?>"
                    >

                        <div class="professional-card-top">

                            <div class="professional-avatar">

                                <?php if (!empty($profesional['foto'])): ?>

                                    <img
                                        src="<?= SITE_URL ?>/<?= e($profesional['foto']) ?>"
                                        alt="<?= e($nombreCompleto) ?>"
                                    >

                                <?php else: ?>

                                    <?= e($iniciales) ?>

                                <?php endif; ?>

                            </div>


                            <span
                                class="professional-status status-<?= e($profesional['estado']) ?>"
                            >
                                <span></span>

                                <?= e($estadoTexto) ?>

                            </span>

                        </div>


                        <div class="professional-card-content">

                            <span class="professional-role">
                                PROFESIONAL
                            </span>

                            <h2>
                                <?= e($nombreCompleto) ?>
                            </h2>

                            <p class="professional-email">
                                <?= e($profesional['correo']) ?>
                            </p>


                            <div class="professional-details">

                                <div>

                                    <span>
                                        Tarjeta profesional
                                    </span>

                                    <strong>
                                        <?= e(
                                            $profesional[
                                                'numero_tarjeta_profesional'
                                            ] ?: 'No registrada'
                                        ) ?>
                                    </strong>

                                </div>


                                <div>

                                    <span>
                                        Experiencia
                                    </span>

                                    <strong>
                                        <?= (int) $profesional['experiencia_anios'] ?>
                                        <?= (int) $profesional['experiencia_anios'] === 1
                                            ? 'año'
                                            : 'años'
                                        ?>
                                    </strong>

                                </div>

                            </div>


                            <?php if (!empty($profesional['descripcion'])): ?>

                                <p class="professional-description">
                                    <?= e($profesional['descripcion']) ?>
                                </p>

                            <?php endif; ?>

                        </div>


                        <div class="professional-card-footer">

                            <a
                                href="?editar=<?= (int) $profesional['id'] ?>"
                                class="professional-edit-button"
                            >
                                Editar
                                <span>→</span>
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </section>


        <div
            class="professional-no-results"
            id="noProfesionales"
            hidden
        >

            <strong>
                No encontramos profesionales.
            </strong>

            <span>
                Pruebe con otro nombre, correo o estado.
            </span>

        </div>

    </div>

</main>


<!-- ==========================================
     MODAL PROFESIONAL
=========================================== -->

<div
    class="professional-modal"
    id="professionalModal"
    aria-hidden="true"
>

    <div class="professional-modal-backdrop"></div>

    <div
        class="professional-modal-box"
        role="dialog"
        aria-modal="true"
        aria-labelledby="professionalModalTitle"
    >

        <div class="professional-modal-header">

            <div>

                <span>
                    <?= $profesionalEditar
                        ? 'EDITAR PROFESIONAL'
                        : 'NUEVO PROFESIONAL' ?>
                </span>

                <h2 id="professionalModalTitle">

                    <?= $profesionalEditar
                        ? 'Editar profesional'
                        : 'Registrar profesional' ?>

                </h2>

            </div>

            <button
                type="button"
                class="professional-modal-close"
                id="cerrarProfessionalModal"
                aria-label="Cerrar"
            >
                ×
            </button>

        </div>


        <form
            method="POST"
            class="professional-form"
        >

            <input
                type="hidden"
                name="accion"
                value="guardar"
            >

            <input
                type="hidden"
                name="id"
                value="<?= $profesionalEditar
                    ? (int) $profesionalEditar['id']
                    : 0 ?>"
            >


            <?php if (!$profesionalEditar): ?>

                <div class="professional-form-group">

                    <label for="usuario_id">
                        Usuario profesional
                    </label>

                    <select
                        name="usuario_id"
                        id="usuario_id"
                        required
                    >

                        <option value="">
                            Seleccione un usuario
                        </option>

                        <?php foreach ($usuariosDisponibles as $usuario): ?>

                            <option
                                value="<?= (int) $usuario['id'] ?>"
                            >

                                <?= e(
                                    trim(
                                        $usuario['nombres'] . ' ' .
                                        $usuario['apellidos']
                                    )
                                ) ?>

                                —
                                <?= e($usuario['correo']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <?php if (!$usuariosDisponibles): ?>

                        <small class="form-help">
                            No hay usuarios con rol Profesional
                            disponibles para registrar.
                        </small>

                    <?php endif; ?>

                </div>

            <?php else: ?>

                <div class="professional-selected-user">

                    <div class="selected-user-avatar">

                        <?= e(
                            strtoupper(
                                mb_substr(
                                    $profesionalEditar['nombres'],
                                    0,
                                    1
                                )
                            )
                        ) ?>

                    </div>

                    <div>

                        <strong>

                            <?= e(
                                trim(
                                    $profesionalEditar['nombres'] . ' ' .
                                    $profesionalEditar['apellidos']
                                )
                            ) ?>

                        </strong>

                        <span>
                            <?= e($profesionalEditar['correo']) ?>
                        </span>

                    </div>

                </div>

            <?php endif; ?>


            <div class="professional-form-grid">

                <div class="professional-form-group">

                    <label for="numero_tarjeta_profesional">
                        Tarjeta profesional
                    </label>

                    <input
                        type="text"
                        id="numero_tarjeta_profesional"
                        name="numero_tarjeta_profesional"
                        maxlength="100"
                        placeholder="Ej. TP-001"
                        value="<?= e(
                            $profesionalEditar[
                                'numero_tarjeta_profesional'
                            ] ?? ''
                        ) ?>"
                    >

                </div>


                <div class="professional-form-group">

                    <label for="experiencia_anios">
                        Experiencia
                    </label>

                    <input
                        type="number"
                        id="experiencia_anios"
                        name="experiencia_anios"
                        min="0"
                        max="80"
                        value="<?= (int) (
                            $profesionalEditar[
                                'experiencia_anios'
                            ] ?? 0
                        ) ?>"
                    >

                </div>

            </div>


            <div class="professional-form-group">

                <label for="estado">
                    Estado
                </label>

                <select
                    name="estado"
                    id="estado"
                >

                    <?php

                    $estadoActual =
                        $profesionalEditar['estado']
                        ?? 'activo';

                    ?>

                    <option
                        value="activo"
                        <?= $estadoActual === 'activo'
                            ? 'selected'
                            : '' ?>
                    >
                        Activo
                    </option>

                    <option
                        value="vacaciones"
                        <?= $estadoActual === 'vacaciones'
                            ? 'selected'
                            : '' ?>
                    >
                        Vacaciones
                    </option>

                    <option
                        value="inactivo"
                        <?= $estadoActual === 'inactivo'
                            ? 'selected'
                            : '' ?>
                    >
                        Inactivo
                    </option>

                </select>

            </div>


            <div class="professional-form-group">

                <label for="descripcion">
                    Descripción
                </label>

                <textarea
                    id="descripcion"
                    name="descripcion"
                    rows="4"
                    maxlength="1000"
                    placeholder="Describa la experiencia o especialidad del profesional..."
                ><?= e(
                    $profesionalEditar['descripcion'] ?? ''
                ) ?></textarea>

            </div>


            <div class="professional-form-actions">

                <button
                    type="button"
                    class="professional-cancel-button"
                    id="cancelarProfessionalModal"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="admin-primary-button"
                >
                    <?= $profesionalEditar
                        ? 'Guardar cambios'
                        : 'Registrar profesional' ?>
                </button>

            </div>

        </form>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', () => {

    /* =====================================================
       BUSCADOR
    ===================================================== */

    const buscador =
        document.getElementById('buscarProfesional');

    const filtro =
        document.getElementById('filtroEstado');

    const tarjetas =
        document.querySelectorAll('.professional-card');

    const sinResultados =
        document.getElementById('noProfesionales');


    function filtrarProfesionales() {

        const texto =
            (buscador?.value || '')
                .toLowerCase()
                .trim();

        const estado =
            filtro?.value || 'todos';

        let visibles = 0;


        tarjetas.forEach(tarjeta => {

            const nombre =
                tarjeta.dataset.name || '';

            const correo =
                tarjeta.dataset.email || '';

            const estadoTarjeta =
                tarjeta.dataset.status || '';


            const coincideTexto =
                nombre.includes(texto) ||
                correo.includes(texto);

            const coincideEstado =
                estado === 'todos' ||
                estadoTarjeta === estado;


            const mostrar =
                coincideTexto &&
                coincideEstado;


            tarjeta.style.display =
                mostrar ? '' : 'none';


            if (mostrar) {
                visibles++;
            }

        });


        if (sinResultados) {

            sinResultados.hidden =
                visibles !== 0;

        }

    }


    buscador?.addEventListener(
        'input',
        filtrarProfesionales
    );

    filtro?.addEventListener(
        'change',
        filtrarProfesionales
    );


    /* =====================================================
       MODAL
    ===================================================== */

    const modal =
        document.getElementById('professionalModal');

    const abrir =
        document.getElementById('btnNuevoProfesional');

    const abrirVacio =
        document.getElementById(
            'btnNuevoProfesionalEmpty'
        );

    const cerrar =
        document.getElementById(
            'cerrarProfessionalModal'
        );

    const cancelar =
        document.getElementById(
            'cancelarProfessionalModal'
        );


    function abrirModal() {

        if (!modal) {
            return;
        }

        modal.classList.add('open');

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'modal-open'
        );

    }


    function cerrarModal() {

        if (!modal) {
            return;
        }

        modal.classList.remove('open');

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.classList.remove(
            'modal-open'
        );

    }


    abrir?.addEventListener('click', () => {
    const url = new URL(window.location.href);

    if (url.searchParams.has('editar')) {
        url.searchParams.delete('editar');
        window.location.href = url.toString();
        return;
    }

    abrirModal();
});

    abrirVacio?.addEventListener(
        'click',
        abrirModal
    );

    cerrar?.addEventListener(
        'click',
        cerrarModal
    );

    cancelar?.addEventListener(
        'click',
        cerrarModal
    );


    modal?.querySelector(
        '.professional-modal-backdrop'
    )?.addEventListener(
        'click',
        cerrarModal
    );


    document.addEventListener(
        'keydown',
        event => {

            if (event.key === 'Escape') {
                cerrarModal();
            }

        }
    );


    /*
     * Si estamos editando un profesional,
     * abrimos automáticamente el modal.
     */

    <?php if ($profesionalEditar): ?>

        abrirModal();

    <?php endif; ?>

});

</script>


<?php

require_once __DIR__ . '/../../includes/footer.php';

?>