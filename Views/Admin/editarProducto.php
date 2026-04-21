<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/guardAdmin.php";
RequerirAdminOculto();
$rutaNavbar = ObtenerRutaNavbarAdmin();
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/categoriasController.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/productoController.php";

$idProducto = intval($_GET['id'] ?? 0);
if ($idProducto <= 0) {
    $_SESSION['error_producto'] = 'Producto invalido.';
    header('Location: /proyectoWebCS/Views/Admin/listaProductos.php');
    exit();
}

$categorias = ObtenerCategoriasController();
$producto = ObtenerProductoPorIdController($idProducto);

if (!$producto) {
    $_SESSION['error_producto'] = 'No se encontro el producto.';
    header('Location: /proyectoWebCS/Views/Admin/listaProductos.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Producto | Admin</title>
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
            <h3 class="mb-1"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h3>
            <p class="text-muted mb-0 small">Actualiza datos y stock del producto.</p>
        </div>
        <a href="/proyectoWebCS/Views/Admin/listaProductos.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver a productos
        </a>
    </div>

    <?php if (isset($_SESSION['error_producto'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_producto']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php unset($_SESSION['error_producto']); endif; ?>

    <div class="row">
        <div class="col-md-8 col-lg-6 mx-auto">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header backgroundPrincipal text-white text-center py-4">
                    <h3 class="mb-0">Producto #<?= intval($producto['idProducto']) ?></h3>
                </div>

                <div class="card-body p-4">
                    <form action="../../Controllers/productoController.php" method="POST">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="idProducto" value="<?= intval($producto['idProducto']) ?>">

                        <div class="mb-3">
                            <label class="form-label">Nombre del producto</label>
                            <input type="text" name="nombreProducto" class="form-control" required value="<?= htmlspecialchars($producto['nombreProducto'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marcaProducto" class="form-control" required value="<?= htmlspecialchars($producto['marca'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripcion</label>
                            <textarea name="descripcionProducto" class="form-control" rows="3"><?= htmlspecialchars($producto['descripcionProducto'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" step="0.01" min="1" name="precioProducto" class="form-control" required value="<?= htmlspecialchars($producto['precioProducto'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" min="0" name="stockProducto" class="form-control" required value="<?= intval($producto['stockProducto'] ?? 0) ?>">
                            <small class="text-muted">Si el stock queda en 0, el sistema lo marca como agotado.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select name="idCategoria" class="form-control" required>
                                <option value="">Seleccione una categoria</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= intval($categoria['idCategoria']) ?>" <?= intval($categoria['idCategoria']) === intval($producto['idCategoria'] ?? 0) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria['nombreCategoria']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn backgroundPrincipal btn-lg text-white">
                                <i class="bi bi-save me-1"></i>Guardar cambios
                            </button>
                            <a href="/proyectoWebCS/Views/Admin/listaProductos.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('../components/footer.php') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
