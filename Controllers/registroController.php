<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/registroModel.php";

function VerificarCorreoExistenteController($correo)
{
    return VerificarCorreoExistenteModel($correo);
}

function RegistrarUsuarioController($nombre, $correo, $password)
{
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    return RegistrarUsuarioModel($nombre, $correo, $passwordHash);
}

function ValidarAccesoController($correo)
{
    return ValidarAccesoModel($correo);
}

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombreUsuario'] ?? '');
    $correo = trim($_POST['correoUsuario'] ?? '');
    $password = trim($_POST['passwordUsuario'] ?? '');
    $confirmar = trim($_POST['confirmPassword'] ?? '');

    if (empty($nombre) || empty($correo) || empty($password) || empty($confirmar)) {
        $_SESSION['error_registro'] = "Todos los campos son obligatorios";
        header("Location: ../Views/Registro/registro.php");
        exit();
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_registro'] = "El correo electrónico no es válido";
        header("Location: ../Views/Registro/registro.php");
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['error_registro'] = "La contraseña debe tener al menos 8 caracteres";
        header("Location: ../Views/Registro/registro.php");
        exit();
    }

    if ($password !== $confirmar) {
        $_SESSION['error_registro'] = "Las contraseñas no coinciden";
        header("Location: ../Views/Registro/registro.php");
        exit();
    }

    if (VerificarCorreoExistenteController($correo)) {
        $_SESSION['error_registro'] = "Ese correo ya está registrado";
        header("Location: ../Views/Registro/registro.php");
        exit();
    }

    $resultado = RegistrarUsuarioController($nombre, $correo, $password);

    if ($resultado) {
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['error_registro'] = "Error al registrar usuario";
        header("Location: ../Views/Registro/registro.php");
        exit();
    }
}