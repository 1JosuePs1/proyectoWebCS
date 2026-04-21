<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/verificarSesion.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/pedidoController.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/productosModel.php";

$pedidos = ObtenerPedidosUsuarioController(intval($_SESSION['idUsuario']));

$imagenesProducto = [];
foreach ($pedidos as $filaPedido) {
    $idProducto = intval($filaPedido['idProducto'] ?? 0);
    if ($idProducto <= 0 || isset($imagenesProducto[$idProducto])) {
        continue;
    }

    $producto = ObtenerProductoPorIdModel($idProducto);
    $imagenes = isset($producto['imagenProducto']) ? json_decode($producto['imagenProducto'], true) : [];
    $imagenesProducto[$idProducto] = (!empty($imagenes)) ? $imagenes[0] : null;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis pedidos | My Pc Gaming</title>

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .pedido-thumb {
            width: 62px;
            height: 62px;
            object-fit: contain;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 4px;
        }
    </style>
</head>

<body class="bg-light">
    <?php require('../components/nav.php') ?>

    <div class="container my-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <h3 class="mb-0 text-center w-100"><i class="bi bi-clock-history me-2"></i>Historial de pedidos</h3>
            <a href="/proyectoWebCS/Views/Home/Home.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Volver a la tienda
            </a>
        </div>

        <?php if (empty($pedidos)): ?>
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body text-center py-5">
                    <i class="bi bi-bag-x fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted mb-0">Aun no tienes pedidos registrados.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-center align-middle mb-0">
                        <thead>
                            <tr style="background-color: var(--primer-color); color: #fff;">
                                <th class="text-center">Pedido</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Total orden</th>
                                <th class="text-center">Direccion de envio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $fila): ?>
                                <?php
                                    $idProducto = intval($fila['idProducto'] ?? 0);
                                    $primeraImg = $imagenesProducto[$idProducto] ?? null;
                                ?>
                                <tr>
                                    <td>#<?= intval($fila['idPedido']) ?></td>
                                    <td><?= htmlspecialchars($fila['fechaPedido']) ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?= $fila['estadoPedido'] === 'completado' ? 'text-bg-success' : 'text-bg-warning' ?>">
                                            <?= htmlspecialchars(ucfirst($fila['estadoPedido'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column align-items-center justify-content-center gap-1">
                                            <?php if ($primeraImg): ?>
                                                <img src="/proyectoWebCS/Views/assets/image/productos/<?= $idProducto ?>/<?= htmlspecialchars($primeraImg) ?>" alt="<?= htmlspecialchars($fila['nombreProducto']) ?>" class="pedido-thumb">
                                            <?php else: ?>
                                                <div class="pedido-thumb d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span class="fw-semibold small"><?= htmlspecialchars($fila['nombreProducto']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= intval($fila['cantidadProductos']) ?></td>
                                    <td>₡<?= number_format(floatval($fila['totalVenta']), 0, ',', '.') ?></td>
                                    <td class="small text-center">
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($fila['nombreDestinatario']) ?></div>
                                            <div><?= htmlspecialchars($fila['telefonoEnvio']) ?></div>
                                            <div><?= htmlspecialchars($fila['direccionEnvio']) ?></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php require('../components/footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
