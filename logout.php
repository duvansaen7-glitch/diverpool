<?php

require_once __DIR__ . '/includes/funciones.php';

/*
 * Cerrar completamente la sesión.
 */
cerrarSesionUsuario();

/*
 * Volver al inicio.
 */
redirect('index.php');