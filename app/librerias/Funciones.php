<?php
if (!function_exists('verificarRol')) {
    function verificarRol($rolesPermitidos = [])
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['id_rol'], $rolesPermitidos)) {
            $_SESSION['errorPermiso'] = "No tienes permisos para acceder a esta sección.";
            header("Location: " . RUTA_URL . "/logins");
            exit();
        }
    }
}

if (!function_exists('verificarSesionActiva')) {
    function verificarSesionActiva()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            $_SESSION['errorPermiso'] = "Debes iniciar sesión para acceder.";
            header("Location: " . RUTA_URL . "/logins");
            exit;
        }
    }
}


// if (!function_exists('verificarRol')) {
//     function verificarRol($rolesPermitidos = [])
//     {
//         session_start();
//         if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['id_rol'], $rolesPermitidos)) {
//             $_SESSION['errorPermiso'] = "No tienes permisos para acceder a esta sección.";
//             header("Location: " . RUTA_URL . "/logins");
//             exit();
//         }
//     }
// }

// if (!function_exists('verificarSesionActiva')) {
//     function verificarSesionActiva()
//     {
//         session_start();
//         if (!isset($_SESSION['usuario'])) {
//             $_SESSION['errorPermiso'] = "Debes iniciar sesión para acceder.";
//             header("Location: " . RUTA_URL . "/logins");
//             exit;
//         }
//     }
// }


if (!function_exists('test_input')) {
    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

if (!function_exists('checkVAr2')) {
    function checkVAr2($v)
    {
        return isset($v) && !empty($v);
    }
}

if (!function_exists('obtenerIdioma')) {
    function obtenerIdioma()
    {
        // idiomas
        $idiomas_permitidos = ['espanol', 'ingles', 'catalan', 'euskera'];

        // establecer español por defecto
        if (!isset($_COOKIE['idioma']) || !in_array($_COOKIE['idioma'], $idiomas_permitidos)) {
            setcookie('idioma', 'espanol', time() + (86400 * 1), "/", "", true, true);
            return 'espanol';
        }

        return $_COOKIE['idioma'];
    }
}



?>