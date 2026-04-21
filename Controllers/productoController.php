
<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/slugify.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/productosModel.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/guardAdmin.php";

function ObtenerProductoDetallePorNombreController($nombreProducto)
{
    $p = ObtenerProductoPorNombreModel($nombreProducto);
    if ($p && isset($p['imagenProducto'])) {
        $imagenes = json_decode($p['imagenProducto'], true);
        $p['imagen'] = (isset($imagenes[0]) && $imagenes[0])
            ? '/proyectoWebCS/Views/assets/image/productos/' . $p['idProducto'] . '/' . $imagenes[0]
            : '/proyectoWebCS/Views/assets/image/imgLogo/logo.png';
    } else {
        $p['imagen'] = '/proyectoWebCS/Views/assets/image/imgLogo/logo.png';
    }
    return $p;
}

function ObtenerProductoDetalleController($idProducto)
{
    $p = ObtenerProductoPorIdModel($idProducto);
    if ($p && isset($p['imagenProducto'])) {
        $imagenes = json_decode($p['imagenProducto'], true);
        $p['imagen'] = (isset($imagenes[0]) && $imagenes[0])
            ? '/proyectoWebCS/Views/assets/image/productos/' . $p['idProducto'] . '/' . $imagenes[0]
            : '/proyectoWebCS/Views/assets/image/imgLogo/logo.png';
    } else {
        $p['imagen'] = '/proyectoWebCS/Views/assets/image/imgLogo/logo.png';
    }
    return $p;
}


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    RequerirAdminOculto();

    $accion = trim($_POST['accion'] ?? 'registrar');

    if ($accion === 'editar') {
        $idProducto = intval($_POST['idProducto'] ?? 0);
        $idCategoria = trim($_POST['idCategoria'] ?? '');
        $nombre = trim($_POST['nombreProducto'] ?? '');
        $marca = trim($_POST['marcaProducto'] ?? '');
        $descripcion = trim($_POST['descripcionProducto'] ?? '');
        $precio = trim($_POST['precioProducto'] ?? '');
        $stock = trim($_POST['stockProducto'] ?? '');

        if ($idProducto <= 0 || empty($idCategoria) || empty($nombre) || empty($marca) || empty($precio) || $stock === "") {
            $_SESSION['error_producto'] = "Completa los campos obligatorios para editar.";
            header("Location: /proyectoWebCS/Views/Admin/editarProducto.php?id=" . $idProducto);
            exit();
        }

        if (!is_numeric($precio) || floatval($precio) <= 0) {
            $_SESSION['error_producto'] = "El precio debe ser mayor que 0.";
            header("Location: /proyectoWebCS/Views/Admin/editarProducto.php?id=" . $idProducto);
            exit();
        }

        if (!is_numeric($stock) || intval($stock) < 0) {
            $_SESSION['error_producto'] = "El stock no puede ser negativo.";
            header("Location: /proyectoWebCS/Views/Admin/editarProducto.php?id=" . $idProducto);
            exit();
        }

        $okEdicion = editarProductoModel(
            $idProducto,
            intval($idCategoria),
            $nombre,
            $marca,
            $descripcion,
            floatval($precio),
            intval($stock)
        );

        if (!$okEdicion) {
            $_SESSION['error_producto'] = "No se pudo actualizar el producto. Verifica que exista el SP sp_EditarProductoAdmin.";
            header("Location: /proyectoWebCS/Views/Admin/editarProducto.php?id=" . $idProducto);
            exit();
        }

        $_SESSION['ok_producto'] = "Producto actualizado correctamente.";
        header("Location: /proyectoWebCS/Views/Admin/listaProductos.php");
        exit();
    }

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

    $_SESSION['ok_producto'] = "Producto registrado correctamente.";
    header("Location: ../Views/Admin/listaProductos.php");
    exit();
}


function ObtenerProductoPorIdController($idProducto)
{
    return ObtenerProductoPorIdModel($idProducto);
}



$p = null;
if (isset($_GET['nombre'])) {
    $nombreProducto = $_GET['nombre'];
    $p = ObtenerProductoDetallePorNombreController($nombreProducto);
} elseif (isset($_GET['id'])) {
    $idProducto = intval($_GET['id']);
    $p = ObtenerProductoDetalleController($idProducto);
}


?>


