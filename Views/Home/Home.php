
<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/verificarSesion.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/categoriasController.php";

$categorias = ObtenerCategoriasController();

$idCategoriaFiltro = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
$listaProductos = $idCategoriaFiltro
    ? ObtenerProductosPorCategoriaController($idCategoriaFiltro)
    : ObtenerProductosController();

$nombreCategoriaActual = null;
if ($idCategoriaFiltro) {
    foreach ($categorias as $cat) {
        if ($cat['idCategoria'] == $idCategoriaFiltro) {
            $nombreCategoriaActual = $cat['nombreCategoria'];
            break;
        }
    }
}

$iconosPorCategoria = [
    'teclados'            => 'bi-keyboard',
    'mouses'              => 'bi-mouse',
    'monitores'           => 'bi-display',
    'audifonos'           => 'bi-headphones',
    'componentes'         => 'bi-cpu',
    'computadoras'        => 'bi-laptop',
    'impresoras'          => 'bi-printer',
    'gaming streaming'    => 'bi-joystick',
    'conectividad redes'  => 'bi-wifi',
    'periféricos'         => 'bi-pc-display-horizontal',
    'audio'               => 'bi-speaker',
    'energía'             => 'bi-lightning-charge',
    'almacenamiento'      => 'bi-hdd-rack',
    'seguridad'           => 'bi-shield',
    'accesorios'          => 'bi-tools',
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>My Pc Gaming | Componentes y Laptops en Cartago</title>
    <meta name="description" content="La mejor tienda de hardware en Costa Rica. Encuentra laptops, GPUs, periféricos y soporte técnico especializado en Cartago.">
    <meta name="keywords" content="gaming, laptops, costa rica, cartago, componentes pc, tarjeta de video, periféricos">
    <meta name="author" content="My Pc Gaming">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="website">
    <meta property="og:title" content="My Pc Gaming | Potencia tu Setup">
    <meta property="og:description" content="Tu tienda de confianza para componentes de PC y periféricos en Costa Rica.">
    <meta property="og:image" content="https://mypcgaming.com/assets/image/imgLogo/logo.png">
    <meta property="og:url" content="https://mypcgaming.com/">

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">

    <!-- Boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/home.css">
</head>

<body>
    <!-- Navbar componente-->
    <?php require('../components/nav.php')?>


    
    <div id="registroCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#registroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#registroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#registroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../assets/image/Slider1/b1.jpeg" class="d-block w-100 carousel-slide-img" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="../assets/image/Slider1/b2.jpg" class="d-block w-100 carousel-slide-img" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="../assets/image/Slider1/b3.png" class="d-block w-100 carousel-slide-img" alt="Slide 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#registroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#registroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    
    <div class="container my-5">
        <h2 class="mb-4">Categorías Populares</h2>
        <div class="categories-slider-container">
            <button class="slider-btn slider-btn-prev" id="prevBtn">
                <i class="bi bi-chevron-left"></i>
            </button>
            
            <div class="categories-slider" id="categoriesSlider">
                <a href="/proyectoWebCS/Views/Home/Home.php" class="text-decoration-none">
                    <div class="category-item <?= !$idCategoriaFiltro ? 'active' : '' ?>">
                        <div class="category-img">
                            <i class="bi bi-grid"></i>
                        </div>
                        <p class="category-name">Todos</p>
                    </div>
                </a>
                <?php foreach ($categorias as $cat):
                    $icono = $iconosPorCategoria[strtolower($cat['nombreCategoria'])] ?? 'bi-box';
                ?>
                <a href="/proyectoWebCS/Views/Home/Home.php?categoria=<?= $cat['idCategoria'] ?>" class="text-decoration-none">
                    <div class="category-item <?= ($idCategoriaFiltro == $cat['idCategoria']) ? 'active' : '' ?>">
                        <div class="category-img">
                            <i class="bi <?= $icono ?>"></i>
                        </div>
                        <p class="category-name"><?= htmlspecialchars($cat['nombreCategoria']) ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            
            <button class="slider-btn slider-btn-next" id="nextBtn">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Main -->
    <div class="container my-5">
        <h2 class="mb-4"><?= $nombreCategoriaActual ? htmlspecialchars($nombreCategoriaActual) : 'Productos destacados' ?></h2>
        <div class="row g-5">
            <?php include $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Views/components/cardProducto.php"; ?>
        </div>
    </div>

   <!-- Navbar componente-->
    <?php require('../components/footer.php')?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/proyectoWebCS/Views/assets/js/home.js"></script>
</body>
</html>