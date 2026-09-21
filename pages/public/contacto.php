<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/funciones.php';

$pageTitle = 'Contacto | Diverpool Mascotas';
$pageDescription = 'Póngase en contacto con Diverpool Mascotas para resolver sus dudas y recibir información sobre nuestros servicios.';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';

?>

<main>

    <!-- Encabezado -->
    <section class="hero">

        <div class="container">

            <div class="hero-copy">

                <span class="eyebrow">CONTACTO</span>

                <h1>
                    Estamos aquí para
                    <span>ayudarle.</span>
                </h1>

                <p>
                    Si tiene alguna pregunta sobre nuestros servicios,
                    reservas o atención para su mascota, puede ponerse
                    en contacto con nosotros.
                </p>

            </div>

        </div>

    </section>


    <!-- Información de contacto -->
    <section class="section">

        <div class="container">

            <div class="section-heading centered">

                <span class="eyebrow">DIVERPOOL MASCOTAS</span>

                <h2>
                    Póngase en contacto con nosotros.
                </h2>

                <p>
                    Encuentre nuestros datos de contacto o envíenos
                    un mensaje.
                </p>

            </div>


            <div class="service-grid">

                <!-- Correo -->
                <article class="service-card">

                    <div class="service-image service-consultas">
                        <span>@</span>
                    </div>

                    <div class="service-body">

                        <h3>Correo electrónico</h3>

                        <p>
                            contacto@diverpool.com
                        </p>

                        <a href="mailto:contacto@diverpool.com">
                            Enviar correo
                        </a>

                    </div>

                </article>


                <!-- Teléfono -->
                <article class="service-card">

                    <div class="service-image service-veterinaria">
                        <span>☎</span>
                    </div>

                    <div class="service-body">

                        <h3>Teléfono</h3>

                        <p>
                            Comuníquese con nuestro equipo para
                            recibir información sobre los servicios.
                        </p>

                    </div>

                </article>


                <!-- Ubicación -->
                <article class="service-card">

                    <div class="service-image service-guarderia">
                        <span>⌂</span>
                    </div>

                    <div class="service-body">

                        <h3>Ubicación</h3>

                        <p>
                            Bogotá, Colombia
                        </p>

                    </div>

                </article>

            </div>

        </div>

    </section>


    <!-- Formulario -->
    <section class="section">

        <div class="container">

            <div class="about-card">

                <div>

                    <span class="eyebrow">ESCRÍBANOS</span>

                    <h2>
                        ¿Tiene alguna pregunta?
                    </h2>

                    <p>
                        Complete el siguiente formulario y podremos
                        utilizar esta información para atender su solicitud.
                    </p>

                </div>

            </div>


            <form
                class="contact-form"
                action="#"
                method="post"
            >

                <div class="form-grid">

                    <div class="form-group">

                        <label for="nombre">
                            Nombre
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            placeholder="Ingrese su nombre"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="correo">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            placeholder="correo@ejemplo.com"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="telefono">
                            Teléfono
                        </label>

                        <input
                            type="tel"
                            id="telefono"
                            name="telefono"
                            placeholder="Ingrese su teléfono"
                        >

                    </div>


                    <div class="form-group">

                        <label for="asunto">
                            Asunto
                        </label>

                        <input
                            type="text"
                            id="asunto"
                            name="asunto"
                            placeholder="¿En qué podemos ayudarle?"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="mensaje">
                        Mensaje
                    </label>

                    <textarea
                        id="mensaje"
                        name="mensaje"
                        rows="6"
                        placeholder="Escriba su mensaje..."
                        required
                    ></textarea>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Enviar mensaje
                </button>

            </form>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
