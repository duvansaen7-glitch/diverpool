<?php

require_once __DIR__ . '/../../config/database.php';

try {
    $stmt = $pdo->query("SELECT DATABASE() AS base_datos");
    $resultado = $stmt->fetch();

    echo "CONEXIÓN EXITOSA\n";
    echo "Base de datos: " . $resultado['base_datos'] . "\n";

} catch (PDOException $e) {
    echo "ERROR DE CONEXIÓN\n";
    echo $e->getMessage() . "\n";
}
