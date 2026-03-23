<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/categoriasController.php";

$listaProductos = ObtenerProductosController();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lista de Productos | Admin</title>

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">

    <style>
        .table-productos th {
            background-color: var(--primer-color);
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }
        .table-productos td {
            vertical-align: middle;
            font-size: 0.9rem;
        }
        .img-producto-tabla {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 4px;
        }
        .badge-stock {
            font-size: 0.75rem;
        }
        .btn-accion {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            padding: 0;
            font-size: 0.9rem;
        }
        .desc-truncada {
            max-width: 220px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body class="bg-light">

    <?php require('../components/nav.php') ?>

    <div class="container my-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1"><i class="bi bi-box-seam me-2"></i>Lista de Productos</h3>
                <p class="text-muted mb-0 small"><?= count($listaProductos) ?> productos registrados</p>
            </div>
            <a href="registrarProducto.php" class="btn btn-sm text-white" style="background-color: var(--primer-color);">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
            </a>
        </div>

        <!-- Tabla -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-productos mb-0">
                        <thead>
                            <tr>
                                <th class="rounded-start">Foto</th>
                                <th>Nombre</th>
                                <th>Marca</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th class="rounded-end text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listaProductos)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No hay productos registrados
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listaProductos as $producto): ?>
                                    <tr>
                                        <!-- Foto -->
                                        <td>
                                            <?php if (!empty($producto['primeraImagen'])): ?>
                                                <img src="/proyectoWebCS/Views/assets/image/productos/<?= $producto['idProducto'] ?>/<?= htmlspecialchars($producto['primeraImagen']) ?>"
                                                     class="img-producto-tabla"
                                                     alt="<?= htmlspecialchars($producto['nombreProducto']) ?>">
                                            <?php else: ?>
                                                <div class="img-producto-tabla bg-light d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Nombre -->
                                        <td class="fw-semibold"><?= htmlspecialchars($producto['nombreProducto']) ?></td>

                                        <!-- Marca -->
                                        <td><span class="text-muted"><?= htmlspecialchars($producto['marca']) ?></span></td>

                                        <!-- Descripción (max 100 chars) -->
                                        <td>
                                            <span class="desc-truncada d-inline-block" title="<?= htmlspecialchars($producto['descripcionProducto']) ?>">
                                                <?= htmlspecialchars(mb_substr($producto['descripcionProducto'], 0, 100)) ?>
                                                <?= mb_strlen($producto['descripcionProducto']) > 100 ? '...' : '' ?>
                                            </span>
                                        </td>

                                        <!-- Precio -->
                                        <td class="fw-bold" style="color: var(--primer-color);">
                                            ₡<?= number_format($producto['precioProducto'], 0, ',', '.') ?>
                                        </td>

                                        <!-- Stock -->
                                        <td>
                                            <?php if ($producto['stockProducto'] > 0): ?>
                                                <span class="badge bg-success badge-stock"><?= $producto['stockProducto'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger badge-stock">Agotado</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Acciones -->
                                        <td class="text-center">
                                            <a href="editarProducto.php?id=<?= $producto['idProducto'] ?>" class="btn btn-accion btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-accion btn-outline-danger ms-1" title="Eliminar"
                                                    onclick="confirmarEliminar(<?= $producto['idProducto'] ?>, '<?= htmlspecialchars(addslashes($producto['nombreProducto'])) ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
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

    <!-- Modal Confirmar Eliminación -->
    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white border-0">
                    <h6 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar producto</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <p class="mb-0">¿Eliminar <strong id="nombreProductoEliminar"></strong>?</p>
                    <small class="text-muted">Esta acción no se puede deshacer.</small>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a id="btnConfirmarEliminar" href="#" class="btn btn-sm btn-danger">Eliminar</a>
                </div>
            </div>
        </div>
    </div>

    <?php require('../components/footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        function confirmarEliminar(id, nombre) {
            document.getElementById('nombreProductoEliminar').textContent = nombre;
            document.getElementById('btnConfirmarEliminar').href = '../../Controllers/productoController.php?eliminar=' + id;
            new bootstrap.Modal(document.getElementById('modalEliminar')).show();
        }
    </script>
</body>

</html>
