<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/productoController.php";

include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/categoriasController.php";

$listaProductos = ObtenerProductosController();
$categorias = ObtenerCategoriasController();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Producto | My Pc Gaming</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/proyectoWebCS/Views/assets/css/main.css">
    <link rel="stylesheet" href="/proyectoWebCS/Views/assets/css/home.css">
    <link rel="stylesheet" href="/proyectoWebCS/Views/assets/css/producto.css">
</head>
<body class="bg-light">

    <?php require('../components/nav.php')?>

    <div class="container">
        <div class="row producto-main">
                <!-- Galería de imágenes -->
                <div class="col-md-5 producto-galeria">
                    <?php 
                        $imagenes = isset($p['imagenProducto']) ? json_decode($p['imagenProducto'], true) : [];
                        if (!$imagenes || count($imagenes) === 0) {
                            $imagenes = ["imgLogo/logo.png"];
                        }
                    ?>
                    <div class="producto-galeria-miniaturas">
                        <?php foreach ($imagenes as $i => $img): ?>
                            <img src="/proyectoWebCS/Views/assets/image/productos/<?php echo $p['idProducto']; ?>/<?php echo htmlspecialchars($img); ?>" class="miniatura<?php echo $i === 0 ? ' active' : ''; ?>" onclick="mostrarImagen(<?php echo $i; ?>)" alt="Miniatura">
                        <?php endforeach; ?>
                    </div>
                    <div class="producto-galeria-principal">
                        <img id="imgPrincipal" src="/proyectoWebCS/Views/assets/image/productos/<?php echo $p['idProducto']; ?>/<?php echo htmlspecialchars($imagenes[0]); ?>" alt="Imagen principal" class="img-fluid">
                    </div>
                </div>
                <!-- Info producto -->
                <div class="col-md-6 producto-info">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-white p-2">
                            <li class="breadcrumb-item"><a href="/proyectoWebCS/index.php">Tienda</a></li>
                            <li class="breadcrumb-item active " style> <?php echo htmlspecialchars($p['nombreProducto']); ?> </li>
                        </ol>
                    </nav>
                    <h1 class="producto-titulo"><?php echo htmlspecialchars($p['nombreProducto']); ?></h1>
                    <p class=" text-muted small">Número de ítem: <?php echo $p['idProducto']; ?></p>
                    <div class="producto-precio">₡<?php echo number_format($p['precioProducto'], 0); ?></div>
                    <button class="btn-primary producto-boton">Añadir al carrito</button>
                    <div class="producto-disponibilidad">
                        <span class="badge bg-success">En stock: <?php echo number_format($p['stockProducto'], 0); ?></span>
                    </div>
                    <div class="producto-descripcion">
                        <h4>Descripción</h4>
                        <?php echo nl2br(htmlspecialchars($p['descripcionProducto'])); ?>
                    </div>
                </div>
            </div>
            <!-- Placeholder para disponibilidad, relacionados, etc. -->
            <div class="producto-relacionados">
                <h5 class="mt-5">Productos Relacionados</h5>
                <div class="container my-5">
    
                <div class="row g-5">
                    <?php include $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Views/components/cardProducto.php"; ?>
                </div>
            </div>
        </div>
    </div>
        <script>
           
            function mostrarImagen(idx) {
                var miniaturas = document.querySelectorAll('.producto-galeria-miniaturas img');
                var principal = document.getElementById('imgPrincipal');
                miniaturas.forEach(function(img, i) {
                    img.classList.toggle('active', i === idx);
                });
                principal.src = miniaturas[idx].src;
            }
        </script>

    <?php require($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Views/components/footer.php"); ?>
</body>
</html>
