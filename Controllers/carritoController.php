<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/productosModel.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/ventaModel.php";

function AgregarAlCarrito($idProducto, $cantidad = 1)
{
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if (isset($_SESSION['carrito'][$idProducto])) {
        $_SESSION['carrito'][$idProducto] += $cantidad;
    } else {
        $_SESSION['carrito'][$idProducto] = $cantidad;
    }
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
    if ($cantidad <= 0) {
        EliminarDelCarrito($idProducto);
    } else {
        $_SESSION['carrito'][$idProducto] = $cantidad;
    }
}

function ObtenerCarrito()
{
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return [];
    }

    $productosCarrito = [];
    foreach ($_SESSION['carrito'] as $idProducto => $cantidad) {
        $producto = ObtenerProductoPorIdModel(intval($idProducto));
        if ($producto) {
            $producto['cantidad'] = $cantidad;
            $producto['subtotal'] = $producto['precioProducto'] * $cantidad;
            $productosCarrito[] = $producto;
        }
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

function ComprarCarrito($idUsuario)
{
    $itemsCompra = PrepararItemsCompraCarrito();
    if (empty($itemsCompra)) {
        return [
            'ok' => false,
            'mensaje' => 'Tu carrito esta vacio.'
        ];
    }

    return RegistrarCompraCarritoModel($idUsuario, $itemsCompra);
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

function TarjetaPasaLuhn($numeroTarjeta)
{
    $suma = 0;
    $alternar = false;

    for ($i = strlen($numeroTarjeta) - 1; $i >= 0; $i--) {
        $digito = intval($numeroTarjeta[$i]);

        if ($alternar) {
            $digito *= 2;
            if ($digito > 9) {
                $digito -= 9;
            }
        }

        $suma += $digito;
        $alternar = !$alternar;
    }

    return ($suma % 10) === 0;
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

    if (!preg_match('/^\d{16}$/', $numeroTarjeta)) {
        return 'El numero de tarjeta debe tener 16 digitos.';
    }

    if (!TarjetaPasaLuhn($numeroTarjeta)) {
        return 'El numero de tarjeta no es valido.';
    }

    if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $vencimiento)) {
        return 'La fecha de vencimiento no es valida.';
    }

    $partes = explode('/', $vencimiento);
    $mesVence = intval($partes[0]);
    $anioVence = 2000 + intval($partes[1]);
    $hoy = new DateTime();
    $finMesTarjeta = new DateTime($anioVence . '-' . str_pad((string)$mesVence, 2, '0', STR_PAD_LEFT) . '-01');
    $finMesTarjeta->modify('last day of this month')->setTime(23, 59, 59);

    if ($finMesTarjeta < $hoy) {
        return 'La tarjeta ya esta vencida.';
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
            if ($idProducto > 0) {
            AgregarAlCarrito($idProducto);
            }
            break;
        case 'eliminar':
            $idProducto = intval($_POST['idProducto'] ?? 0);
            if ($idProducto > 0) {
            EliminarDelCarrito($idProducto);
            }
            break;
        case 'actualizar':
            $idProducto = intval($_POST['idProducto'] ?? 0);
            $cantidad = intval($_POST['cantidad'] ?? 1);
            if ($idProducto > 0) {
                ActualizarCantidadCarrito($idProducto, $cantidad);
            }
            break;
        case 'comprar':
            if (!isset($_SESSION['idUsuario'])) {
                $_SESSION['error_carrito'] = 'Necesitas iniciar sesion para completar la compra.';
                header("Location: /proyectoWebCS/Views/Registro/login.php");
                exit();
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

            $resultadoCompra = ComprarCarrito(intval($_SESSION['idUsuario']));
            if ($resultadoCompra['ok']) {
                VaciarCarrito();
                $idVenta = intval($resultadoCompra['idVenta']);
                $tarjetaEnmascarada = '**** **** **** ' . substr($numeroTarjeta, -4);
                $_SESSION['resumen_pago'] = [
                    'idVenta' => $idVenta,
                    'total' => floatval($resultadoCompra['total'] ?? 0),
                    'tarjeta' => $tarjetaEnmascarada,
                    'titular' => $nombreTitular,
                    'fecha' => date('Y-m-d H:i:s')
                ];
                $_SESSION['ok_carrito'] = 'Compra realizada con exito. Numero de venta #' . $idVenta . '.';
                unset($_SESSION['pago_form']);
                RegenerarTokenPago();
                $redireccion = '/proyectoWebCS/Views/Home/confirmacionPago.php';
            } else {
                $_SESSION['error_carrito'] = $resultadoCompra['mensaje'] ?? 'No se pudo procesar la compra.';
                $redireccion = $redireccionDefecto;
            }
            break;
    }

    header("Location: " . $redireccion);
    exit();
}
