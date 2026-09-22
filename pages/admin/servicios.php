<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/funciones.php';

if (!usuarioAutenticado() || !tieneRol('Administrador')) {
    redirect(SITE_URL . '/login.php');
}

$pageTitle = 'Servicios | Administración';

$pageDescription =
    'Gestión de servicios de Diverpool Mascotas.';

global $pdo;

$mensaje = '';
$error = '';

$accion = $_POST['accion'] ?? '';

$editarId = isset($_GET['editar'])
    ? (int) $_GET['editar']
    : 0;


/*
|--------------------------------------------------------------------------
| PROCESAR FORMULARIOS
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($accion === 'crear') {

        $categoriaId = (int) ($_POST['categoria_id'] ?? 0);

        $nombre = trim($_POST['nombre'] ?? '');

        $descripcion = trim($_POST['descripcion'] ?? '');

        $duracion = (int) ($_POST['duracion_minutos'] ?? 0);

        $precio = (float) ($_POST['precio'] ?? 0);

        $requiereProfesional =
            isset($_POST['requiere_profesional'])
                ? 1
                : 0;

        $estado = $_POST['estado'] ?? 'activo';


        if ($categoriaId <= 0) {

            $error = 'Debe seleccionar una categoría.';

        } elseif ($nombre === '') {

            $error = 'El nombre del servicio es obligatorio.';

        } elseif (mb_strlen($nombre) > 150) {

            $error =
                'El nombre del servicio no puede superar los 150 caracteres.';

        } elseif ($duracion <= 0) {

            $error =
                'La duración debe ser mayor que cero minutos.';

        } elseif ($precio < 0) {

            $error =
                'El precio no puede ser negativo.';

        } elseif (!in_array($estado, ['activo', 'inactivo'], true)) {

            $error =
                'El estado seleccionado no es válido.';

        } else {

            try {

                $stmt = $pdo->prepare("
                    SELECT id
                    FROM categorias_servicios
                    WHERE id = :id
                    LIMIT 1
                ");

                $stmt->execute([
                    ':id' => $categoriaId
                ]);

                if (!$stmt->fetch()) {

                    throw new RuntimeException(
                        'La categoría seleccionada no existe.'
                    );
                }


                $stmt = $pdo->prepare("
                    SELECT id
                    FROM servicios
                    WHERE categoria_id = :categoria_id
                      AND LOWER(nombre) = LOWER(:nombre)
                    LIMIT 1
                ");

                $stmt->execute([
                    ':categoria_id' => $categoriaId,
                    ':nombre' => $nombre
                ]);

                if ($stmt->fetch()) {

                    throw new RuntimeException(
                        'Ya existe un servicio con ese nombre en la categoría seleccionada.'
                    );
                }


                $stmt = $pdo->prepare("
                    INSERT INTO servicios (
                        categoria_id,
                        nombre,
                        descripcion,
                        duracion_minutos,
                        precio,
                        requiere_profesional,
                        estado
                    )
                    VALUES (
                        :categoria_id,
                        :nombre,
                        :descripcion,
                        :duracion,
                        :precio,
                        :requiere_profesional,
                        :estado
                    )
                ");

                $stmt->execute([
                    ':categoria_id' => $categoriaId,
                    ':nombre' => $nombre,
                    ':descripcion' =>
                        $descripcion !== ''
                            ? $descripcion
                            : null,
                    ':duracion' => $duracion,
                    ':precio' => $precio,
                    ':requiere_profesional' =>
                        $requiereProfesional,
                    ':estado' => $estado
                ]);

                $mensaje =
                    'Servicio creado correctamente.';

            } catch (Throwable $e) {

                $error =
                    $e instanceof RuntimeException
                        ? $e->getMessage()
                        : 'No fue posible crear el servicio.';
            }
        }
    }


    elseif ($accion === 'editar') {

        $id = (int) ($_POST['id'] ?? 0);

        $categoriaId = (int) ($_POST['categoria_id'] ?? 0);

        $nombre = trim($_POST['nombre'] ?? '');

        $descripcion = trim($_POST['descripcion'] ?? '');

        $duracion = (int) ($_POST['duracion_minutos'] ?? 0);

        $precio = (float) ($_POST['precio'] ?? 0);

        $requiereProfesional =
            isset($_POST['requiere_profesional'])
                ? 1
                : 0;

        $estado = $_POST['estado'] ?? 'activo';


        if ($id <= 0) {

            $error = 'Servicio no válido.';

        } elseif ($categoriaId <= 0) {

            $error = 'Debe seleccionar una categoría.';

        } elseif ($nombre === '') {

            $error = 'El nombre del servicio es obligatorio.';

        } elseif ($duracion <= 0) {

            $error =
                'La duración debe ser mayor que cero minutos.';

        } elseif ($precio < 0) {

            $error =
                'El precio no puede ser negativo.';

        } elseif (!in_array($estado, ['activo', 'inactivo'], true)) {

            $error =
                'El estado seleccionado no es válido.';

        } else {

            try {

                $stmt = $pdo->prepare("
                    SELECT id
                    FROM servicios
                    WHERE id = :id
                    LIMIT 1
                ");

                $stmt->execute([
                    ':id' => $id
                ]);

                if (!$stmt->fetch()) {

                    throw new RuntimeException(
                        'El servicio no existe.'
                    );
                }


                $stmt = $pdo->prepare("
                    SELECT id
                    FROM categorias_servicios
                    WHERE id = :id
                    LIMIT 1
                ");

                $stmt->execute([
                    ':id' => $categoriaId
                ]);

                if (!$stmt->fetch()) {

                    throw new RuntimeException(
                        'La categoría seleccionada no existe.'
                    );
                }


                $stmt = $pdo->prepare("
                    SELECT id
                    FROM servicios
                    WHERE categoria_id = :categoria_id
                      AND LOWER(nombre) = LOWER(:nombre)
                      AND id <> :id
                    LIMIT 1
                ");

                $stmt->execute([
                    ':categoria_id' => $categoriaId,
                    ':nombre' => $nombre,
                    ':id' => $id
                ]);

                if ($stmt->fetch()) {

                    throw new RuntimeException(
                        'Ya existe otro servicio con ese nombre en la categoría seleccionada.'
                    );
                }


                $stmt = $pdo->prepare("
                    UPDATE servicios
                    SET
                        categoria_id = :categoria_id,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        duracion_minutos = :duracion,
                        precio = :precio,
                        requiere_profesional = :requiere_profesional,
                        estado = :estado,
                        updated_at = NOW()
                    WHERE id = :id
                ");

                $stmt->execute([
                    ':categoria_id' => $categoriaId,
                    ':nombre' => $nombre,
                    ':descripcion' =>
                        $descripcion !== ''
                            ? $descripcion
                            : null,
                    ':duracion' => $duracion,
                    ':precio' => $precio,
                    ':requiere_profesional' =>
                        $requiereProfesional,
                    ':estado' => $estado,
                    ':id' => $id
                ]);

                $mensaje =
                    'Servicio actualizado correctamente.';

            } catch (Throwable $e) {

                $error =
                    $e instanceof RuntimeException
                        ? $e->getMessage()
                        : 'No fue posible actualizar el servicio.';
            }
        }
    }


    elseif ($accion === 'cambiar_estado') {

        $id = (int) ($_POST['id'] ?? 0);

        $nuevoEstado = $_POST['estado'] ?? '';


        if ($id <= 0) {

            $error = 'Servicio no válido.';

        } elseif (
            !in_array(
                $nuevoEstado,
                ['activo', 'inactivo'],
                true
            )
        ) {

            $error = 'Estado no válido.';

        } else {

            try {

                $stmt = $pdo->prepare("
                    UPDATE servicios
                    SET
                        estado = :estado,
                        updated_at = NOW()
                    WHERE id = :id
                ");

                $stmt->execute([
                    ':estado' => $nuevoEstado,
                    ':id' => $id
                ]);

                $mensaje =
                    $nuevoEstado === 'activo'
                        ? 'Servicio activado correctamente.'
                        : 'Servicio desactivado correctamente.';

            } catch (Throwable $e) {

                $error =
                    'No fue posible cambiar el estado del servicio.';
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| CATEGORÍAS
|--------------------------------------------------------------------------
*/

