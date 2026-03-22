    <!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-red-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="/proyectoWebCS/index.php"><img src="/proyectoWebCS/Views/assets/image/imgLogo/logoBlanco.png" class="img-fluid" width="100" alt="Logo de la tienda"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="#navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Inicio</a>
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
                    <li class="nav-item">
                        <a class="btn btn-user" href="#" title="Perfil"><i class="bi bi-person-circle"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-logout" href="../../Controllers/logout.php" title="Cerrar sesión"><i class="bi bi-box-arrow-right"></i></a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>
