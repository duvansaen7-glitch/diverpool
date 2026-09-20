<?php

/**
 * Funciones generales de Diverpool
 */

/**
 * Escapa texto para mostrarlo de forma segura en HTML.
 */
function e($valor): string
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

/**
 * Redirecciona a una página.
 */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/**
 * Comprueba si existe una sesión iniciada.
 */
function usuarioAutenticado(): bool
{
    return isset($_SESSION['usuario_id']);
}

/**
 * Obtiene el usuario actualmente autenticado.
 */
function usuarioId(): ?int
{
    return isset($_SESSION['usuario_id'])
        ? (int) $_SESSION['usuario_id']
        : null;
}