<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/productosModel.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/ventaModel.php";

function EsPeticionAjax()
{
    $xRequestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    if (is_string($xRequestedWith) && strcasecmp($xRequestedWith, 'XMLHttpRequest') === 0) {
        return true;
    }

    return ($_POST['ajax'] ?? '') === '1';
}

function ResponderJson($payload, $httpCode = 200)
{
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit();
}

function ObtenerEstadoCarritoAjax()
{
    $productosCarrito = ObtenerCarrito();
    $items = [];
    $totalCarrito = 0;
    $totalItems = 0;

    foreach ($productosCarrito as $item) {
        $idProducto = strval(intval($item['idProducto']));
        $cantidad = intval($item['cantidad']);
        $subtotal = floatval($item['subtotal']);
        $precio = floatval($item['precioProducto']);
        $stock = intval($item['stockProducto']);

        $items[$idProducto] = [
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
            'precio' => $precio,
            'stock' => $stock
        ];

        $totalItems += $cantidad;
        $totalCarrito += $subtotal;
    }

    return [
        'items' => $items,
        'totalItems' => $totalItems,
        'totalCarrito' => $totalCarrito
    ];
}

function NormalizarTelefonoEnvio($telefono)
{
    $soloDigitos = preg_replace('/\D+/', '', $telefono);
    if (!is_string($soloDigitos)) {
        return '';
    }

    return substr($soloDigitos, 0, 8);
}

function FormatearTelefonoEnvio($telefonoSoloDigitos)
{
    if (strlen($telefonoSoloDigitos) !== 8) {
        return $telefonoSoloDigitos;
    }

    return substr($telefonoSoloDigitos, 0, 4) . '-' . substr($telefonoSoloDigitos, 4);
}

function ObtenerMensajeValidacionTelefonoEnvio($telefonoEnvio)
{
    $telefonoEnvio = trim($telefonoEnvio);

    if ($telefonoEnvio === '') {
        return 'El telefono de contacto es obligatorio.';
    }

    if (!preg_match('/^[0-9\-\s]+$/', $telefonoEnvio)) {
        return 'El telefono solo permite numeros, guion o espacio.';
    }

    if (strlen($telefonoEnvio) > 9) {
        return 'El telefono permite maximo 9 caracteres.';
    }

    $telefonoSoloDigitos = NormalizarTelefonoEnvio($telefonoEnvio);
    if (strlen($telefonoSoloDigitos) !== 8) {
        return 'El telefono debe tener exactamente 8 digitos.';
    }

    return '';
}

function ObtenerEstadoProductoParaCarrito($idProducto)
{
    $producto = ObtenerProductoPorIdModel(intval($idProducto));
    if (!$producto) {
        return [
            'ok' => false,
            'mensaje' => 'Producto no encontrado.',
            'stock' => 0
        ];
    }

    $stock = intval($producto['stockProducto'] ?? 0);
    $estado = strtolower(trim($producto['estadoProducto'] ?? ''));

    if ($stock <= 0 || $estado === 'agotado') {
        return [
            'ok' => false,
            'mensaje' => 'Este producto esta agotado.',
            'stock' => 0
        ];
    }

    return [
        'ok' => true,
        'stock' => $stock,
        'producto' => $producto
    ];
}

