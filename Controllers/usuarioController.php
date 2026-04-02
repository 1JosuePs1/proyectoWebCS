<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/usuarioModel.php";

function ObtenerUsuarioPorIdController($idUsuario)
{
    return ObtenerUsuarioPorIdModel($idUsuario);
}

function ActualizarUsuarioController($idUsuario, $nombreCompleto, $passwordNueva)
{
    $passwordHash = null;
    if (!empty($passwordNueva)) {
        $passwordHash = password_hash($passwordNueva, PASSWORD_DEFAULT);
    }
    return ActualizarUsuarioModel($idUsuario, $nombreCompleto, $passwordHash);
}

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['idUsuario'])) {
        header("Location: /proyectoWebCS/index.php");
        exit();
    }

    $idUsuario = $_SESSION['idUsuario'];
    $nombreCompleto = trim($_POST['nombreCompleto'] ?? '');
    $passwordNueva = trim($_POST['passwordNueva'] ?? '');
    $confirmarPassword = trim($_POST['confirmarPassword'] ?? '');

    if (empty($nombreCompleto)) {
        $_SESSION['error_usuario'] = "El nombre completo es obligatorio";
        header("Location: ../Views/usuario/usuario.php");
        exit();
    }

    if (!empty($passwordNueva)) {
        if (strlen($passwordNueva) < 8) {
            $_SESSION['error_usuario'] = "La contraseña debe tener al menos 8 caracteres";
            header("Location: ../Views/usuario/usuario.php");
            exit();
        }
        if ($passwordNueva !== $confirmarPassword) {
            $_SESSION['error_usuario'] = "Las contraseñas no coinciden";
            header("Location: ../Views/usuario/usuario.php");
            exit();
        }
    }

    $resultado = ActualizarUsuarioController($idUsuario, $nombreCompleto, $passwordNueva);

    if ($resultado) {
        $_SESSION['nombreCompleto'] = $nombreCompleto;
        header("Location: ../Views/usuario/usuario.php?actualizado=1");
        exit();
    } else {
        $_SESSION['error_usuario'] = "Error al actualizar el perfil";
        header("Location: ../Views/usuario/usuario.php");
        exit();
    }
}
