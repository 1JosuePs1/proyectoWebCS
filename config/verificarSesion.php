<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idUsuario'])) {
    header("Location: /proyectoWebCS/Views/Registro/login.php");
    exit();
}

$idRol = intval($_SESSION['idRol'] ?? 0);
$esAdmin = ($idRol === 1);
$rutaNavbar = $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Views/components/" . ($esAdmin ? "navAdmin.php" : "nav.php");
?>