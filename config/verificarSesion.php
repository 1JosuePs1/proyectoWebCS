<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idUsuario'])) {
    header("Location: /proyectoWebCS/Views/Registro/login.php");
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Protección: Si ni siquiera hay sesión, al login
if (!isset($_SESSION['idUsuario'])) {
    header("Location: /proyectoWebCS/Views/Registro/login.php");
    exit();
}

// 2. Definir qué Navbar le toca
$idRol = intval($_SESSION['idRol'] ?? 0);
$rutaNavbar = ($idRol === 1) 
    ? __DIR__ . "/../../components/navAdmin.php" 
    : __DIR__ . "/../../components/nav.php";
?>