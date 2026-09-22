<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../models/Mascota.php';
require_once __DIR__ . '/../../models/Servicio.php';

if (!usuarioAutenticado() || !tieneRol('Cliente')) {
    redirect(SITE_URL . '/login.php');
}

$usuarioId = usuarioId();

$mascotaModel = new Mascota();
$servicioModel = new Servicio();

$mascotas = $mascotaModel->obtenerPorUsuario($usuarioId);
$servicios = $servicioModel->obtenerActivos();

$mascotaSeleccionada = isset($_GET['mascota_id'])
    ? (int) $_GET['mascota_id']
    : 0;

/*
 * Verificar que la mascota seleccionada
 * realmente pertenezca al usuario.
 */
if ($mascotaSeleccionada > 0) {

    $mascotaExiste = false;

    foreach ($mascotas as $mascota) {

        if ((int) $mascota['id'] === $mascotaSeleccionada) {
            $mascotaExiste = true;
            break;
        }
    }

    if (!$mascotaExiste) {
        $mascotaSeleccionada = 0;
    }
}

$pageTitle = 'Agendar servicio | ' . SITE_NAME;
$pageDescription = 'Agende un servicio para su mascota.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

    <section class="section">

        <div class="container">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        RESERVAS
                    </span>

                    <h1>
                        Agendar servicio
                    </h1>

                    <p>
                        Seleccione su mascota, servicio, fecha y horario.
                    </p>

                </div>

            </div>


            <?php if (empty($mascotas)): ?>

                <div class="empty-state">

                    <div class="empty-state-icon">
                        🐾
                    </div>

                    <h2>
                        Primero debe registrar una mascota
                    </h2>

                    <p>
                        Para agendar un servicio necesitamos conocer
                        cuál de sus mascotas recibirá la atención.
                    </p>

                    <a href="<?= SITE_URL ?>/pages/usuario/mascota_crear.php" class="btn btn-primary">
                        Registrar mascota
                    </a>

                </div>

            <?php else: ?>

                <div class="reserva-form-card">

                    <!-- PROGRESO -->

                    <div class="reserva-progress">

                        <div class="reserva-step active">
                            <span>1</span>
                            <strong>Mascota</strong>
                        </div>

                        <div class="reserva-step">
                            <span>2</span>
                            <strong>Servicio</strong>
                        </div>

                        <div class="reserva-step">
                            <span>3</span>
                            <strong>Horario</strong>
                        </div>

                        <div class="reserva-step">
                            <span>4</span>
                            <strong>Confirmación</strong>
                        </div>

                    </div>


                    <form method="POST" action="" id="reservaForm">

                        <!-- ==================================================
                             MASCOTA
                             ================================================== -->

                        <div class="reserva-form-section">

                            <h2>
                                Seleccione su mascota
                            </h2>

                            <p class="form-help">
                                Seleccione la mascota que recibirá el servicio.
                            </p>

                            <div class="mascotas-reserva-grid">

                                <?php foreach ($mascotas as $mascota): ?>

                                    <label class="mascota-reserva-option">

                                        <input type="radio" name="mascota_id" value="<?= (int) $mascota['id'] ?>"
                                            <?= $mascotaSeleccionada === (int) $mascota['id']
                                                ? 'checked'
                                                : '' ?> required>

                                        <div class="mascota-reserva-card">

                                            <?php if (!empty($mascota['foto'])): ?>

                                                <img src="<?= SITE_URL . '/' . e($mascota['foto']) ?>"
                                                    alt="<?= e($mascota['nombre']) ?>">

                                            <?php else: ?>

                                                <div class="mascota-reserva-avatar">
                                                    🐾
                                                </div>

                                            <?php endif; ?>


                                            <div>

                                                <strong>
                                                    <?= e($mascota['nombre']) ?>
                                                </strong>

                                                <?php if (!empty($mascota['especie_nombre'])): ?>

                                                    <span>
                                                        <?= e($mascota['especie_nombre']) ?>
                                                    </span>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    </label>

                                <?php endforeach; ?>

                            </div>

                        </div>


                        <!-- ==================================================
                             SERVICIO
                             ================================================== -->

                        <div class="reserva-form-section">

                            <h2>
                                Servicio
                            </h2>

                            <p class="form-help">
                                Seleccione el servicio que desea agendar.
                            </p>


                            <div class="reserva-empty-info">

                                <span>
                                    🩺
                                </span>

                                <div>

                                    <div class="service-selection">

                                        <?php if (empty($servicios)): ?>

                                            <div class="reservation-placeholder">

                                                <div class="reservation-placeholder-icon">
                                                    🩺
                                                </div>

                                                <div>

                                                    <strong>
                                                        Sin servicios disponibles
                                                    </strong>

                                                    <p>
                                                        Actualmente no hay servicios activos para agendar.
                                                    </p>

                                                </div>

                                            </div>

                                        <?php else: ?>

                                            <div class="reservation-service-grid">

                                                <?php foreach ($servicios as $servicio): ?>

                                                    <label class="reservation-service-card">

                                                        <input type="radio" name="servicio_id" value="<?= (int) $servicio['id'] ?>"
                                                            required>

                                                        <span class="reservation-service-content">

                                                            <span class="reservation-service-category">
                                                                <?= e($servicio['categoria_nombre']) ?>
                                                            </span>

                                                            <strong class="reservation-service-name">
                                                                <?= e($servicio['nombre']) ?>
                                                            </strong>

                                                            <?php if (!empty($servicio['descripcion'])): ?>

                                                                <span class="reservation-service-description">
                                                                    <?= e($servicio['descripcion']) ?>
                                                                </span>

                                                            <?php endif; ?>

                                                            <span class="reservation-service-details">

                                                                <span>
                                                                    <?= (int) $servicio['duracion_minutos'] ?>
                                                                    minutos
                                                                </span>

                                                                <span>
                                                                    $
                                                                    <?= number_format(
                                                                        (float) $servicio['precio'],
                                                                        0,
                                                                        ',',
                                                                        '.'
                                                                    ) ?>
                                                                </span>

                                                            </span>

                                                        </span>

                                                    </label>

                                                <?php endforeach; ?>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- ==================================================
                             FECHA Y HORARIO
                             ================================================== -->

                        <div class="reserva-form-section">

                            <h2>
                                Fecha y horario
                            </h2>

                            <p class="form-help">
                                Seleccione una fecha para consultar los horarios
                                disponibles.
                            </p>


                            <div class="reserva-fecha-container">

                                <label for="fecha_reserva">
                                    Fecha de la reserva
                                </label>

                                <input type="date" id="fecha_reserva" name="fecha" min="<?= date('Y-m-d') ?>" required>

                            </div>


                            <div id="disponibilidadContainer" class="disponibilidad-container">

                                <div class="reserva-empty-info">

                                    <span>
                                        📅
                                    </span>

                                    <div>

                                        <strong>
                                            Seleccione una fecha
                                        </strong>

                                        <p>
                                            Primero seleccione un servicio
                                            y una fecha para consultar los
                                            horarios disponibles.
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <input type="hidden" name="hora_inicio" id="hora_inicio" value="">

                            <input type="hidden" name="hora_fin" id="hora_fin" value="">

                        </div>


                        <!-- ==================================================
                             BOTONES
                             ================================================== -->

                        <div class="reserva-form-actions">

                            <a href="<?= SITE_URL ?>/pages/usuario/reservas.php" class="btn btn-outline">
                                Cancelar
                            </a>

                            <button type="button" id="btnContinuarReserva" class="btn btn-primary" disabled>
                                Continuar
                            </button>

                        </div>

                    </form>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>


