<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/registroModel.php";

function LoginController($correo, $password)
{
    $usuario = ValidarAccesoModel($correo);

    if ($usuario && password_verify($password, $usuario['passwordUsuario'])) {
        return $usuario;
    }
    return null;
}

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correoUsuario'] ?? '');
    $password = trim($_POST['passwordUsuario'] ?? '');

    if (empty($correo) || empty($password)) {
        $_SESSION['error_login'] = "Todos los campos son obligatorios";
        header("Location: ../Views/Registro/login.php");
        exit();
    }

    $usuario = LoginController($correo, $password);

    if ($usuario) {
        $_SESSION['idUsuario'] = $usuario['idUsuario'];
        $_SESSION['nombreCompleto'] = $usuario['nombreCompleto'];
        $_SESSION['idRol'] = $usuario['idRol'];

        if (intval($usuario['idRol'] ?? 0) === 1) {
            header("Location: ../Views/Admin/dashboard.php");
        } else {
            header("Location: ../Views/Home/Home.php");
        }
        exit();
    } else {
        $_SESSION['error_login'] = "Usuario no encontrado o contraseña incorrecta";
        header("Location: ../Views/Registro/login.php");
        exit();
    }
}