<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/verificarSesion.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/carritoController.php";

$productosCarrito = ObtenerCarrito();
$totalCarrito = ObtenerTotalCarrito();
$totalItems = ObtenerTotalItemsCarrito();
$tokenPago = ObtenerTokenPago();
$datosPagoForm = $_SESSION['pago_form'] ?? [];
$nombreTitularForm = $datosPagoForm['nombreTitular'] ?? '';
$vencimientoForm = $datosPagoForm['vencimiento'] ?? '';

if (empty($productosCarrito)) {
    $_SESSION['error_carrito'] = 'Tu carrito esta vacio.';
    header('Location: /proyectoWebCS/Views/Home/carrito.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago | My Pc Gaming</title>

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body class="bg-light">
    <?php require('../components/nav.php') ?>

    <div class="container my-5">
        <h3 class="mb-4"><i class="bi bi-credit-card-2-front me-2"></i>Datos de pago</h3>

        <?php if (isset($_SESSION['error_carrito'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error_carrito']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php unset($_SESSION['error_carrito']); endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <form action="/proyectoWebCS/Controllers/carritoController.php" method="POST">
                            <input type="hidden" name="accion" value="comprar">
                            <input type="hidden" name="token_pago" value="<?= htmlspecialchars($tokenPago) ?>">

                            <div class="mb-3">
                                <label for="nombreTitular" class="form-label">Nombre del titular</label>
                                <input type="text" class="form-control" id="nombreTitular" name="nombreTitular" maxlength="100" value="<?= htmlspecialchars($nombreTitularForm) ?>" autocomplete="cc-name" required>
                            </div>

                            <div class="mb-3">
                                <label for="numeroTarjeta" class="form-label">Numero de tarjeta</label>
                                <input type="text" class="form-control" id="numeroTarjeta" name="numeroTarjeta" inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000" autocomplete="cc-number" required>
                                <div class="form-text">No se guarda la tarjeta. Solo se valida para simular el pago.</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="vencimiento" class="form-label">Vencimiento (MM/AA)</label>
                                    <input type="text" class="form-control" id="vencimiento" name="vencimiento" maxlength="5" placeholder="MM/AA" value="<?= htmlspecialchars($vencimientoForm) ?>" autocomplete="cc-exp" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="password" class="form-control" id="cvv" name="cvv" inputmode="numeric" maxlength="4" placeholder="***" autocomplete="cc-csc" required>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <a href="/proyectoWebCS/Views/Home/carrito.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Volver al carrito
                                </a>
                                <button type="submit" class="btn text-white" style="background-color: var(--primer-color);">
                                    <i class="bi bi-lock-fill me-1"></i>Pagar ahora
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header text-white text-center py-3" style="background-color: var(--primer-color);">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Resumen del pedido</h5>
                    </div>
                    <div class="card-body p-4">
                        <ul class="list-group list-group-flush mb-3">
                            <?php foreach ($productosCarrito as $item): ?>
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="me-2"><?= htmlspecialchars($item['nombreProducto']) ?> x<?= intval($item['cantidad']) ?></span>
                                    <span>₡<?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Productos (<?= $totalItems ?>)</span>
                            <span>₡<?= number_format($totalCarrito, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Envio</span>
                            <span class="text-success">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total</span>
                            <span style="color: var(--primer-color);">₡<?= number_format($totalCarrito, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('../components/footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        const inputTarjeta = document.getElementById('numeroTarjeta');
        const inputVencimiento = document.getElementById('vencimiento');
        const inputCvv = document.getElementById('cvv');

        inputTarjeta.addEventListener('input', function () {
            const limpio = this.value.replace(/\D/g, '').slice(0, 16);
            this.value = limpio.replace(/(.{4})/g, '$1 ').trim();
        });

        inputVencimiento.addEventListener('input', function () {
            const limpio = this.value.replace(/\D/g, '').slice(0, 4);
            if (limpio.length >= 3) {
                this.value = limpio.slice(0, 2) + '/' + limpio.slice(2);
            } else {
                this.value = limpio;
            }
        });

        inputCvv.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 4);
        });
    </script>
</body>

</html>
