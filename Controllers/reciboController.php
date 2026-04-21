<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/verificarSesion.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/ventaModel.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/usuarioModel.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/correoController.php";

function RedirigirPedidosConMensajeRecibo($tipo, $mensaje)
{
    $_SESSION[$tipo] = $mensaje;
    header('Location: /proyectoWebCS/Views/usuario/pedidos.php');
    exit();
}

function EscaparRecibo($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function ObtenerLineasPedidoUsuario($idUsuario, $idPedido)
{
    $pedidos = ObtenerPedidosUsuarioModel($idUsuario);
    $lineas = [];

    foreach ($pedidos as $fila) {
        if (intval($fila['idPedido'] ?? 0) === $idPedido) {
            $lineas[] = $fila;
        }
    }

    return $lineas;
}

function ConstruirHtmlRecibo($lineasPedido, $usuario)
{
    $primeraLinea = $lineasPedido[0];
    $idPedido = intval($primeraLinea['idPedido'] ?? 0);
    $idVenta = intval($primeraLinea['idVenta'] ?? 0);
    $fechaPedido = EscaparRecibo($primeraLinea['fechaPedido'] ?? '');
    $estadoPedido = EscaparRecibo(ucfirst($primeraLinea['estadoPedido'] ?? 'pendiente'));
    $nombreDestinatario = EscaparRecibo($primeraLinea['nombreDestinatario'] ?? '');
    $telefonoEnvio = EscaparRecibo($primeraLinea['telefonoEnvio'] ?? '');
    $direccionEnvio = EscaparRecibo($primeraLinea['direccionEnvio'] ?? '');
    $nombreUsuario = EscaparRecibo($usuario['nombreCompleto'] ?? 'Usuario');
    $correoUsuario = EscaparRecibo($usuario['emailUsuario'] ?? '');

    $filasTabla = '';
    $totalCalculado = 0;

    foreach ($lineasPedido as $linea) {
        $cantidad = intval($linea['cantidadProductos'] ?? 0);
        $precioUnitario = floatval($linea['precioUnitarioHistorico'] ?? 0);
        $subtotal = $cantidad * $precioUnitario;
        $totalCalculado += $subtotal;

        $filasTabla .= '<tr>'
            . '<td>' . EscaparRecibo($linea['nombreProducto'] ?? '') . '</td>'
            . '<td style="text-align:center;">' . $cantidad . '</td>'
            . '<td style="text-align:right;">₡' . number_format($precioUnitario, 0, ',', '.') . '</td>'
            . '<td style="text-align:right;">₡' . number_format($subtotal, 0, ',', '.') . '</td>'
            . '</tr>';
    }

    $totalPedido = floatval($primeraLinea['totalVenta'] ?? 0);
    if ($totalPedido <= 0) {
        $totalPedido = $totalCalculado;
    }

    return '<!DOCTYPE html>'
        . '<html lang="es"><head><meta charset="UTF-8"><title>Recibo Pedido #' . $idPedido . '</title>'
        . '<style>'
        . 'body{font-family:Arial,sans-serif;margin:24px;color:#1f2937;}'
        . '.box{border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:16px;}'
        . '.title{color:#e42327;margin:0 0 8px 0;}'
        . 'table{width:100%;border-collapse:collapse;margin-top:12px;}'
        . 'th,td{border:1px solid #e5e7eb;padding:8px;font-size:14px;}'
        . 'th{background:#f9fafb;text-align:left;}'
        . '.total{font-size:18px;font-weight:bold;color:#e42327;text-align:right;margin-top:12px;}'
        . '</style></head><body>'
        . '<h2 class="title">My Pc Gaming - Recibo de compra</h2>'
        . '<div class="box">'
        . '<p><strong>Pedido:</strong> #' . $idPedido . '</p>'
        . '<p><strong>Venta:</strong> #' . $idVenta . '</p>'
        . '<p><strong>Fecha:</strong> ' . $fechaPedido . '</p>'
        . '<p><strong>Estado:</strong> ' . $estadoPedido . '</p>'
        . '</div>'
        . '<div class="box">'
        . '<p><strong>Cliente:</strong> ' . $nombreUsuario . '</p>'
        . '<p><strong>Correo:</strong> ' . $correoUsuario . '</p>'
        . '<p><strong>Destinatario:</strong> ' . $nombreDestinatario . '</p>'
        . '<p><strong>Telefono:</strong> ' . $telefonoEnvio . '</p>'
        . '<p><strong>Direccion:</strong> ' . $direccionEnvio . '</p>'
        . '</div>'
        . '<table><thead><tr><th>Producto</th><th style="text-align:center;">Cantidad</th><th style="text-align:right;">Precio Unitario</th><th style="text-align:right;">Subtotal</th></tr></thead><tbody>'
        . $filasTabla
        . '</tbody></table>'
        . '<p class="total">Total pagado: ₡' . number_format($totalPedido, 0, ',', '.') . '</p>'
        . '</body></html>';
}

$idUsuario = intval($_SESSION['idUsuario'] ?? 0);
$idPedido = intval($_GET['idPedido'] ?? 0);
$accion = trim($_GET['accion'] ?? 'descargar');

if ($idUsuario <= 0 || $idPedido <= 0) {
    RedirigirPedidosConMensajeRecibo('error_recibo', 'Solicitud de recibo invalida.');
}

$lineasPedido = ObtenerLineasPedidoUsuario($idUsuario, $idPedido);
if (empty($lineasPedido)) {
    RedirigirPedidosConMensajeRecibo('error_recibo', 'No se encontro ese pedido para tu cuenta.');
}

$usuario = ObtenerUsuarioPorIdModel($idUsuario);
if (!$usuario) {
    RedirigirPedidosConMensajeRecibo('error_recibo', 'No se pudo cargar la informacion de usuario.');
}

$htmlRecibo = ConstruirHtmlRecibo($lineasPedido, $usuario);

if ($accion === 'enviarCorreo') {
    $correoDestino = trim((string) ($usuario['emailUsuario'] ?? ''));
    if ($correoDestino === '') {
        RedirigirPedidosConMensajeRecibo('error_recibo', 'Tu cuenta no tiene un correo disponible.');
    }

    $asunto = 'Recibo de pedido #' . $idPedido . ' - My Pc Gaming';
    $enviado = EnviarCorreo($asunto, $htmlRecibo, $correoDestino);

    if ($enviado) {
        RedirigirPedidosConMensajeRecibo('ok_recibo', 'Recibo enviado a tu correo.');
    }

    RedirigirPedidosConMensajeRecibo('error_recibo', 'No se pudo enviar el recibo por correo.');
}

$nombreArchivo = 'recibo-pedido-' . $idPedido . '.html';
header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Cache-Control: no-store, no-cache, must-revalidate');

echo $htmlRecibo;
exit();
