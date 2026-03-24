<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/recuperarModel.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/correoController.php");

$correo = trim($_POST['correoUsuario'] ?? '');
$nuevaPassword = trim($_POST['nuevaPassword'] ?? '');
$confirmarPassword = trim($_POST['confirmarPassword'] ?? '');

if (empty($correo) || empty($nuevaPassword) || empty($confirmarPassword)) {
    die("Todos los campos son obligatorios");
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    die("El correo electrónico no es válido");
}

if (strlen($nuevaPassword) < 8) {
    die("La contraseña debe tener al menos 8 caracteres");
}

if ($nuevaPassword !== $confirmarPassword) {
    die("Las contraseñas no coinciden");
}

$usuario = VerificarCorreoUsuarioModel($correo);

if (!$usuario) {
    die("Ese correo no está registrado");
}

$passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

if (ActualizarPasswordUsuarioModel($correo, $passwordHash)) {
    EnviarCorreo("Recuperar Acceso", $nuevaPassword, $correo);
    header("Location: ../index.php");
    exit();
} else {
    die("Error al actualizar la contraseña");
}
?>