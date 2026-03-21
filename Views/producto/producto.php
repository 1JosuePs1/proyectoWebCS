<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/productoController.php";

$p = null;
if (isset($_GET['id'])) {
    $idProducto = intval($_GET['id']);
    $p = ObtenerProductoDetalleController($idProducto);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/detalle.css">
</head>
<body class="bg-light">

    <?php require('../components/nav.php')?>

    <div class="main-content">
        <div class="container mt-5">
            <div class="row bg-white p-4 shadow-sm rounded">
                <div class="col-md-6">
                    <img src="<?php echo htmlspecialchars($p['imagen']); ?>" class="img-fluid rounded shadow" alt="...">
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tienda</a></li>
                        <li class="breadcrumb-item active"> <?php echo htmlspecialchars($p['nombreProducto']); ?> </li>
                      </ol>
                    </nav>
                    <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($p['nombreProducto']); ?></h1>
                    <p class="lead text-muted small">Referencia ID: #<?php echo $p['idProducto']; ?></p>
                    <hr>
                    <h2 class="text-primary mb-4">₡<?php echo number_format($p['precioProducto'], 0); ?></h2>
                    <p class="fs-5"><?php echo nl2br(htmlspecialchars($p['descripcionProducto'])); ?></p>
                    <div class="mt-5 d-grid gap-2">
                        <button class="btn btn-primary btn-lg">Añadir al carrito</button>
                        <a href="index.php" class="btn btn-link">Volver a la tienda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('../components/footer.php')?>
</body>
</html>