function AgregarAlCarrito($idProducto, $cantidad = 1)
{
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $idProducto = intval($idProducto);
    $cantidad = max(1, intval($cantidad));

    if ($idProducto <= 0) {
        return [
            'ok' => false,
            'mensaje' => 'Producto invalido.',
            'stock' => 0
        ];
    }

    $estadoProducto = ObtenerEstadoProductoParaCarrito($idProducto);
    if (!$estadoProducto['ok']) {
        return [
            'ok' => false,
            'mensaje' => $estadoProducto['mensaje'],
            'stock' => intval($estadoProducto['stock'] ?? 0)
        ];
    }

    $stock = intval($estadoProducto['stock']);
    $cantidadActual = intval($_SESSION['carrito'][$idProducto] ?? 0);

    if ($cantidadActual >= $stock) {
        return [
            'ok' => false,
            'mensaje' => 'Solo hay ' . $stock . ' unidades disponibles.',
            'cantidad' => $cantidadActual,
            'stock' => $stock
        ];
    }

    $cantidadNueva = min($stock, $cantidadActual + $cantidad);
    $_SESSION['carrito'][$idProducto] = $cantidadNueva;

    return [
        'ok' => true,
        'mensaje' => ($cantidadNueva < ($cantidadActual + $cantidad))
            ? 'Solo hay ' . $stock . ' unidades disponibles.'
            : 'Producto agregado.',
        'cantidad' => $cantidadNueva,
        'stock' => $stock
    ];
}

function VaciarCarrito()
{
    $_SESSION['carrito'] = [];
}

function EliminarDelCarrito($idProducto)
{
    if (isset($_SESSION['carrito'][$idProducto])) {
        unset($_SESSION['carrito'][$idProducto]);
    }
}

function ActualizarCantidadCarrito($idProducto, $cantidad)
{
    $idProducto = intval($idProducto);

    if ($idProducto <= 0) {
        return [
            'ok' => false,
            'mensaje' => 'Producto invalido.',
            'stock' => 0
        ];
    }

    if ($cantidad <= 0) {
        EliminarDelCarrito($idProducto);

        return [
            'ok' => true,
            'mensaje' => 'Producto eliminado.',
            'cantidad' => 0,
            'stock' => 0
        ];
    }

    $estadoProducto = ObtenerEstadoProductoParaCarrito($idProducto);
    if (!$estadoProducto['ok']) {
        EliminarDelCarrito($idProducto);

        return [
            'ok' => false,
            'mensaje' => $estadoProducto['mensaje'],
            'cantidad' => 0,
            'stock' => 0
        ];
    }

    $stock = intval($estadoProducto['stock']);
    $cantidadSolicitada = intval($cantidad);
    $cantidadFinal = min($stock, $cantidadSolicitada);
    $_SESSION['carrito'][$idProducto] = $cantidadFinal;

    return [
        'ok' => true,
        'mensaje' => ($cantidadFinal < $cantidadSolicitada)
            ? 'Solo hay ' . $stock . ' unidades disponibles.'
            : 'Cantidad actualizada.',
        'cantidad' => $cantidadFinal,
        'stock' => $stock
    ];
}

function ObtenerCarrito()
{
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return [];
    }

    $productosCarrito = [];
    foreach ($_SESSION['carrito'] as $idProducto => $cantidad) {
        $producto = ObtenerProductoPorIdModel(intval($idProducto));
        if (!$producto) {
            unset($_SESSION['carrito'][$idProducto]);
            continue;
        }

        $stockProducto = intval($producto['stockProducto'] ?? 0);
        if ($stockProducto <= 0) {
            unset($_SESSION['carrito'][$idProducto]);
            continue;
        }

        $cantidadLimpia = max(1, intval($cantidad));
        $cantidadFinal = min($cantidadLimpia, $stockProducto);
        $_SESSION['carrito'][$idProducto] = $cantidadFinal;

        $producto['cantidad'] = $cantidadFinal;
        $producto['subtotal'] = $producto['precioProducto'] * $cantidadFinal;
        $productosCarrito[] = $producto;
    }

    return $productosCarrito;
}

function ObtenerTotalItemsCarrito()
{
    if (!isset($_SESSION['carrito'])) {
        return 0;
    }
    return array_sum($_SESSION['carrito']);
}

function ObtenerTotalCarrito()
{
    $carrito = ObtenerCarrito();
    $total = 0;
    foreach ($carrito as $item) {
        $total += $item['subtotal'];
    }
    return $total;
}

