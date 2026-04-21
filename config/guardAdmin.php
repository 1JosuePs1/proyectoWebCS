<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function ObtenerRutaNavbarAdmin()
{
    return $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Views/components/navAdmin.php";
}

function RequerirAdmin(
    $redireccionNoAdmin = '/proyectoWebCS/Views/Home/Home.php',
    $claveMensaje = 'error_acceso',
    $mensaje = 'No tienes permisos para entrar a esta seccion.'
) {
    if (!isset($_SESSION['idUsuario'])) {
        header('Location: /proyectoWebCS/Views/Registro/login.php');
        exit();
    }

    if (intval($_SESSION['idRol'] ?? 0) !== 1) {
        $_SESSION[$claveMensaje] = $mensaje;
        header('Location: ' . $redireccionNoAdmin);
        exit();
    }
}

function RequerirAdminOculto()
{
    if (!isset($_SESSION['idUsuario'])) {
        header('Location: /proyectoWebCS/Views/Registro/login.php');
        exit();
    }

    if (intval($_SESSION['idRol'] ?? 0) !== 1) {
        http_response_code(404);
        exit('404 Not Found');
    }
}