<script>

    document.addEventListener('DOMContentLoaded', () => {

        /*
         * =========================================================
         * ELEMENTOS
         * =========================================================
         */

        const servicioInputs = document.querySelectorAll(
            'input[name="servicio_id"]'
        );

        const mascotaInputs = document.querySelectorAll(
            'input[name="mascota_id"]'
        );

        const fechaInput = document.getElementById(
            'fecha_reserva'
        );

        const disponibilidadContainer = document.getElementById(
            'disponibilidadContainer'
        );

        const horaInicioInput = document.getElementById(
            'hora_inicio'
        );

        const horaFinInput = document.getElementById(
            'hora_fin'
        );

        const btnContinuar = document.getElementById(
            'btnContinuarReserva'
        );

        const form = document.getElementById(
            'reservaForm'
        );


        /*
         * =========================================================
         * CONFIGURACIÓN
         * =========================================================
         */

        const sedeId = 1;

        const API_URL =
            'http://localhost:8080/api/reservas/disponibilidad';


        /*
         * =========================================================
         * ESTADO
         * =========================================================
         */

        let servicioSeleccionado = null;

        let horarioSeleccionado = null;


        /*
         * =========================================================
         * SERVICIO
         * =========================================================
         */

        servicioInputs.forEach(input => {

            input.addEventListener('change', () => {

                servicioSeleccionado = input.value;

                horarioSeleccionado = null;

                horaInicioInput.value = '';
                horaFinInput.value = '';

                actualizarBotonContinuar();


                /*
                 * Si ya existe una fecha seleccionada,
                 * volvemos a consultar la disponibilidad.
                 */

                if (fechaInput.value) {
                    consultarDisponibilidad();
                }

            });

        });


        /*
         * =========================================================
         * MASCOTA
         * =========================================================
         */

        mascotaInputs.forEach(input => {

            input.addEventListener('change', () => {

                actualizarBotonContinuar();

            });

        });


        /*
         * =========================================================
         * FECHA
         * =========================================================
         */

        fechaInput.addEventListener('change', () => {

            horarioSeleccionado = null;

            horaInicioInput.value = '';
            horaFinInput.value = '';

            actualizarBotonContinuar();


            if (!servicioSeleccionado) {

                disponibilidadContainer.innerHTML = `

                <div class="reserva-empty-info">

                    <span>
                        ⚠️
                    </span>

                    <div>

                        <strong>
                            Seleccione un servicio
                        </strong>

                        <p>
                            Debe seleccionar un servicio antes
                            de consultar los horarios.
                        </p>

                    </div>

                </div>

            `;

                return;
            }


            consultarDisponibilidad();

        });


        /*
         * =========================================================
         * CONSULTAR DISPONIBILIDAD
         * =========================================================
         */

        async function consultarDisponibilidad() {

            const fecha = fechaInput.value;


            if (!servicioSeleccionado || !fecha) {
                return;
            }


            horarioSeleccionado = null;

            horaInicioInput.value = '';
            horaFinInput.value = '';

            actualizarBotonContinuar();


            disponibilidadContainer.innerHTML = `

            <div class="reserva-loading">

                <span>
                    Consultando disponibilidad...
                </span>

            </div>

        `;


            const url =
                API_URL +
                '?servicio_id=' +
                encodeURIComponent(servicioSeleccionado) +
                '&sede_id=' +
                encodeURIComponent(sedeId) +
                '&fecha=' +
                encodeURIComponent(fecha);


            try {

                const response = await fetch(url, {

                    method: 'GET',

                    headers: {
                        'Accept': 'application/json'
                    }

                });


                if (!response.ok) {

                    throw new Error(
                        'Error HTTP: ' + response.status
                    );

                }


                const data = await response.json();


                if (!data.ok) {

                    throw new Error(
                        'La API no pudo consultar la disponibilidad.'
                    );

                }


                mostrarHorarios(data.horarios);


            } catch (error) {

                console.error(
                    'Error consultando disponibilidad:',
                    error
                );


                disponibilidadContainer.innerHTML = `

                <div class="reserva-empty-info">

                    <span>
                        ⚠️
                    </span>

                    <div>

                        <strong>
                            No se pudo consultar la disponibilidad
                        </strong>

                        <p>
                            Verifique que la API Java esté
                            ejecutándose en el puerto 8080.
                        </p>

                    </div>

                </div>

            `;

            }

        }


        /*
         * =========================================================
         * MOSTRAR HORARIOS
         * =========================================================
         */

        function mostrarHorarios(horarios) {


            if (
                !Array.isArray(horarios) ||
                horarios.length === 0
            ) {

                disponibilidadContainer.innerHTML = `

                <div class="reserva-empty-info">

                    <span>
                        📅
                    </span>

                    <div>

                        <strong>
                            No hay horarios disponibles
                        </strong>

                        <p>
                            No existen horarios disponibles para
                            este servicio en la fecha seleccionada.
                        </p>

                    </div>

                </div>

            `;

                return;

            }


            const grid = document.createElement('div');

            grid.className = 'horarios-disponibles-grid';


            horarios.forEach(horario => {


                const button = document.createElement('button');

                button.type = 'button';

                button.className = 'horario-disponible';


                button.dataset.horaInicio =
                    horario.horaInicio;

                button.dataset.horaFin =
                    horario.horaFin;


                const horaInicio =
                    horario.horaInicio.substring(0, 5);

                const horaFin =
                    horario.horaFin.substring(0, 5);


                const profesionales =
                    horario.profesionalesDisponibles;


                const textoProfesionales =
                    profesionales === 1
                        ? '1 profesional disponible'
                        : profesionales + ' profesionales disponibles';


                button.innerHTML = `

                <strong>
                    ${horaInicio} - ${horaFin}
                </strong>

                <span>
                    ${textoProfesionales}
                </span>

            `;


                /*
                 * Seleccionar horario
                 */

                button.addEventListener('click', () => {


                    document
                        .querySelectorAll('.horario-disponible')
                        .forEach(elemento => {

                            elemento.classList.remove(
                                'selected'
                            );

                        });


                    button.classList.add(
                        'selected'
                    );


                    horarioSeleccionado = horario;


                    horaInicioInput.value =
                        horario.horaInicio;

                    horaFinInput.value =
                        horario.horaFin;


                    actualizarBotonContinuar();

                });


                grid.appendChild(button);

            });


            disponibilidadContainer.innerHTML = `

            <div class="horarios-heading">

                <strong>
                    Horarios disponibles
                </strong>

                <span>
                    Seleccione uno
                </span>

            </div>

        `;


            disponibilidadContainer.appendChild(
                grid
            );

        }


        /*
         * =========================================================
         * BOTÓN CONTINUAR
         * =========================================================
         */

        function actualizarBotonContinuar() {


            const mascotaSeleccionada =
                document.querySelector(
                    'input[name="mascota_id"]:checked'
                );


            btnContinuar.disabled = !(
                mascotaSeleccionada &&
                servicioSeleccionado &&
                fechaInput.value &&
                horarioSeleccionado
            );

        }


        /*
         * =========================================================
         * CONTINUAR
         * =========================================================
         *
         * Todavía NO crea la reserva.
         *
         * En el siguiente paso conectaremos este botón
         * con el POST de la API Java.
         * =========================================================
         */

        btnContinuar.addEventListener('click', () => {

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (!horarioSeleccionado) {
                return;
            }

            mostrarConfirmacion();

        });


        function mostrarConfirmacion() {

            const mascotaSeleccionada =
                document.querySelector(
                    'input[name="mascota_id"]:checked'
                );

            const servicioSeleccionadoInput =
                document.querySelector(
                    'input[name="servicio_id"]:checked'
                );


            if (!mascotaSeleccionada || !servicioSeleccionadoInput) {
                return;
            }


            /*
             * Obtener nombres visibles
             */

            const mascotaCard =
                mascotaSeleccionada
                    .closest('.mascota-reserva-option');

            const mascotaNombre =
                mascotaCard
                    ?.querySelector('strong')
                    ?.textContent
                    .trim() || 'Mascota';


            const servicioCard =
                servicioSeleccionadoInput
                    .closest('.reservation-service-card');

            const servicioNombre =
                servicioCard
                    ?.querySelector('.reservation-service-name')
                    ?.textContent
                    .trim() || 'Servicio';


            const precioTexto =
                servicioCard
                    ?.querySelector(
                        '.reservation-service-details span:last-child'
                    )
                    ?.textContent
                    .trim() || 'Precio no disponible';


            const fecha =
                fechaInput.value;


            const fechaFormateada =
                formatearFecha(fecha);


            const horaInicio =
                horaInicioInput.value.substring(0, 5);


            const horaFin =
                horaFinInput.value.substring(0, 5);


            /*
             * Ocultar formulario
             */

            form.style.display = 'none';


            /*
             * Crear confirmación
             */

            const confirmacion =
                document.createElement('div');

            confirmacion.id =
                'confirmacionReserva';

            confirmacion.className =
                'confirmacion-reserva';


            confirmacion.innerHTML = `

        <div class="confirmacion-reserva-header">

            <span class="confirmacion-icon">
                ✓
            </span>

            <div>

                <span class="eyebrow">
                    RESERVA
                </span>

                <h2>
                    Confirme su reserva
                </h2>

                <p>
                    Revise los datos antes de continuar.
                </p>

            </div>

        </div>


        <div class="confirmacion-reserva-detalles">


            <div class="confirmacion-item">

                <span class="confirmacion-label">
                    Mascota
                </span>

                <strong>
                    ${escapeHtml(mascotaNombre)}
                </strong>

            </div>


            <div class="confirmacion-item">

                <span class="confirmacion-label">
                    Servicio
                </span>

                <strong>
                    ${escapeHtml(servicioNombre)}
                </strong>

            </div>


            <div class="confirmacion-item">

                <span class="confirmacion-label">
                    Fecha
                </span>

                <strong>
                    ${escapeHtml(fechaFormateada)}
                </strong>

            </div>


            <div class="confirmacion-item">

                <span class="confirmacion-label">
                    Horario
                </span>

                <strong>
                    ${horaInicio} - ${horaFin}
                </strong>

            </div>


            <div class="confirmacion-item">

                <span class="confirmacion-label">
                    Precio
                </span>

                <strong>
                    ${escapeHtml(precioTexto)}
                </strong>

            </div>


        </div>


        <div class="confirmacion-aviso">

            <span>
                ℹ️
            </span>

            <p>
                Al confirmar, volveremos a verificar que el horario
                siga disponible antes de crear la reserva.
            </p>

        </div>


        <div class="reserva-form-actions">

            <button
                type="button"
                id="btnVolverReserva"
                class="btn btn-outline"
            >
                Volver
            </button>

            <button
                type="button"
                id="btnConfirmarReserva"
                class="btn btn-primary"
            >
                Confirmar reserva
            </button>

        </div>

    `;


            form.parentNode.insertBefore(
                confirmacion,
                form.nextSibling
            );


            /*
             * Volver a editar
             */

            document
                .getElementById('btnVolverReserva')
                .addEventListener('click', () => {

                    confirmacion.remove();

                    form.style.display = '';

                });


            /*
             * Confirmar
             *
             * Por ahora NO hacemos el POST.
             * Lo conectaremos al endpoint Java en
             * el siguiente paso.
             */

            document
                .getElementById('btnConfirmarReserva')
                .addEventListener('click', async () => {

                    const btnConfirmar =
                        document.getElementById('btnConfirmarReserva');


                    /*
                     * Datos seleccionados
                     */

                    const mascotaId =
                        parseInt(
                            mascotaSeleccionada.value,
                            10
                        );

                    const servicioId =
                        parseInt(
                            servicioSeleccionadoInput.value,
                            10
                        );


                    /*
                     * Desactivar botón para evitar
                     * doble envío.
                     */

                    btnConfirmar.disabled = true;

                    btnConfirmar.textContent =
                        'Creando reserva...';


                    try {

                        const response = await fetch(
                            'http://localhost:8080/api/reservas',
                            {
                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },

                                body: JSON.stringify({

                                    usuarioId: <?= (int) $usuarioId ?>,

                                    mascotaId: mascotaId,

                                    servicioId: servicioId,

                                    sedeId: 1,

                                    fecha: fecha,

                                    horaInicio:
                                    horaInicioInput.value,

                                    horaFin:
                                    horaFinInput.value,

                                    observaciones: null

                    })
                    }
            );


            const data =
                await response.json();


            /*
             * Java respondió con error.
             */

            if (!response.ok || !data.ok) {

                throw new Error(
                    data.mensaje ||
                    'No fue posible crear la reserva.'
                );

            }


            /*
             * Reserva creada.
             */

            console.log(
                'Reserva creada:',
                data
            );


            /*
             * Mostrar resultado.
             */

            confirmacion.innerHTML = `

                <div class="confirmacion-reserva-header">

                    <span class="confirmacion-icon">
                        ✓
                    </span>

                    <div>

                        <span class="eyebrow">
                            RESERVA CREADA
                        </span>

                        <h2>
                            Reserva confirmada
                        </h2>

                        <p>
                            Su reserva fue registrada correctamente.
                        </p>

                    </div>

                </div>


                <div class="confirmacion-reserva-detalles">

                    <div class="confirmacion-item">

                        <span class="confirmacion-label">
                            Número de reserva
                        </span>

                        <strong>
                            #${data.reserva_id}
                        </strong>

                    </div>


                    <div class="confirmacion-item">

                        <span class="confirmacion-label">
                            Mascota
                        </span>

                        <strong>
                            ${escapeHtml(mascotaNombre)}
                        </strong>

                    </div>


                    <div class="confirmacion-item">

                        <span class="confirmacion-label">
                            Servicio
                        </span>

                        <strong>
                            ${escapeHtml(servicioNombre)}
                        </strong>

                    </div>


                    <div class="confirmacion-item">

                        <span class="confirmacion-label">
                            Fecha
                        </span>

                        <strong>
                            ${escapeHtml(fechaFormateada)}
                        </strong>

                    </div>


                    <div class="confirmacion-item">

                        <span class="confirmacion-label">
                            Horario
                        </span>

                        <strong>
                            ${horaInicio} - ${horaFin}
                        </strong>

                    </div>


                    <div class="confirmacion-item">

                        <span class="confirmacion-label">
                            Precio
                        </span>

                        <strong>
                            ${escapeHtml(precioTexto)}
                        </strong>

                    </div>

                </div>


                <div class="confirmacion-aviso">

                    <span>
                        ✓
                    </span>

                    <p>
                        La reserva quedó registrada y será gestionada
                        por el profesional correspondiente.
                    </p>

                </div>


                <div class="reserva-form-actions">

                    <a
                        href="<?= SITE_URL ?>/pages/usuario/reservas.php"
                        class="btn btn-outline"
                    >
                        Ver mis reservas
                    </a>

                    <a
                        href="<?= SITE_URL ?>/pages/usuario/nueva_reserva.php"
                        class="btn btn-primary"
                    >
                        Nueva reserva
                    </a>

                </div>

            `;


        } catch (error) {

            console.error(
                'Error creando reserva:',
                error
            );


            /*
             * Reactivar botón.
             */

            btnConfirmar.disabled = false;

            btnConfirmar.textContent =
                'Confirmar reserva';


            /*
             * Mostrar error.
             */

            alert(
                error.message ||
                'No fue posible crear la reserva.'
            );

        }

    });

        }


    /*
     * Formatear fecha
     */

    function formatearFecha(fecha) {

        const partes =
            fecha.split('-');

        if (partes.length !== 3) {
            return fecha;
        }

        return `${partes[2]}/${partes[1]}/${partes[0]}`;

    }


    /*
     * Evitar insertar texto HTML directamente.
     */

    function escapeHtml(texto) {

        const div =
            document.createElement('div');

        div.textContent =
            texto;

        return div.innerHTML;

    }


    /*
     * =========================================================
     * ESTADO INICIAL
     * =========================================================
     */

    actualizarBotonContinuar();

    });

</script>


<?php require_once __DIR__ . '/../../includes/footer.php'; ?>