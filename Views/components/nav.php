    <!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-red-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="/proyectoWebCS/Views/Home/Home.php"><img src="/proyectoWebCS/Views/assets/image/imgLogo/logoBlanco.png" class="img-fluid" width="100" alt="Logo de la tienda"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="#navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/proyectoWebCS/Views/Home/Home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Ofertas</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar">
                        <button class="btn btn-outline-light" type="submit">Buscar</button>
                    </form>
                    <li class="nav-item">   
                        <a class="nav-link bold" href="#"><i class="bi bi-cart"></i> Carrito</a>
                    </li>

                    <?php if (isset($_SESSION['idUsuario'])): ?>
                        <li class="nav-item">
                            <a class="btn btn-user" href="/proyectoWebCS/Views/usuario/usuario.php" title="Perfil de <?= htmlspecialchars($_SESSION['nombreCompleto']) ?>">
                                <i class="bi bi-person-circle"></i>
                                <span class="d-none d-lg-inline ms-1 small nombreUsuario"><?= htmlspecialchars($_SESSION['nombreCompleto']) ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-logout" href="/proyectoWebCS/Controllers/logout.php" title="Cerrar sesión"><i class="bi bi-box-arrow-right"></i></a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-user" href="/proyectoWebCS/index.php" title="Iniciar sesión"><i class="bi bi-person-circle"></i></a>
                        </li>
                    <?php endif; ?>
                </ul>

            </div>
        </div>
    </nav>
