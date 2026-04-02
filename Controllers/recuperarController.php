<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/recuperarModel.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/correoController.php");

session_start();
$correo = trim($_POST['correoUsuario'] ?? '');
$nuevaPassword = trim($_POST['nuevaPassword'] ?? '');
$confirmarPassword = trim($_POST['confirmarPassword'] ?? '');

if (empty($correo) || empty($nuevaPassword) || empty($confirmarPassword)) {
    $_SESSION['error_recuperar'] = "Todos los campos son obligatorios";
    header("Location: ../Views/Registro/cambiarClave.php");
    exit();
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_recuperar'] = "El correo electrónico no es válido";
    header("Location: ../Views/Registro/cambiarClave.php");
    exit();
}

if (strlen($nuevaPassword) < 8) {
    $_SESSION['error_recuperar'] = "La contraseña debe tener al menos 8 caracteres";
    header("Location: ../Views/Registro/cambiarClave.php");
    exit();
}

if ($nuevaPassword !== $confirmarPassword) {
    $_SESSION['error_recuperar'] = "Las contraseñas no coinciden";
    header("Location: ../Views/Registro/cambiarClave.php");
    exit();
}

$usuario = VerificarCorreoUsuarioModel($correo);

if (!$usuario) {
    $_SESSION['error_recuperar'] = "Ese correo no está registrado";
    header("Location: ../Views/Registro/cambiarClave.php");
    exit();
}

$passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

if (ActualizarPasswordUsuarioModel($correo, $passwordHash)) {
    EnviarCorreo("Recuperar Acceso", $nuevaPassword, $correo);
    header("Location: ../index.php");
    exit();
} else {
    $_SESSION['error_recuperar'] = "Error al actualizar la contraseña";
    header("Location: ../Views/Registro/cambiarClave.php");
    exit();
}
?>