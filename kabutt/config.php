<?php
// Configuración de sesión
ini_set('session.gc_maxlifetime', 1800); // 30 minutos
session_set_cookie_params(1800); // 30 minutos

// Configuración básica del sitio
define('SITE_NAME', 'Kabutt');
define('SITE_URL', 'http://localhost/kabutt');
define('DEFAULT_CATEGORY', 'running');

// Verificar y regenerar ID de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();

    // Regenerar ID cada 5 minutos para mayor seguridad
    if (!isset($_SESSION['last_regeneration'])) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}
?>