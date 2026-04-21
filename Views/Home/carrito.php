<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/verificarSesion.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/carritoController.php";

$productosCarrito = ObtenerCarrito();
$totalCarrito = ObtenerTotalCarrito();
$totalItems = ObtenerTotalItemsCarrito();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Carrito | My Pc Gaming</title>

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body class="bg-light">

    <?php require('../components/nav.php') ?>

    <div class="container my-5">
        <h3 class="mb-4"><i class="bi bi-cart3 me-2"></i>Mi Carrito <span class="text-muted fs-6">(<?= $totalItems ?> productos)</span></h3>

        <?php if (isset($_SESSION['ok_carrito'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['ok_carrito']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php unset($_SESSION['ok_carrito']); endif; ?>

        <?php if (isset($_SESSION['error_carrito'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error_carrito']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php unset($_SESSION['error_carrito']); endif; ?>

        <?php if (empty($productosCarrito)): ?>
            <div class="text-center py-5">
                <i class="bi bi-cart-x fs-1 text-muted d-block mb-3"></i>
                <p class="text-muted fs-5">Tu carrito está vacío</p>
                <a href="/proyectoWebCS/Views/Home/Home.php" class="btn text-white" style="background-color: var(--primer-color);">
                    <i class="bi bi-arrow-left me-1"></i>Ir a la tienda
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr style="background-color: var(--primer-color); color: #fff;">
                                        <th class="rounded-start">Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th class="rounded-end text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productosCarrito as $item): ?>
                                        <?php
                                            $imagenes = isset($item['imagenProducto']) ? json_decode($item['imagenProducto'], true) : [];
                                            $primeraImg = (!empty($imagenes)) ? $imagenes[0] : null;
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($primeraImg): ?>
                                                        <img src="/proyectoWebCS/Views/assets/image/productos/<?= $item['idProducto'] ?>/<?= htmlspecialchars($primeraImg) ?>"
                                                             alt="<?= htmlspecialchars($item['nombreProducto']) ?>"
                                                             style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px; background: #f8f9fa; padding: 4px;">
                                                    <?php else: ?>
                                                        <div style="width: 60px; height: 60px;" class="bg-light d-flex align-items-center justify-content-center rounded">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <span class="ms-3 fw-semibold"><?= htmlspecialchars($item['nombreProducto']) ?></span>
                                                </div>
                                            </td>
                                            <td class="align-middle">₡<?= number_format($item['precioProducto'], 0, ',', '.') ?></td>
                                            <td class="align-middle">
                                                <form action="/proyectoWebCS/Controllers/carritoController.php" method="POST" class="d-flex align-items-center" style="width: 120px;">
                                                    <input type="hidden" name="accion" value="actualizar">
                                                    <input type="hidden" name="idProducto" value="<?= $item['idProducto'] ?>">
                                                    <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" max="<?= $item['stockProducto'] ?>" class="form-control form-control-sm text-center" onchange="this.form.submit()">
                                                </form>
                                            </td>
                                            <td class="align-middle fw-bold" style="color: var(--primer-color);">₡<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                            <td class="align-middle text-center">
                                                <form action="/proyectoWebCS/Controllers/carritoController.php" method="POST">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="idProducto" value="<?= $item['idProducto'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header text-white text-center py-3" style="background-color: var(--primer-color);">
                            <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Resumen</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Productos (<?= $totalItems ?>)</span>
                                <span>₡<?= number_format($totalCarrito, 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Envío</span>
                                <span class="text-success">Gratis</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total</span>
                                <span style="color: var(--primer-color);">₡<?= number_format($totalCarrito, 0, ',', '.') ?></span>
                            </div>
                            <div class="d-grid mt-4">
                                <a href="/proyectoWebCS/Views/Home/pago.php" class="btn btn-lg text-white w-100" style="background-color: var(--primer-color);">
                                    <i class="bi bi-bag-check me-2"></i>Proceder al pago
                                </a>
                            </div>
                            <div class="text-center mt-3">
                                <a href="/proyectoWebCS/Views/Home/Home.php" class="text-muted small">
                                    <i class="bi bi-arrow-left me-1"></i>Seguir comprando
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php require('../components/footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
