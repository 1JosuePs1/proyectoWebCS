<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    if (!isset($_SESSION['idUsuario'])) {
        header('Location: /proyectoWebCS/Views/Registro/login.php');
        exit();
    }

    if (intval($_SESSION['idRol'] ?? 0) !== 1) {
        $_SESSION['error_pedidos'] = 'No tienes permisos para esta accion.';
        header('Location: /proyectoWebCS/Views/Home/Home.php');
        exit();
    }

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
