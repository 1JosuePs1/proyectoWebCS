<?php
require_once("../config/conexion.php");

$nombre = trim($_POST['nombreUsuario']);
$correo = trim($_POST['correoUsuario']);
$password = trim($_POST['passwordUsuario']);
$confirmar = trim($_POST['confirmPassword']);

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

$sqlVerificar = "SELECT idUsuario FROM usuario WHERE emailUsuario = ?";
$stmt = $conexion->prepare($sqlVerificar);
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    die("Ese correo ya está registrado");
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$sqlInsertar = "INSERT INTO usuario (nombreCompleto, emailUsuario, passwordUsuario) VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sqlInsertar);
$stmt->bind_param("sss", $nombre, $correo, $passwordHash);

if ($stmt->execute()) {
    echo "Usuario registrado correctamente";
} else {
    echo "Error al registrar usuario";
}
?>