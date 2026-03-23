<?php
require_once("../config/conexion.php");

$correo = trim($_POST['correoUsuario']);
$nuevaPassword = trim($_POST['nuevaPassword']);
$confirmarPassword = trim($_POST['confirmarPassword']);

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

$sqlVerificar = "SELECT idUsuario FROM usuario WHERE emailUsuario = ?";
$stmt = $conexion->prepare($sqlVerificar);
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Ese correo no está registrado");
}

$passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

$sqlActualizar = "UPDATE usuario SET passwordUsuario = ? WHERE emailUsuario = ?";
$stmt = $conexion->prepare($sqlActualizar);
$stmt->bind_param("ss", $passwordHash, $correo);

if ($stmt->execute()) {
    header("Location: ../index.php");
    exit();
} else {
    die("Error al actualizar la contraseña");
}
?>