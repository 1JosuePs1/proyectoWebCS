<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idUsuario'])) {
    header("Location: /proyectoWebCS/Views/Registro/login.php");
    exit();
}