function PrepararItemsCompraCarrito()
{
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return [];
    }

    $items = [];
    foreach ($_SESSION['carrito'] as $idProducto => $cantidad) {
        $idLimpio = intval($idProducto);
        $cantidadLimpia = intval($cantidad);

        if ($idLimpio > 0 && $cantidadLimpia > 0) {
            $items[] = [
                'idProducto' => $idLimpio,
                'cantidad' => $cantidadLimpia
            ];
        }
    }

    return $items;
}

function ObtenerDatosEnvioFormulario()
{
    return $_SESSION['envio_form'] ?? [];
}

function GuardarDatosEnvioFormulario($nombreDestinatario, $telefonoEnvio, $direccionEnvio)
{
    $_SESSION['envio_form'] = [
        'nombreDestinatario' => substr(trim($nombreDestinatario), 0, 100),
        'telefonoEnvio' => substr(trim($telefonoEnvio), 0, 9),
        'direccionEnvio' => substr(trim($direccionEnvio), 0, 350)
    ];
}

function LimpiarDatosEnvioFormulario()
{
    unset($_SESSION['envio_form']);
}

function ComprarCarrito($idUsuario, $datosEnvio)
{
    $itemsCompra = PrepararItemsCompraCarrito();
    if (empty($itemsCompra)) {
        return [
            'ok' => false,
            'mensaje' => 'Tu carrito esta vacio.'
        ];
    }

    return RegistrarCompraCarritoModel($idUsuario, $itemsCompra, $datosEnvio);
}

function ObtenerTokenPago()
{
    if (empty($_SESSION['token_pago'])) {
        try {
            $_SESSION['token_pago'] = bin2hex(random_bytes(32));
        } catch (Throwable $error) {
            $_SESSION['token_pago'] = hash('sha256', uniqid((string) mt_rand(), true));
        }
    }

    return $_SESSION['token_pago'];
}

function RegenerarTokenPago()
{
    unset($_SESSION['token_pago']);
    return ObtenerTokenPago();
}

function ValidarTokenPago($tokenRecibido)
{
    if (!is_string($tokenRecibido) || $tokenRecibido === '') {
        return false;
    }

    if (!isset($_SESSION['token_pago'])) {
        return false;
    }

    return hash_equals($_SESSION['token_pago'], $tokenRecibido);
}

function EsRedireccionInterna($url)
{
    if ($url === '') {
        return false;
    }

    if (strpos($url, '/proyectoWebCS/') === 0) {
        return true;
    }

    $partes = parse_url($url);
    if ($partes === false || !isset($partes['host'])) {
        return false;
    }

    $hostActual = $_SERVER['HTTP_HOST'] ?? '';
    if ($hostActual === '' || strcasecmp($partes['host'], $hostActual) !== 0) {
        return false;
    }

    $ruta = $partes['path'] ?? '';
    return strpos($ruta, '/proyectoWebCS/') === 0;
}

function ObtenerRedireccionSegura($referer, $redireccionDefecto)
{
    if (is_string($referer) && EsRedireccionInterna($referer)) {
        return $referer;
    }

    return $redireccionDefecto;
}

function ObtenerMensajeValidacionPago($nombreTitular, $numeroTarjeta, $vencimiento, $cvv)
{
    if ($nombreTitular === '') {
        return 'Ingresa el nombre del titular.';
    }

    if (!preg_match('/^\d{12,19}$/', $numeroTarjeta)) {
        return 'Ingresa un numero de tarjeta entre 12 y 19 digitos.';
    }

    if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $vencimiento)) {
        return 'La fecha de vencimiento no es valida.';
    }

    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        return 'El CVV no es valido.';
    }

    return '';
}

