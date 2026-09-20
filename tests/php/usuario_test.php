<?php

require_once __DIR__ . '/../../models/Usuario.php';

try {
    $usuario = new Usuario();

    $resultado = $usuario->buscarPorCorreo('correo-inexistente@prueba.com');

    if ($resultado === null) {
        echo "MODELO Usuario FUNCIONANDO\n";
        echo "La consulta por correo se ejecutó correctamente.\n";
        echo "No existe un usuario con ese correo.\n";
    } else {
        echo "Se encontró un usuario:\n";
        print_r($resultado);
    }

} catch (Throwable $e) {
    echo "ERROR\n";
    echo $e->getMessage() . "\n";
}

