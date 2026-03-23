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
        die("El nombre completo es obligatorio");
    }

    if (!empty($passwordNueva)) {
        if (strlen($passwordNueva) < 8) {
            die("La contraseña debe tener al menos 8 caracteres");
        }
        if ($passwordNueva !== $confirmarPassword) {
            die("Las contraseñas no coinciden");
        }
    }

    $resultado = ActualizarUsuarioController($idUsuario, $nombreCompleto, $passwordNueva);

    if ($resultado) {
        $_SESSION['nombreCompleto'] = $nombreCompleto;
        header("Location: ../Views/usuario/usuario.php?actualizado=1");
        exit();
    } else {
        die("Error al actualizar el perfil");
    }
}
