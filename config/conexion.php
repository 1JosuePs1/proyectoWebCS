<?php
$host = "127.0.0.1";
$usuario = "root";
$contrasena = "123456";
$baseDatos = "tiendaGaming";
$puerto = 3306;

$conexion = new mysqli($host, $usuario, $contrasena, $baseDatos, $puerto);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>