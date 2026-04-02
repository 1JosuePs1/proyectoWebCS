<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/productosModel.php";

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

// Procesar peticiones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $idProducto = intval($_POST['idProducto'] ?? 0);

    if ($idProducto <= 0) {
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/proyectoWebCS/Views/Home/Home.php'));
        exit();
    }

    switch ($accion) {
        case 'agregar':
            AgregarAlCarrito($idProducto);
            break;
        case 'eliminar':
            EliminarDelCarrito($idProducto);
            break;
        case 'actualizar':
            $cantidad = intval($_POST['cantidad'] ?? 1);
            ActualizarCantidadCarrito($idProducto, $cantidad);
            break;
    }

    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/proyectoWebCS/Views/Home/Home.php'));
    exit();
}
