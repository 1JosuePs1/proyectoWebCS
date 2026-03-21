<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/productosModel.php";

function ObtenerProductoDetalleController($idProducto)
{
    $p = ObtenerProductoPorIdModel($idProducto);
    if ($p && isset($p['imagenProducto'])) {
        $imagenes = json_decode($p['imagenProducto'], true);
        $p['imagen'] = isset($imagenes[0]) ? '../assets/image/productos/' . $p['idProducto'] . '/' . $imagenes[0] : '../assets/image/imgLogo/logo.png';
    } else {
        $p['imagen'] = '../assets/image/imgLogo/logo.png';
    }
    return $p;
}


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idCategoria = trim($_POST['idCategoria']);
    $nombre = trim($_POST['nombreProducto']);
    $marca = trim($_POST['marcaProducto']);
    $descripcion = trim($_POST['descripcionProducto']);
    $precio = trim($_POST['precioProducto']);
    $stock = trim($_POST['stockProducto']);

    if (empty($idCategoria) || empty($nombre) || empty($marca) || empty($precio) || $stock === "") {
        die("Todos los campos obligatorios deben completarse");
    }

    if (!is_numeric($precio) || $precio <= 0) {
        die("El precio debe ser mayor que 0");
    }

    if (!is_numeric($stock) || $stock < 0) {
        die("El stock no puede ser negativo");
    }

    // 1. Insertar producto con JSON vacío para obtener el ID
    $idProducto = registrarProductoModel($idCategoria, $nombre, $marca, $descripcion, $precio, $stock, '[]');

    if (!$idProducto) {
        die("Error al registrar el producto en la base de datos");
    }

    // 2. Crear carpeta para las imágenes del producto
    $carpeta = $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Views/assets/image/productos/" . $idProducto;
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    // 3. Subir imágenes y guardar nombres
    $imagenesGuardadas = [];
    if (isset($_FILES['imagenes']) && count($_FILES['imagenes']['name']) > 0) {
        for ($i = 0; $i < count($_FILES['imagenes']['name']); $i++) {
            if ($_FILES['imagenes']['error'][$i] === 0) {
                $extension = pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION);
                $nombreImagen = ($i + 1) . "." . strtolower($extension);
                $rutaDestino = $carpeta . "/" . $nombreImagen;

                if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $rutaDestino)) {
                    $imagenesGuardadas[] = $nombreImagen;
                }
            }
        }
    }

    // 4. Actualizar el producto con el JSON de imágenes
    $jsonImagenes = json_encode($imagenesGuardadas);
    actualizarImagenesProductoModel($idProducto, $jsonImagenes);

    header("Location: ../Views/Admin/dashboard.php");
    exit();
}


function ObtenerProductoPorIdController($idProducto)
{
    return ObtenerProductoPorIdModel($idProducto);
}
?>