// Procesar peticiones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $redireccionDefecto = '/proyectoWebCS/Views/Home/carrito.php';
    $redireccion = ObtenerRedireccionSegura($_SERVER['HTTP_REFERER'] ?? '', $redireccionDefecto);

    switch ($accion) {
        case 'agregar':
            $idProducto = intval($_POST['idProducto'] ?? 0);
            $resultadoAgregar = [
                'ok' => false,
                'mensaje' => 'Producto invalido.'
            ];

            if ($idProducto > 0) {
                $resultadoAgregar = AgregarAlCarrito($idProducto);
            }

            if (EsPeticionAjax()) {
                if (!$resultadoAgregar['ok']) {
                    ResponderJson([
                        'ok' => false,
                        'mensaje' => $resultadoAgregar['mensaje'] ?? 'No se pudo agregar el producto.'
                    ], 400);
                }

                ResponderJson(array_merge([
                    'ok' => true,
                    'mensaje' => $resultadoAgregar['mensaje'] ?? 'Producto agregado.'
                ], ObtenerEstadoCarritoAjax()));
            }

            if (!$resultadoAgregar['ok']) {
                $_SESSION['error_carrito'] = $resultadoAgregar['mensaje'] ?? 'No se pudo agregar el producto.';
            }
            break;
        case 'eliminar':
            $idProducto = intval($_POST['idProducto'] ?? 0);
            if ($idProducto > 0) {
                EliminarDelCarrito($idProducto);
            }

            if (EsPeticionAjax()) {
                if ($idProducto <= 0) {
                    ResponderJson([
                        'ok' => false,
                        'mensaje' => 'Producto invalido.'
                    ], 400);
                }

                ResponderJson(array_merge([
                    'ok' => true,
                    'mensaje' => 'Producto eliminado.'
                ], ObtenerEstadoCarritoAjax()));
            }
            break;
        case 'actualizar':
            $idProducto = intval($_POST['idProducto'] ?? 0);
            $cantidad = intval($_POST['cantidad'] ?? 1);
            $resultadoActualizar = [
                'ok' => false,
                'mensaje' => 'Producto invalido.'
            ];

            if ($idProducto > 0) {
                $resultadoActualizar = ActualizarCantidadCarrito($idProducto, $cantidad);
            }

            if (EsPeticionAjax()) {
                if ($idProducto <= 0) {
                    ResponderJson([
                        'ok' => false,
                        'mensaje' => 'Producto invalido.'
                    ], 400);
                }

                ResponderJson(array_merge([
                    'ok' => true,
                    'mensaje' => $resultadoActualizar['mensaje'] ?? 'Cantidad actualizada.'
                ], ObtenerEstadoCarritoAjax()));
            }

            if (!$resultadoActualizar['ok']) {
                $_SESSION['error_carrito'] = $resultadoActualizar['mensaje'] ?? 'No se pudo actualizar la cantidad.';
            }
            break;
        case 'guardar_envio':
            if (!isset($_SESSION['idUsuario'])) {
                $_SESSION['error_carrito'] = 'Necesitas iniciar sesion para continuar.';
                header("Location: /proyectoWebCS/Views/Registro/login.php");
                exit();
            }

            $nombreDestinatario = trim($_POST['nombreDestinatario'] ?? '');
            $telefonoEnvio = trim($_POST['telefonoEnvio'] ?? '');
            $direccionEnvio = trim($_POST['direccionEnvio'] ?? '');
            $mensajeTelefono = ObtenerMensajeValidacionTelefonoEnvio($telefonoEnvio);

            if ($nombreDestinatario === '' || $telefonoEnvio === '' || $direccionEnvio === '') {
                $_SESSION['error_carrito'] = 'Completa todos los datos de envio.';
                $redireccion = '/proyectoWebCS/Views/Home/checkout.php';
                break;
            }

            if ($mensajeTelefono !== '') {
                $_SESSION['error_carrito'] = $mensajeTelefono;
                $redireccion = '/proyectoWebCS/Views/Home/checkout.php';
                break;
            }

            $telefonoFormateado = FormatearTelefonoEnvio(NormalizarTelefonoEnvio($telefonoEnvio));
            GuardarDatosEnvioFormulario($nombreDestinatario, $telefonoFormateado, $direccionEnvio);
            $_SESSION['ok_carrito'] = 'Direccion de envio guardada.';
            $redireccion = '/proyectoWebCS/Views/Home/pago.php';
            break;
        case 'comprar':
            if (!isset($_SESSION['idUsuario'])) {
                $_SESSION['error_carrito'] = 'Necesitas iniciar sesion para completar la compra.';
                header("Location: /proyectoWebCS/Views/Registro/login.php");
                exit();
            }

            $datosEnvio = ObtenerDatosEnvioFormulario();
            if (empty($datosEnvio['nombreDestinatario']) || empty($datosEnvio['telefonoEnvio']) || empty($datosEnvio['direccionEnvio'])) {
                $_SESSION['error_carrito'] = 'Completa la direccion de envio antes de pagar.';
                $redireccion = '/proyectoWebCS/Views/Home/checkout.php';
                break;
            }

            $tokenPago = $_POST['token_pago'] ?? '';
            if (!ValidarTokenPago($tokenPago)) {
                $_SESSION['error_carrito'] = 'La sesion de pago vencio. Intenta de nuevo.';
                RegenerarTokenPago();
                $redireccion = '/proyectoWebCS/Views/Home/pago.php';
                break;
            }

            $nombreTitular = trim($_POST['nombreTitular'] ?? '');
            $numeroTarjeta = preg_replace('/\D+/', '', $_POST['numeroTarjeta'] ?? '');
            $vencimiento = trim($_POST['vencimiento'] ?? '');
            $cvv = trim($_POST['cvv'] ?? '');

            $_SESSION['pago_form'] = [
                'nombreTitular' => $nombreTitular,
                'vencimiento' => $vencimiento
            ];

            $mensajeValidacion = ObtenerMensajeValidacionPago($nombreTitular, $numeroTarjeta, $vencimiento, $cvv);
            if ($mensajeValidacion !== '') {
                $_SESSION['error_carrito'] = $mensajeValidacion;
                RegenerarTokenPago();
                $redireccion = '/proyectoWebCS/Views/Home/pago.php';
                break;
            }

            $resultadoCompra = ComprarCarrito(intval($_SESSION['idUsuario']), $datosEnvio);
            if ($resultadoCompra['ok']) {
                VaciarCarrito();
                $idVenta = intval($resultadoCompra['idVenta']);
                $idPedido = intval($resultadoCompra['idPedido'] ?? 0);
                $tarjetaEnmascarada = '**** **** **** ' . substr($numeroTarjeta, -4);
                $_SESSION['resumen_pago'] = [
                    'idPedido' => $idPedido,
                    'idVenta' => $idVenta,
                    'total' => floatval($resultadoCompra['total'] ?? 0),
                    'tarjeta' => $tarjetaEnmascarada,
                    'titular' => $nombreTitular,
                    'estadoPedido' => $resultadoCompra['estadoPedido'] ?? 'pendiente',
                    'nombreDestinatario' => $datosEnvio['nombreDestinatario'],
                    'telefonoEnvio' => $datosEnvio['telefonoEnvio'],
                    'direccionEnvio' => $datosEnvio['direccionEnvio'],
                    'fecha' => date('Y-m-d H:i:s')
                ];
                $_SESSION['ok_carrito'] = 'Compra realizada con exito. Numero de venta #' . $idVenta . '.';
                unset($_SESSION['pago_form']);
                LimpiarDatosEnvioFormulario();
                RegenerarTokenPago();
                $redireccion = '/proyectoWebCS/Views/Home/confirmacionPago.php';
            } else {
                $_SESSION['error_carrito'] = $resultadoCompra['mensaje'] ?? 'No se pudo procesar la compra.';
                $redireccion = '/proyectoWebCS/Views/Home/pago.php';
            }
            break;
    }

    header("Location: " . $redireccion);
    exit();
}
