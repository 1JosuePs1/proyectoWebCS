<?php
session_start();
require_once("../config/conexion.php");

$correo = trim($_POST['correoUsuario']);
$password = trim($_POST['passwordUsuario']);

if (empty($correo) || empty($password)) {
    die("Todos los campos son obligatorios");
}

$sql = "SELECT * FROM usuario WHERE emailUsuario = ? AND estadoUsuario = 'activo'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();

    if (password_verify($password, $usuario['passwordUsuario'])) {
    $_SESSION['idUsuario'] = $usuario['idUsuario'];
    $_SESSION['nombreCompleto'] = $usuario['nombreCompleto'];
    $_SESSION['idRol'] = $usuario['idRol'];

    header("Location: ../Views/Home/Home.php");
    exit();
} else {
    die("Contraseña incorrecta");
}
} else {
    die("Usuario no encontrado o inactivo");
}
?>