<?php 
// 1. Seguridad: Si no es admin, rebota al login
if (intval($_SESSION['idRol'] ?? 0) !== 1) {
    header("Location: /proyectoWebCS/Views/Registro/login.php");
    exit();
}

// 2. Identificar la página real para el estado "active"
$urlActual = basename($_SERVER['PHP_SELF']); 
?>
<head><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></head>


<nav class="navbar navbar-expand-lg navbar-dark backgroundPrincipal shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/proyectoWebCS/Views/Admin/dashboard.php">
            <i class="bi bi-cpu-fill"></i> My PC Gaming Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNavAdmin">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($urlActual == 'dashboard.php') ? 'active' : '' ?>" href="/proyectoWebCS/Views/Admin/dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($urlActual == 'listaProductos.php') ? 'active' : '' ?>" href="/proyectoWebCS/Views/Admin/listaProductos.php">
                        <i class="bi bi-box-seam"></i> Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($urlActual == 'gestionCategorias.php') ? 'active' : '' ?>" href="/proyectoWebCS/Views/Admin/gestionCategorias.php">
                        <i class="bi bi-tags"></i> Categorías
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($urlActual == 'pedidos.php') ? 'active' : '' ?>" href="/proyectoWebCS/Views/Admin/pedidos.php">
                        <i class="bi bi-graph-up"></i> Ventas
                    </a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <span class="text-white me-3 border-end pe-3">
                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['nombreUsuario'] ?? 'Admin') ?>
                </span>
                <a href="/proyectoWebCS/config/cerrarSesion.php" class="btn btn-outline-light btn-sm ms-2">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </div>
</nav>