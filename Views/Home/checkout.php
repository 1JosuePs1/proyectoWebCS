<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/verificarSesion.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/carritoController.php";

$productosCarrito = ObtenerCarrito();
$totalCarrito = ObtenerTotalCarrito();
$totalItems = ObtenerTotalItemsCarrito();
$datosEnvioForm = ObtenerDatosEnvioFormulario();

$nombreDestinatarioForm = $datosEnvioForm['nombreDestinatario'] ?? ($_SESSION['nombreCompleto'] ?? '');
$telefonoEnvioForm = $datosEnvioForm['telefonoEnvio'] ?? '';
$direccionEnvioForm = $datosEnvioForm['direccionEnvio'] ?? '';

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
    <title>Checkout | My Pc Gaming</title>

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        .checkout-item-thumb {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 8px;
            background: #f8f9fa;
            padding: 4px;
        }

        .qty-pill {
            min-width: 120px;
        }

        .qty-pill .btn {
            border: 0;
        }

        .qty-pill input {
            max-width: 48px;
            border: 0;
            background: transparent;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">
    <?php require('../components/nav.php') ?>

    <div class="container my-5">
        <h3 class="mb-4"><i class="bi bi-geo-alt me-2"></i>Checkout - Datos de envio</h3>

        <?php if (isset($_SESSION['ok_carrito'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['ok_carrito']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php unset($_SESSION['ok_carrito']); endif; ?>

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
                        <form action="/proyectoWebCS/Controllers/carritoController.php" method="POST" id="formEnvio">
                            <input type="hidden" name="accion" value="guardar_envio">

                            <div class="mb-3">
                                <label for="nombreDestinatario" class="form-label">Nombre del destinatario</label>
                                <input type="text" class="form-control" id="nombreDestinatario" name="nombreDestinatario" maxlength="100" value="<?= htmlspecialchars($nombreDestinatarioForm) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefonoEnvio" class="form-label">Telefono de contacto</label>
                                <input type="text" class="form-control" id="telefonoEnvio" name="telefonoEnvio" maxlength="9" inputmode="numeric" placeholder="8888-8888" value="<?= htmlspecialchars($telefonoEnvioForm) ?>" required>
                                <div class="form-text">8 digitos. Puedes escribirlo como 8888-8888.</div>
                            </div>

                            <div class="mb-3">
                                <label for="direccionEnvio" class="form-label">Direccion de envio</label>
                                <textarea class="form-control" id="direccionEnvio" name="direccionEnvio" rows="4" maxlength="350" required><?= htmlspecialchars($direccionEnvioForm) ?></textarea>
                                <div class="form-text">Incluye provincia, canton, distrito y detalles para entrega.</div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <a href="/proyectoWebCS/Views/Home/carrito.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Volver al carrito
                                </a>
                                <button type="submit" class="btn text-white" style="background-color: var(--primer-color);">
                                    <i class="bi bi-arrow-right-circle me-1"></i>Continuar al pago
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
                        <div id="ajaxCheckoutError" class="alert alert-danger d-none py-2" role="alert"></div>

                        <ul class="list-group list-group-flush mb-3" id="checkoutItemsList">
                            <?php foreach ($productosCarrito as $item): ?>
                                <?php
                                    $imagenes = isset($item['imagenProducto']) ? json_decode($item['imagenProducto'], true) : [];
                                    $primeraImg = (!empty($imagenes)) ? $imagenes[0] : null;
                                ?>
                                <li class="list-group-item px-0" data-item-row data-id="<?= intval($item['idProducto']) ?>" data-stock="<?= intval($item['stockProducto']) ?>">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <?php if ($primeraImg): ?>
                                                <img src="/proyectoWebCS/Views/assets/image/productos/<?= intval($item['idProducto']) ?>/<?= htmlspecialchars($primeraImg) ?>" alt="<?= htmlspecialchars($item['nombreProducto']) ?>" class="checkout-item-thumb">
                                            <?php else: ?>
                                                <div class="checkout-item-thumb d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 small fw-semibold"><?= htmlspecialchars($item['nombreProducto']) ?></p>
                                            <p class="mb-2 text-muted small">₡<span data-precio><?= number_format($item['precioProducto'], 0, ',', '.') ?></span></p>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="d-inline-flex align-items-center border rounded-pill qty-pill overflow-hidden">
                                                    <button type="button" class="btn btn-sm btn-light" data-accion-carrito="menos" title="Restar">-</button>
                                                    <input type="text" value="<?= intval($item['cantidad']) ?>" data-cantidad readonly>
                                                    <button type="button" class="btn btn-sm btn-light" data-accion-carrito="mas" title="Sumar">+</button>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-link text-danger p-0" data-accion-carrito="eliminar">Quitar</button>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <p class="mb-0 small fw-semibold">₡<span data-subtotal><?= number_format($item['subtotal'], 0, ',', '.') ?></span></p>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Productos (<span id="checkoutItemsCount"><?= $totalItems ?></span>)</span>
                            <span>₡<span id="checkoutTotalMonto"><?= number_format($totalCarrito, 0, ',', '.') ?></span></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Envio</span>
                            <span class="text-success">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total</span>
                            <span style="color: var(--primer-color);">₡<span id="checkoutTotalGeneral"><?= number_format($totalCarrito, 0, ',', '.') ?></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('../components/footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        const controllerCarrito = '/proyectoWebCS/Controllers/carritoController.php';
        const contenedorItems = document.getElementById('checkoutItemsList');
        const errorAjax = document.getElementById('ajaxCheckoutError');
        const inputTelefono = document.getElementById('telefonoEnvio');
        const formEnvio = document.getElementById('formEnvio');

        const formatterMoneda = new Intl.NumberFormat('es-CR');

        function formatearMoneda(valor) {
            return formatterMoneda.format(Math.round(Number(valor) || 0));
        }

        function mostrarErrorAjax(mensaje) {
            if (!errorAjax) return;
            errorAjax.textContent = mensaje;
            errorAjax.classList.remove('d-none');
        }

        function limpiarErrorAjax() {
            if (!errorAjax) return;
            errorAjax.classList.add('d-none');
            errorAjax.textContent = '';
        }

        async function enviarCambioCarrito(accion, idProducto, cantidad) {
            const payload = new URLSearchParams();
            payload.append('accion', accion);
            payload.append('idProducto', idProducto);
            payload.append('ajax', '1');
            if (typeof cantidad === 'number') {
                payload.append('cantidad', String(cantidad));
            }

            const respuesta = await fetch(controllerCarrito, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: payload.toString()
            });

            let data = null;
            try {
                data = await respuesta.json();
            } catch (error) {
                data = null;
            }

            if (!respuesta.ok || !data || data.ok !== true) {
                const mensaje = (data && data.mensaje) ? data.mensaje : 'No se pudo actualizar el carrito.';
                throw new Error(mensaje);
            }

            return data;
        }

        function refrescarCheckout(data) {
            const items = data.items || {};

            document.querySelectorAll('[data-item-row]').forEach((fila) => {
                const id = fila.dataset.id;
                const item = items[id];

                if (!item) {
                    fila.remove();
                    return;
                }

                const inputCantidad = fila.querySelector('[data-cantidad]');
                const spanSubtotal = fila.querySelector('[data-subtotal]');
                const spanPrecio = fila.querySelector('[data-precio]');

                if (inputCantidad) inputCantidad.value = item.cantidad;
                if (spanSubtotal) spanSubtotal.textContent = formatearMoneda(item.subtotal);
                if (spanPrecio) spanPrecio.textContent = formatearMoneda(item.precio);

                fila.dataset.stock = item.stock;
            });

            const totalItems = Number(data.totalItems || 0);
            const totalCarrito = Number(data.totalCarrito || 0);

            document.getElementById('checkoutItemsCount').textContent = totalItems;
            document.getElementById('checkoutTotalMonto').textContent = formatearMoneda(totalCarrito);
            document.getElementById('checkoutTotalGeneral').textContent = formatearMoneda(totalCarrito);

            if (Object.keys(items).length === 0) {
                window.location.href = '/proyectoWebCS/Views/Home/carrito.php';
            }
        }

        if (contenedorItems) {
            contenedorItems.addEventListener('click', async (event) => {
                const boton = event.target.closest('button[data-accion-carrito]');
                if (!boton) return;

                const fila = boton.closest('[data-item-row]');
                if (!fila) return;

                const idProducto = fila.dataset.id;
                const stock = Number(fila.dataset.stock || 1);
                const inputCantidad = fila.querySelector('[data-cantidad]');
                const cantidadActual = Number(inputCantidad ? inputCantidad.value : 1);
                const accion = boton.dataset.accionCarrito;

                let accionBackend = 'actualizar';
                let nuevaCantidad = cantidadActual;

                if (accion === 'mas') {
                    if (cantidadActual >= stock) {
                        return;
                    }
                    nuevaCantidad = cantidadActual + 1;
                } else if (accion === 'menos') {
                    nuevaCantidad = cantidadActual - 1;
                    if (nuevaCantidad <= 0) {
                        accionBackend = 'eliminar';
                    }
                } else if (accion === 'eliminar') {
                    accionBackend = 'eliminar';
                }

                fila.classList.add('opacity-50');
                limpiarErrorAjax();

                try {
                    const data = await enviarCambioCarrito(accionBackend, idProducto, nuevaCantidad);
                    refrescarCheckout(data);
                } catch (error) {
                    mostrarErrorAjax(error.message || 'No se pudo actualizar el carrito.');
                } finally {
                    fila.classList.remove('opacity-50');
                }
            });
        }

        if (inputTelefono) {
            inputTelefono.addEventListener('input', function () {
                const limpio = this.value.replace(/\D/g, '').slice(0, 8);
                if (limpio.length > 4) {
                    this.value = limpio.slice(0, 4) + '-' + limpio.slice(4);
                } else {
                    this.value = limpio;
                }
                this.setCustomValidity('');
            });
        }

        if (formEnvio && inputTelefono) {
            formEnvio.addEventListener('submit', function (event) {
                const soloDigitos = inputTelefono.value.replace(/\D/g, '');
                if (soloDigitos.length !== 8) {
                    event.preventDefault();
                    inputTelefono.setCustomValidity('El telefono debe tener 8 digitos.');
                    inputTelefono.reportValidity();
                    return;
                }

                inputTelefono.setCustomValidity('');
            });
        }
    </script>
</body>

</html>
