<?php
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
        die("Todos los campos son obligatorios");
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("El correo electrónico no es válido");
    }

    if (strlen($password) < 8) {
        die("La contraseña debe tener al menos 8 caracteres");
    }

    if ($password !== $confirmar) {
        die("Las contraseñas no coinciden");
    }

    if (VerificarCorreoExistenteController($correo)) {
        die("Ese correo ya está registrado");
    }

    $resultado = RegistrarUsuarioController($nombre, $correo, $password);

    if ($resultado) {
        header("Location: ../index.php");
        exit();
    } else {
        die("Error al registrar usuario");
    }
}