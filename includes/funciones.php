<?php

/**
 * Funciones generales de Diverpool
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
 * Obtiene el ID del usuario actualmente autenticado.
 */
function usuarioId(): ?int
{
    return isset($_SESSION['usuario_id'])
        ? (int) $_SESSION['usuario_id']
        : null;
}

/**
 * Obtiene el rol del usuario actualmente autenticado.
 */
function usuarioRol(): ?string
{
    return $_SESSION['usuario_rol'] ?? null;
}

/**
 * Guarda los datos básicos del usuario en la sesión.
 */
function iniciarSesionUsuario(array $usuario): void
{
    session_regenerate_id(true);

    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['usuario_rol_id'] = (int) $usuario['rol_id'];
    $_SESSION['usuario_rol'] = $usuario['rol_nombre'];
    $_SESSION['usuario_nombres'] = $usuario['nombres'];
    $_SESSION['usuario_apellidos'] = $usuario['apellidos'];
    $_SESSION['usuario_correo'] = $usuario['correo'];
}

/**
 * Cierra la sesión del usuario.
 */
function cerrarSesionUsuario(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Comprueba si el usuario tiene un rol específico.
 */
function tieneRol(string $rol): bool
{
    return usuarioRol() === $rol;
}