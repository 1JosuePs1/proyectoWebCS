<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/guardAdmin.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/ventaModel.php";

function ObtenerPedidosUsuarioController($idUsuario)
{
    return ObtenerPedidosUsuarioModel($idUsuario);
}

function ObtenerPedidosAdminController()
{
    return ObtenerPedidosAdminModel();
}

function MarcarPedidoCompletadoController($idPedido)
{
    return MarcarPedidoCompletadoModel($idPedido);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $redireccion = '/proyectoWebCS/Views/Admin/pedidos.php';

    RequerirAdminOculto();

    switch ($accion) {
        case 'marcarCompletado':
            $idPedido = intval($_POST['idPedido'] ?? 0);

            if ($idPedido <= 0) {
                $_SESSION['error_pedidos'] = 'Pedido invalido.';
                break;
            }

            $ok = MarcarPedidoCompletadoController($idPedido);
            if ($ok) {
                $_SESSION['ok_pedidos'] = 'Pedido marcado como completado.';
            } else {
                $_SESSION['error_pedidos'] = 'No se pudo actualizar el pedido.';
            }
            break;
    }

    header('Location: ' . $redireccion);
    exit();
}
