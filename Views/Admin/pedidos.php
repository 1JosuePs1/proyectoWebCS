<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/guardAdmin.php";
RequerirAdminOculto();
$rutaNavbar = ObtenerRutaNavbarAdmin();
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/pedidoController.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/productosModel.php";

$pedidos = ObtenerPedidosAdminController();
$pedidosConAccion = [];

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
    <title>Pedidos | Admin</title>

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .pedido-thumb {
            width: 58px;
            height: 58px;
            object-fit: contain;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 4px;
        }

        .pedido-direccion {
            min-width: 250px;
            white-space: normal;
            line-height: 1.35;
        }

        .pedido-direccion-linea {
            word-break: break-word;
        }

        .pedido-accion {
            min-width: 145px;
        }

        .pedido-accion-contenido {
            display: grid;
            gap: 0.35rem;
            justify-items: stretch;
            width: max-content;
            margin: 1px auto;
        }

        .pedido-accion .btn {
            white-space: nowrap;
            padding: 0.3rem 0.6rem;
            font-size: 0.82rem;
            line-height: 1.2;
        }

        @media (max-width: 991px) {
            .pedido-direccion {
                min-width: 220px;
            }

            .pedido-accion {
                min-width: 130px;
            }
        }
    </style>
</head>

<body class="bg-light admin-page">
    <?php require_once $rutaNavbar; ?>

    <div class="container admin-main">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <h3 class="mb-0 text-center w-100"><i class="bi bi-box-seam me-2"></i>Pedidos de clientes</h3>
            <a href="/proyectoWebCS/Views/Admin/dashboard.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-speedometer2 me-1"></i>Volver al dashboard
            </a>
        </div>

        <?php if (isset($_SESSION['ok_pedidos'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['ok_pedidos']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php unset($_SESSION['ok_pedidos']); endif; ?>

        <?php if (isset($_SESSION['error_pedidos'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error_pedidos']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php unset($_SESSION['error_pedidos']); endif; ?>

        <?php if (empty($pedidos)): ?>
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted mb-0">No hay pedidos registrados.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-center align-middle mb-0">
                        <thead>
                            <tr style="background-color: var(--primer-color); color: #fff;">
                                <th class="text-center">Pedido</th>
                                <th class="text-center">Cliente</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Total orden</th>
                                <th class="text-center">Direccion de envio</th>
                                <th class="text-center">Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $fila): ?>
                                <?php
                                    $idPedido = intval($fila['idPedido']);
                                    $idProducto = intval($fila['idProducto'] ?? 0);
                                    $primeraImg = $imagenesProducto[$idProducto] ?? null;
                                ?>
                                <tr>
                                    <td>#<?= $idPedido ?></td>
                                    <td><?= htmlspecialchars($fila['nombreCompleto']) ?></td>
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
                                    <td class="small text-center pedido-direccion">
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($fila['nombreDestinatario']) ?></div>
                                            <div class="pedido-direccion-linea"><?= htmlspecialchars($fila['telefonoEnvio']) ?></div>
                                            <div class="pedido-direccion-linea"><?= htmlspecialchars($fila['direccionEnvio']) ?></div>
                                        </div>
                                    </td>
                                    <td class="pedido-accion">
                                        <?php if (!isset($pedidosConAccion[$idPedido])): ?>
                                            <?php $pedidosConAccion[$idPedido] = true; ?>
                                            <?php if ($fila['estadoPedido'] === 'pendiente'): ?>
                                                <form action="/proyectoWebCS/Controllers/pedidoController.php" method="POST" class="pedido-accion-contenido">
                                                    <input type="hidden" name="accion" value="marcarCompletado">
                                                    <input type="hidden" name="idPedido" value="<?= $idPedido ?>">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check2-circle me-1"></i>Marcar completado
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted small">Completado</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
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