$stmtCategorias = $pdo->query("
    SELECT
        id,
        nombre
    FROM categorias_servicios
    ORDER BY nombre ASC
");

$categorias = $stmtCategorias->fetchAll();


/*
|--------------------------------------------------------------------------
| SERVICIO EN EDICIÓN
|--------------------------------------------------------------------------
*/

$servicioEditar = null;

if ($editarId > 0) {

    $stmt = $pdo->prepare("
        SELECT
            id,
            categoria_id,
            nombre,
            descripcion,
            duracion_minutos,
            precio,
            imagen,
            requiere_profesional,
            estado
        FROM servicios
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $editarId
    ]);

    $servicioEditar = $stmt->fetch();

    if (!$servicioEditar) {

        $error = 'El servicio solicitado no existe.';

        $editarId = 0;
    }
}


/*
|--------------------------------------------------------------------------
| LISTADO
|--------------------------------------------------------------------------
*/

$stmtServicios = $pdo->query("
    SELECT
        s.id,
        s.categoria_id,
        s.nombre,
        s.descripcion,
        s.duracion_minutos,
        s.precio,
        s.imagen,
        s.requiere_profesional,
        s.estado,
        c.nombre AS categoria_nombre
    FROM servicios s
    INNER JOIN categorias_servicios c
        ON c.id = s.categoria_id
    ORDER BY
        CASE s.estado
            WHEN 'activo' THEN 1
            WHEN 'inactivo' THEN 2
            ELSE 3
        END,
        c.nombre ASC,
        s.nombre ASC
");

$servicios = $stmtServicios->fetchAll();


/*
|--------------------------------------------------------------------------
| ESTADÍSTICAS
|--------------------------------------------------------------------------
*/

$totalServicios = count($servicios);

$serviciosActivos = 0;

$serviciosInactivos = 0;

$requierenProfesional = 0;


foreach ($servicios as $servicio) {

    if ($servicio['estado'] === 'activo') {
        $serviciosActivos++;
    }

    if ($servicio['estado'] === 'inactivo') {
        $serviciosInactivos++;
    }

    if ((int) $servicio['requiere_profesional'] === 1) {
        $requierenProfesional++;
    }
}


require_once __DIR__ . '/../../includes/header.php';

require_once __DIR__ . '/../../includes/navbar.php';

?>

<main class="admin-servicios-page">

    <div class="container">

        <section class="admin-page-header">

            <div>

                <span class="admin-eyebrow">
                    ADMINISTRACIÓN
                </span>

                <h1>
                    Servicios
                </h1>

                <p>
                    Gestione los servicios que Diverpool Mascotas
                    ofrece a sus clientes.
                </p>

            </div>


            <div class="admin-header-actions">

                <a
                    href="<?= SITE_URL ?>/pages/admin/dashboard.php"
                    class="admin-secondary-button"
                >
                    ← Panel
                </a>

                <a
                    href="<?= SITE_URL ?>/pages/admin/servicios.php?nuevo=1"
                    class="admin-primary-button"
                >
                    + Nuevo servicio
                </a>

            </div>

        </section>


        <?php if ($mensaje): ?>

            <div class="admin-service-message success">
                ✓ <?= e($mensaje) ?>
            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="admin-service-message error">
                ! <?= e($error) ?>
            </div>

        <?php endif; ?>


        <?php if ($editarId > 0 || isset($_GET['nuevo'])): ?>

            <?php
            $formEditar = $servicioEditar !== null;
            ?>

            <section class="service-admin-form-wrapper">

                <h2>
                    <?= $formEditar
                        ? 'Editar servicio'
                        : 'Nuevo servicio'
                    ?>
                </h2>

                <form
                    method="POST"
                    class="service-admin-form"
                >

                    <input
                        type="hidden"
                        name="accion"
                        value="<?= $formEditar
                            ? 'editar'
                            : 'crear' ?>"
                    >

                    <?php if ($formEditar): ?>

                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int) $servicioEditar['id'] ?>"
                        >

                    <?php endif; ?>


                    <div class="service-admin-field">

                        <label for="categoria_id">
                            Categoría
                        </label>

                        <select
                            id="categoria_id"
                            name="categoria_id"
                            required
                        >

                            <option value="">
                                Seleccione una categoría
                            </option>

                            <?php foreach ($categorias as $categoria): ?>

                                <option
                                    value="<?= (int) $categoria['id'] ?>"
                                    <?= (
                                        $formEditar &&
                                        (int) $servicioEditar['categoria_id']
                                        === (int) $categoria['id']
                                    )
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= e($categoria['nombre']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="service-admin-field">

                        <label for="nombre">
                            Nombre del servicio
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            maxlength="150"
                            required
                            value="<?= $formEditar
                                ? e($servicioEditar['nombre'])
                                : '' ?>"
                        >

                    </div>


                    <div class="service-admin-field">

                        <label for="duracion_minutos">
                            Duración en minutos
                        </label>

                        <input
                            type="number"
                            id="duracion_minutos"
                            name="duracion_minutos"
                            min="1"
                            required
                            value="<?= $formEditar
                                ? (int) $servicioEditar['duracion_minutos']
                                : 60 ?>"
                        >

                    </div>


                    <div class="service-admin-field">

                        <label for="precio">
                            Precio
                        </label>

                        <input
                            type="number"
                            id="precio"
                            name="precio"
                            min="0"
                            step="0.01"
                            required
                            value="<?= $formEditar
                                ? e($servicioEditar['precio'])
                                : '0' ?>"
                        >

                    </div>


                    <div class="service-admin-field">

                        <label for="estado">
                            Estado
                        </label>

                        <?php
                        $estadoActual =
                            $formEditar
                                ? $servicioEditar['estado']
                                : 'activo';
                        ?>

                        <select
                            id="estado"
                            name="estado"
                            required
                        >

                            <option
                                value="activo"
                                <?= $estadoActual === 'activo'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Activo
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


                    <div class="service-admin-field">

                        <label>
                            Profesional
                        </label>

                        <label class="service-admin-check">

                            <input
                                type="checkbox"
                                name="requiere_profesional"
                                value="1"
                                <?= (
                                    !$formEditar ||
                                    (int) $servicioEditar['requiere_profesional'] === 1
                                )
                                    ? 'checked'
                                    : '' ?>
                            >

                            Requiere profesional para la atención

                        </label>

                    </div>


                    <div class="service-admin-field full">

                        <label for="descripcion">
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            name="descripcion"
                            placeholder="Describa el servicio..."
                        ><?= $formEditar
                            ? e($servicioEditar['descripcion'] ?? '')
                            : '' ?></textarea>

                    </div>


                    <div class="service-admin-form-actions">

                        <a
                            href="<?= SITE_URL ?>/pages/admin/servicios.php"
                            class="admin-secondary-button"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="admin-primary-button"
                        >
                            <?= $formEditar
                                ? 'Guardar cambios'
                                : 'Crear servicio' ?>
                        </button>

                    </div>

                </form>

            </section>

        <?php endif; ?>


        <section class="service-admin-stats">

            <article class="service-admin-stat">

                <div class="service-admin-stat-icon">
                    ✦
                </div>

                <div>

                    <strong>
                        <?= $totalServicios ?>
                    </strong>

                    <span>
                        Servicios
                    </span>

                </div>

            </article>


            <article class="service-admin-stat">

                <div class="service-admin-stat-icon">
                    ✓
                </div>

                <div>

                    <strong>
                        <?= $serviciosActivos ?>
                    </strong>

                    <span>
                        Activos
                    </span>

                </div>

            </article>


            <article class="service-admin-stat">

                <div class="service-admin-stat-icon">
                    ♟
                </div>

                <div>

                    <strong>
                        <?= $requierenProfesional ?>
                    </strong>

                    <span>
                        Requieren profesional
                    </span>

                </div>

            </article>

        </section>


        <section class="services-admin-toolbar">

            <input
                type="search"
                id="buscarServicio"
                placeholder="Buscar servicio..."
                autocomplete="off"
            >

            <select id="filtroCategoria">

                <option value="todos">
                    Todas las categorías
                </option>

                <?php foreach ($categorias as $categoria): ?>

                    <option
                        value="<?= (int) $categoria['id'] ?>"
                    >
                        <?= e($categoria['nombre']) ?>
                    </option>

                <?php endforeach; ?>

            </select>


            <select id="filtroEstado">

                <option value="todos">
                    Todos los estados
                </option>

                <option value="activo">
                    Activos
                </option>

                <option value="inactivo">
                    Inactivos
                </option>

            </select>

        </section>


        <section
            class="services-admin-list"
            id="listaServicios"
        >

            <?php if (!$servicios): ?>

                <div class="service-admin-empty">

                    <h2>
                        No hay servicios registrados
                    </h2>

                    <p>
                        Cree el primer servicio para comenzar.
                    </p>

                </div>

            <?php else: ?>

                <?php foreach ($servicios as $servicio): ?>

                    <article
                        class="service-admin-card <?= $servicio['estado'] === 'inactivo'
                            ? 'is-inactive'
                            : '' ?>"
                        data-name="<?= e(
                            strtolower($servicio['nombre'])
                        ) ?>"
                        data-category="<?= (int) $servicio['categoria_id'] ?>"
                        data-status="<?= e($servicio['estado']) ?>"
                    >

                        <div class="service-admin-card-top">

                            <span class="service-admin-category">
                                <?= e($servicio['categoria_nombre']) ?>
                            </span>

                            <span
                                class="service-admin-status <?= $servicio['estado'] === 'activo'
                                    ? 'active'
                                    : 'inactive' ?>"
                            >
                                <?= $servicio['estado'] === 'activo'
                                    ? 'Activo'
                                    : 'Inactivo' ?>
                            </span>

                        </div>


                        <h2>
                            <?= e($servicio['nombre']) ?>
                        </h2>


                        <p class="service-admin-description">

                            <?= !empty($servicio['descripcion'])
                                ? e($servicio['descripcion'])
                                : 'Sin descripción registrada.' ?>

                        </p>


                        <div class="service-admin-details">

                            <div class="service-admin-detail">

                                <span>
                                    Duración
                                </span>

                                <strong>
                                    <?= (int) $servicio['duracion_minutos'] ?>
                                    min.
                                </strong>

                            </div>


                            <div class="service-admin-detail">

                                <span>
                                    Precio
                                </span>

                                <strong>
                                    $
                                    <?= number_format(
                                        (float) $servicio['precio'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>

                            </div>

                        </div>


                        <div class="service-admin-professional">

                            <?= (int) $servicio['requiere_profesional'] === 1
                                ? '✓ Requiere profesional'
                                : '• No requiere profesional' ?>

                        </div>


                        <div class="service-admin-card-actions">

                            <a
                                href="<?= SITE_URL ?>/pages/admin/servicios.php?editar=<?= (int) $servicio['id'] ?>"
                            >
                                Editar
                            </a>


                            <form
                                method="POST"
                                style="display:flex; flex:1;"
                            >

                                <input
                                    type="hidden"
                                    name="accion"
                                    value="cambiar_estado"
                                >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $servicio['id'] ?>"
                                >

                                <input
                                    type="hidden"
                                    name="estado"
                                    value="<?= $servicio['estado'] === 'activo'
                                        ? 'inactivo'
                                        : 'activo' ?>"
                                >

                                <button type="submit">

                                    <?= $servicio['estado'] === 'activo'
                                        ? 'Desactivar'
                                        : 'Activar' ?>

                                </button>

                            </form>

                        </div>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </section>

    </div>

</main>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const buscador =
        document.getElementById('buscarServicio');

    const filtroCategoria =
        document.getElementById('filtroCategoria');

    const filtroEstado =
        document.getElementById('filtroEstado');

    const tarjetas =
        document.querySelectorAll('.service-admin-card');


    function filtrarServicios() {

        const texto =
            (buscador?.value || '')
                .trim()
                .toLowerCase();

        const categoria =
            filtroCategoria?.value || 'todos';

        const estado =
            filtroEstado?.value || 'todos';


        tarjetas.forEach(function (tarjeta) {

            const nombre =
                tarjeta.dataset.name || '';

            const categoriaTarjeta =
                tarjeta.dataset.category || '';

            const estadoTarjeta =
                tarjeta.dataset.status || '';


            tarjeta.style.display =
                nombre.includes(texto) &&
                (categoria === 'todos' ||
                    categoriaTarjeta === categoria) &&
                (estado === 'todos' ||
                    estadoTarjeta === estado)
                    ? ''
                    : 'none';

        });

    }


    buscador?.addEventListener(
        'input',
        filtrarServicios
    );

    filtroCategoria?.addEventListener(
        'change',
        filtrarServicios
    );

    filtroEstado?.addEventListener(
        'change',
        filtrarServicios
    );

});

</script>


<?php

require_once __DIR__ . '/../../includes/footer.php';

?>