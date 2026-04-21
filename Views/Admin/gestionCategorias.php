<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/guardAdmin.php";
RequerirAdminOculto();
$rutaNavbar = ObtenerRutaNavbarAdmin();
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/categoriasController.php";

$categorias = ObtenerCategoriasController();
$productos = ObtenerProductosController();

$conteoPorCategoria = [];
foreach ($productos as $producto) {
    $idCat = intval($producto['idCategoria'] ?? 0);
    if ($idCat <= 0) {
        continue;
    }

    if (!isset($conteoPorCategoria[$idCat])) {
        $conteoPorCategoria[$idCat] = 0;
    }
    $conteoPorCategoria[$idCat]++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion de Categorias | Admin</title>

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="bg-light admin-page">

<?php require_once $rutaNavbar; ?>

<div class="container admin-main">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1"><i class="bi bi-tags me-2"></i>Gestion de Categorias</h3>
            <p class="text-muted mb-0 small"><?= count($categorias) ?> categorias registradas</p>
        </div>
        <a href="/proyectoWebCS/Views/Home/Home.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-shop me-1"></i>Ir a inicio de tienda
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background-color: var(--primer-color); color: #fff;">
                            <th>Categoria</th>
                            <th>Descripcion</th>
                            <th class="text-center">Productos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categorias)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No hay categorias registradas
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categorias as $categoria): ?>
                                <?php $cantidadProductos = intval($conteoPorCategoria[intval($categoria['idCategoria'])] ?? 0); ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($categoria['nombreCategoria'] ?? '') ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($categoria['descripcionCategoria'] ?? 'Sin descripcion') ?></td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill <?= $cantidadProductos > 0 ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                            <?= $cantidadProductos ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require('../components/footer.php') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
