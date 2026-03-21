
<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/categoriasController.php";

$listaProductos = ObtenerProductosController();
$categorias = ObtenerCategoriasController();
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

    <a href="../../Controllers/logout.php" class="btn btn-danger">Cerrar sesión</a>



    
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
                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <p class="category-name">Computadoras</p>
                </div>
                
                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-cpu"></i>
                    </div>
                    <p class="category-name">Componentes</p>
                </div>
                
                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-printer"></i>
                    </div>
                    <p class="category-name">Impresoras</p>
                </div>
                
                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-joystick"></i>
                    </div>
                    <p class="category-name">Gaming Streaming</p>
                </div>
                
                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-wifi"></i>
                    </div>
                    <p class="category-name">Conectividad Redes</p>
                </div>

                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-mouse"></i>
                    </div>
                    <p class="category-name">Periféricos</p>
                </div>

                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-headphones"></i>
                    </div>
                    <p class="category-name">Audio</p>
                </div>

                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-display"></i>
                    </div>
                    <p class="category-name">Monitores</p>
                </div>

                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-keyboard"></i>
                    </div>
                    <p class="category-name">Teclados</p>
                </div>

                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <p class="category-name">Energía</p>
                </div>

                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-hdd-rack"></i>
                    </div>
                    <p class="category-name">Almacenamiento</p>
                </div>

                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-shield"></i>
                    </div>
                    <p class="category-name">Seguridad</p>
                </div>

                <div class="category-item">
                    <div class="category-img">
                        <i class="bi bi-tools"></i>
                    </div>
                    <p class="category-name">Accesorios</p>
                </div>
            </div>
            
            <button class="slider-btn slider-btn-next" id="nextBtn">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Main -->
    <div class="container my-5">
        <h2 class="mb-4">Productos destacados</h2>
        <div class="row g-5">
            <?php foreach ($listaProductos as $producto): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="producto-card">
                    <?php if (!empty($producto['primeraImagen'])): ?>
                        <img src="../assets/image/productos/<?php echo $producto['idProducto']; ?>/<?php echo htmlspecialchars($producto['primeraImagen']); ?>"
                             class="img-fluid producto-imagen"
                             alt="<?php echo htmlspecialchars($producto['nombreProducto']); ?>"
                             style="height: 280px; width: 100%; object-fit: contain; border-radius: 8px;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 280px; border-radius: 8px;">
                            <i class="bi bi-image fs-1 text-muted"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Información del producto -->
                    <div class="producto-info mt-3">
                        <h6 class="producto-nombre mb-1"><?php echo htmlspecialchars($producto['nombreProducto']); ?></h6>
                        <p class="producto-marca text-muted mb-2"><?php echo htmlspecialchars($producto['marca']); ?></p>
                        <p class="producto-descripcion small text-muted mb-3"><?php echo htmlspecialchars(substr($producto['descripcionProducto'], 0, 80)); ?>...</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="producto-precio">₡<?php echo number_format($producto['precioProducto'], 0, ',', '.'); ?></span>
                            <button class="btn btn-sm btn-color">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

   <!-- Navbar componente-->
    <?php require('../components/footer.php')?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../assets/js/home.js"></script>
</body>
</html>