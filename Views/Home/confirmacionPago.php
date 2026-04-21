<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/verificarSesion.php";

$resumenPago = $_SESSION['resumen_pago'] ?? null;
if (!$resumenPago) {
    $_SESSION['error_carrito'] = 'No hay un pago reciente para mostrar.';
    header('Location: /proyectoWebCS/Views/Home/carrito.php');
    exit();
}

$idVenta = intval($resumenPago['idVenta'] ?? 0);
$idPedido = intval($resumenPago['idPedido'] ?? 0);
$totalPago = floatval($resumenPago['total'] ?? 0);
$tarjetaPago = $resumenPago['tarjeta'] ?? '****';
$titularPago = $resumenPago['titular'] ?? '';
$estadoPedido = $resumenPago['estadoPedido'] ?? 'pendiente';
$nombreDestinatario = $resumenPago['nombreDestinatario'] ?? '';
$telefonoEnvio = $resumenPago['telefonoEnvio'] ?? '';
$direccionEnvio = $resumenPago['direccionEnvio'] ?? '';
$fechaPagoRaw = $resumenPago['fecha'] ?? '';

$fechaPago = $fechaPagoRaw;
if ($fechaPagoRaw !== '') {
    try {
        $fechaObj = new DateTime($fechaPagoRaw);
        $fechaPago = $fechaObj->format('d/m/Y H:i');
    } catch (Throwable $error) {
        $fechaPago = $fechaPagoRaw;
    }
}

unset($_SESSION['resumen_pago']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago confirmado | My Pc Gaming</title>

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body class="bg-light">
    <?php require('../components/nav.php') ?>
    

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 72px; height: 72px; background-color: #eafaf1; color: #198754;">
                                <i class="bi bi-check2-circle fs-1"></i>
                            </div>
                            <h3 class="mb-2">Pago realizado con exito</h3>
                            <p class="text-muted mb-0">Tu pedido quedo registrado y esta en estado pendiente de envio.</p>
                        </div>

                        <div class="border rounded-3 p-3 p-md-4 mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Numero de pedido</span>
                                <span class="fw-semibold">#<?= $idPedido ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Numero de venta</span>
                                <span class="fw-semibold">#<?= $idVenta ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Estado del pedido</span>
                                <span class="badge rounded-pill <?= $estadoPedido === 'completado' ? 'text-bg-success' : 'text-bg-warning' ?>">
                                    <?= htmlspecialchars(ucfirst($estadoPedido)) ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total pagado</span>
                                <span class="fw-semibold" style="color: var(--primer-color);">₡<?= number_format($totalPago, 0, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tarjeta</span>
                                <span><?= htmlspecialchars($tarjetaPago) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Titular</span>
                                <span><?= htmlspecialchars($titularPago) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Fecha</span>
                                <span><?= htmlspecialchars($fechaPago) ?></span>
                            </div>
                            <hr>
                            <div>
                                <p class="text-muted mb-1">Envio a:</p>
                                <p class="mb-1 fw-semibold"><?= htmlspecialchars($nombreDestinatario) ?></p>
                                <p class="mb-1"><?= htmlspecialchars($telefonoEnvio) ?></p>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($direccionEnvio)) ?></p>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="/proyectoWebCS/Views/Home/Home.php" class="btn text-white" style="background-color: var(--primer-color);">
                                <i class="bi bi-shop me-1"></i>Seguir comprando
                            </a>
                            <a href="/proyectoWebCS/Views/usuario/pedidos.php" class="btn btn-outline-primary">
                                <i class="bi bi-clock-history me-1"></i>Ver mis pedidos
                            </a>
                            <a href="/proyectoWebCS/Views/Home/carrito.php" class="btn btn-outline-secondary">
                                <i class="bi bi-cart3 me-1"></i>Ir al carrito
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('../components/footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
