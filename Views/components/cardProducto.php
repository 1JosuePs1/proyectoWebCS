<?php include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/slugify.php"; ?>
<?php foreach ($listaProductos as $producto): ?>
<div class="col-12 col-md-6 col-lg-4">
    <div class="producto-card">
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
            
            <div class="d-flex justify-content-between align-items-center">
                <span class="producto-precio">₡<?php echo number_format($producto['precioProducto'], 0, ',', '.'); ?></span>
                <div class="d-flex">
                    <form action="/proyectoWebCS/Controllers/carritoController.php" method="POST" class="me-1">
                        <input type="hidden" name="accion" value="agregar">
                        <input type="hidden" name="idProducto" value="<?php echo $producto['idProducto']; ?>">
                        <button type="submit" class="btn btn-sm btn-color" title="Agregar al carrito">
                            <i class="bi bi-cart-plus"></i>
                        </button>
                    </form>
                    <a href="/proyectoWebCS/producto/<?php echo slugify($producto['nombreProducto']); ?>" class="btn btn-sm btn-color" title="Ver producto">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
