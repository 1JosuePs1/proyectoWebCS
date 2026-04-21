<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/guardAdmin.php";
RequerirAdminOculto();
$rutaNavbar = ObtenerRutaNavbarAdmin();

include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/dashboardController.php";

$totalVentas = ObtenerCantidadTotalVentasController();
$ventasPorProducto = ObtenerVentasPorProductoController();
$categoriaMasVende = ObtenerCategoriaMasVendeController();
$detalleVentas = ObtenerDetalleVentasDashboardController();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard de Ventas</title>
    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light admin-page">


<?php require_once $rutaNavbar; ?>
<div class="container admin-main">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1"><i class="bi bi-speedometer2 me-2"></i>Dashboard de Ventas</h3>
            <p class="text-muted mb-0 small">Resumen general del rendimiento de la tienda.</p>
        </div>
        <a href="/proyectoWebCS/Views/Home/Home.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-shop me-1"></i>Ir a inicio de tienda
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h5 class="card-title">Cantidad total de ventas</h5>
                    <p class="fs-2 fw-bold mb-0">
                        <?= htmlspecialchars($totalVentas['total_ventas'] ?? 0) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card shadow border-0">
                <div class="card-body">
                    <h5 class="card-title">Categoría que más vende</h5>
                    <p class="fs-4 fw-bold mb-1">
                        <?= htmlspecialchars($categoriaMasVende['categoria'] ?? 'Sin datos') ?>
                    </p>
                    <p class="mb-0">
                        Cantidad vendida:
                        <?= htmlspecialchars($categoriaMasVende['cantidad_vendida'] ?? 0) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-header backgroundPrincipal text-white">
            Ventas por producto
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad vendida</th>
                        <th>Total vendido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ventasPorProducto)): ?>
                        <?php foreach ($ventasPorProducto as $venta): ?>
                            <tr>
                                <td><?= htmlspecialchars($venta['producto']) ?></td>
                                <td><?= htmlspecialchars($venta['cantidad_vendida']) ?></td>
                                <td>₡<?= number_format($venta['total_vendido'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No hay datos de ventas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow border-0">
        <div class="card-header backgroundPrincipal text-white">
            Detalle de ventas
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Fecha</th>
                        <th>Total Venta</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($detalleVentas)): ?>
                        <?php foreach ($detalleVentas as $fila): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['idVenta']) ?></td>
                                <td><?= htmlspecialchars($fila['fechaVenta']) ?></td>
                                <td>₡<?= number_format($fila['totalVenta'], 2) ?></td>
                                <td><?= htmlspecialchars($fila['nombreProducto']) ?></td>
                                <td><?= htmlspecialchars($fila['cantidadProductos']) ?></td>
                                <td>₡<?= number_format($fila['precioUnitarioHistorico'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay ventas registradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require('../components/footer.php') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

</body>
</html>