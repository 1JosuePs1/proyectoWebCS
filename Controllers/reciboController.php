<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/verificarSesion.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/ventaModel.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/usuarioModel.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/correoController.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/correoTemplates.php";

function RedirigirPedidosConMensajeRecibo($tipo, $mensaje)
{
    $_SESSION[$tipo] = $mensaje;
    header('Location: /proyectoWebCS/Views/usuario/pedidos.php');
    exit();
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

$htmlRecibo = ConstruirPlantillaRecibo($lineasPedido, $usuario);

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
