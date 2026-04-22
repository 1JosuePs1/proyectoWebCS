<?php include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/slugify.php"; ?>
<?php foreach ($listaProductos as $producto): ?>
<?php
    $stockProducto = intval($producto['stockProducto'] ?? 0);
    $estadoProducto = strtolower(trim($producto['estadoProducto'] ?? ''));
    $agotado = $stockProducto <= 0 || $estadoProducto === 'agotado';
    
    // Manejo de ofertas
    $enOferta = isset($producto['enOferta']) && $producto['enOferta'] == 1;
    $precioOferta = isset($producto['precioOferta']) ? floatval($producto['precioOferta']) : null;
    $precioOriginal = floatval($producto['precioProducto']);
    
    // Calcular descuento
    $descuentoPorc = 0;
    if ($enOferta && $precioOferta && $precioOferta < $precioOriginal) {
        $descuentoPorc = round((($precioOriginal - $precioOferta) / $precioOriginal) * 100);
    }
?>
<div class="col-12 col-md-6 col-lg-4">
    <div class="producto-card position-relative <?= $enOferta && $descuentoPorc > 0 ? 'en-oferta' : '' ?>">
        <!-- Badge de Oferta -->
        <?php if ($enOferta && $descuentoPorc > 0): ?>
            <div class="position-absolute top-0 end-0 m-2">
                <span class="badge badge-oferta fs-6">
                    <i class="bi bi-tag-fill"></i>-<?= $descuentoPorc ?>%
                </span>
            </div>
        <?php endif; ?>

        <?php if (!empty($producto['primeraImagen'])): ?>
            <img src="/proyectoWebCS/Views/assets/image/productos/<?php echo $producto['idProducto']; ?>/<?php echo htmlspecialchars($producto['primeraImagen']); ?>"
                class="img-fluid producto-imagen card-img-producto"
                alt="<?php echo htmlspecialchars($producto['nombreProducto']); ?>">
        <?php else: ?>
            <div class="bg-light d-flex align-items-center justify-content-center card-img-producto">
                <i class="bi bi-image fs-1 text-muted"></i>
            </div>
        <?php endif; ?>

        <div class="producto-card-info mt-3">
            <h6 class="producto-nombre mb-1"><?php echo htmlspecialchars($producto['nombreProducto']); ?></h6>
            <p class="producto-marca text-muted mb-2"><?php echo htmlspecialchars($producto['marca']); ?></p>
            <p class="producto-descripcion small text-muted mb-3"><?php echo htmlspecialchars(substr($producto['descripcionProducto'], 0, 80)); ?>...</p>
            <p class="mb-2">
                <?php if ($agotado): ?>
                    <span class="badge text-bg-secondary">Agotado</span>
                <?php else: ?>
                    <span class="badge text-bg-success">Stock: <?php echo $stockProducto; ?></span>
                <?php endif; ?>
            </p>
            
            <!-- Sección de precios -->
            <div class="mb-3">
                <?php if ($enOferta && $precioOferta && $precioOferta < $precioOriginal): ?>
                    <!-- Mostrar precio original tachado y nuevo precio -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="precio-tachado">
                            ₡<?php echo number_format($precioOriginal, 0, ',', '.'); ?>
                        </span>
                        <span class="precio-oferta">
                            ₡<?php echo number_format($precioOferta, 0, ',', '.'); ?>
                        </span>
                    </div>
                    <p class="ahorro-texto mb-0 mt-1">
                        <i class="bi bi-percent"></i>Ahorra ₡<?php echo number_format($precioOriginal - $precioOferta, 0, ',', '.'); ?>
                    </p>
                <?php else: ?>
                    <!-- Mostrar precio normal -->
                    <span class="producto-precio">₡<?php echo number_format($precioOriginal, 0, ',', '.'); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="d-flex justify-content-between align-items-center">
                <div></div>
                <div class="d-flex">
                    <?php if ($agotado): ?>
                        <button type="button" class="btn btn-sm btn-secondary disabled me-1" title="Sin stock" disabled>
                            <i class="bi bi-cart-plus"></i>
                        </button>
                    <?php else: ?>
                        <form action="/proyectoWebCS/Controllers/carritoController.php" method="POST" class="me-1">
                            <input type="hidden" name="accion" value="agregar">
                            <input type="hidden" name="idProducto" value="<?php echo $producto['idProducto']; ?>">
                            <button type="submit" class="btn btn-sm btn-color" title="Agregar al carrito">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="/proyectoWebCS/producto/<?php echo slugify($producto['nombreProducto']); ?>" class="btn btn-sm btn-color" title="Ver producto">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
