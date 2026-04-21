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
$datosEnvio = ObtenerDatosEnvioFormulario();

$nombreDestinatario = $datosEnvio['nombreDestinatario'] ?? '';
$telefonoEnvio = $datosEnvio['telefonoEnvio'] ?? '';
$direccionEnvio = $datosEnvio['direccionEnvio'] ?? '';

if (empty($productosCarrito)) {
    $_SESSION['error_carrito'] = 'Tu carrito esta vacio.';
    header('Location: /proyectoWebCS/Views/Home/carrito.php');
    exit();
}

if ($nombreDestinatario === '' || $telefonoEnvio === '' || $direccionEnvio === '') {
    $_SESSION['error_carrito'] = 'Completa la direccion de envio antes de pagar.';
    header('Location: /proyectoWebCS/Views/Home/checkout.php');
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
    <style>
        .tarjeta-preview {
            border-radius: 20px;
            padding: 1.4rem;
            color: #fff;
            background: linear-gradient(130deg, #2f3f52, #4c6078);
            position: relative;
            overflow: hidden;
            transition: all 0.35s ease;
        }

        .tarjeta-preview::after {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            right: -50px;
            top: -50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
        }

        .tarjeta-preview[data-brand="visa"] {
            background: linear-gradient(130deg, #0b1e73, #1f5fbf);
        }

        .tarjeta-preview[data-brand="mastercard"] {
            background: linear-gradient(130deg, #8f1b13, #e26a1b);
        }

        .tarjeta-preview[data-brand="amex"] {
            background: linear-gradient(130deg, #0a6f8e, #33a7b6);
        }

        .brand-chip {
            display: inline-block;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.72rem;
            letter-spacing: 0.4px;
            margin-left: 0.2rem;
            transition: all 0.25s ease;
        }

        .brand-chip.is-active {
            background: #fff;
            color: #111;
            border-color: #fff;
            transform: translateY(-1px);
        }

        .tarjeta-numero {
            font-size: 1.35rem;
            letter-spacing: 1.6px;
            font-weight: 700;
            margin: 0.8rem 0 1rem;
        }

        .tarjeta-label {
            margin: 0;
            font-size: 0.72rem;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tarjeta-value {
            margin: 0;
            font-weight: 600;
        }
    </style>
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

                            <div class="tarjeta-preview mb-3" id="tarjetaPreview" data-brand="unknown">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="tarjeta-label">Marca detectada</p>
                                        <p class="tarjeta-value" id="marcaTarjetaTexto">Sin detectar</p>
                                    </div>
                                    <div>
                                        <span class="brand-chip" data-chip-brand="visa">VISA</span>
                                        <span class="brand-chip" data-chip-brand="mastercard">MASTERCARD</span>
                                        <span class="brand-chip" data-chip-brand="amex">AMEX</span>
                                    </div>
                                </div>

                                <p class="tarjeta-numero" id="numeroTarjetaPreview">#### #### #### ####</p>

                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <p class="tarjeta-label">Titular</p>
                                        <p class="tarjeta-value" id="titularTarjetaPreview">NOMBRE TITULAR</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="tarjeta-label">Vence</p>
                                        <p class="tarjeta-value" id="venceTarjetaPreview">MM/AA</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded-3 p-3 mb-3 bg-light-subtle">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0"><i class="bi bi-truck me-1"></i>Direccion de envio</h6>
                                    <a href="/proyectoWebCS/Views/Home/checkout.php" class="small">Editar</a>
                                </div>
                                <p class="mb-1 fw-semibold"><?= htmlspecialchars($nombreDestinatario) ?></p>
                                <p class="mb-1 text-muted"><?= htmlspecialchars($telefonoEnvio) ?></p>
                                <p class="mb-0 text-muted"><?= nl2br(htmlspecialchars($direccionEnvio)) ?></p>
                            </div>

                            <div class="mb-3">
                                <label for="nombreTitular" class="form-label">Nombre del titular</label>
                                <input type="text" class="form-control" id="nombreTitular" name="nombreTitular" maxlength="100" value="<?= htmlspecialchars($nombreTitularForm) ?>" autocomplete="cc-name" required>
                            </div>

                            <div class="mb-3">
                                <label for="numeroTarjeta" class="form-label">Numero de tarjeta</label>
                                <input type="text" class="form-control" id="numeroTarjeta" name="numeroTarjeta" inputmode="numeric" maxlength="23" placeholder="0000 0000 0000 0000" autocomplete="cc-number" required>
                                <div class="form-text">No se guarda la tarjeta. Puedes usar cualquier numero para simular el pago.</div>
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
                                <a href="/proyectoWebCS/Views/Home/checkout.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Volver al checkout
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
        const inputNombre = document.getElementById('nombreTitular');
        const inputTarjeta = document.getElementById('numeroTarjeta');
        const inputVencimiento = document.getElementById('vencimiento');
        const inputCvv = document.getElementById('cvv');

        const tarjetaPreview = document.getElementById('tarjetaPreview');
        const marcaTarjetaTexto = document.getElementById('marcaTarjetaTexto');
        const numeroTarjetaPreview = document.getElementById('numeroTarjetaPreview');
        const titularTarjetaPreview = document.getElementById('titularTarjetaPreview');
        const venceTarjetaPreview = document.getElementById('venceTarjetaPreview');
        const chipsMarca = document.querySelectorAll('[data-chip-brand]');

        function detectarMarcaTarjeta(numero) {
            if (/^4/.test(numero)) return 'visa';
            if (/^(34|37)/.test(numero)) return 'amex';
            if (/^(5[1-5]|2(2[2-9]|[3-6][0-9]|7[01]|720))/.test(numero)) return 'mastercard';
            return 'unknown';
        }

        function nombreMarcaBonito(marca) {
            if (marca === 'visa') return 'Visa';
            if (marca === 'mastercard') return 'Mastercard';
            if (marca === 'amex') return 'American Express';
            return 'Sin detectar';
        }

        function formatearNumeroTarjeta(numero, marca) {
            if (marca === 'amex') {
                const p1 = numero.slice(0, 4);
                const p2 = numero.slice(4, 10);
                const p3 = numero.slice(10, 15);
                return [p1, p2, p3].filter(Boolean).join(' ');
            }

            return numero.replace(/(.{4})/g, '$1 ').trim();
        }

        function actualizarMarcaUI(marca) {
            tarjetaPreview.dataset.brand = marca;
            marcaTarjetaTexto.textContent = nombreMarcaBonito(marca);

            chipsMarca.forEach((chip) => {
                chip.classList.toggle('is-active', chip.dataset.chipBrand === marca);
            });
        }

        function actualizarNumeroPreview(valorFormateado) {
            numeroTarjetaPreview.textContent = valorFormateado !== '' ? valorFormateado : '#### #### #### ####';
        }

        inputTarjeta.addEventListener('input', function () {
            const limpio = this.value.replace(/\D/g, '').slice(0, 19);
            const marca = detectarMarcaTarjeta(limpio);
            const formateado = formatearNumeroTarjeta(limpio, marca);

            this.value = formateado;
            actualizarMarcaUI(marca);
            actualizarNumeroPreview(formateado);
        });

        inputVencimiento.addEventListener('input', function () {
            const limpio = this.value.replace(/\D/g, '').slice(0, 4);
            if (limpio.length >= 3) {
                this.value = limpio.slice(0, 2) + '/' + limpio.slice(2);
            } else {
                this.value = limpio;
            }

            venceTarjetaPreview.textContent = this.value !== '' ? this.value : 'MM/AA';
        });

        inputCvv.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 4);
        });

        inputNombre.addEventListener('input', function () {
            const nombre = this.value.trim().toUpperCase();
            titularTarjetaPreview.textContent = nombre !== '' ? nombre : 'NOMBRE TITULAR';
        });

        if (inputNombre.value.trim() !== '') {
            titularTarjetaPreview.textContent = inputNombre.value.trim().toUpperCase();
        }
        if (inputVencimiento.value.trim() !== '') {
            venceTarjetaPreview.textContent = inputVencimiento.value.trim();
        }
    </script>
</body>

</html>
