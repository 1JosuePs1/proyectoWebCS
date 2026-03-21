<?php
require_once("../config/conexion.php");

$idCategoria = trim($_POST['idCategoria']);
$nombre = trim($_POST['nombreProducto']);
$descripcion = trim($_POST['descripcionProducto']);
$precio = trim($_POST['precioProducto']);
$stock = trim($_POST['stockProducto']);
$estado = trim($_POST['estadoProducto']);

if (empty($idCategoria) || empty($nombre) || empty($precio) || $stock === "" || empty($estado)) {
    die("Todos los campos obligatorios deben completarse");
}

if (!is_numeric($precio) || $precio <= 0) {
    die("El precio debe ser mayor que 0");
}

if (!is_numeric($stock) || $stock < 0) {
    die("El stock no puede ser negativo");
}

$nombreImagen = "";

if (isset($_FILES['imagenProducto']) && $_FILES['imagenProducto']['error'] === 0) {
    $nombreImagen = time() . "_" . basename($_FILES['imagenProducto']['name']);
    $rutaDestino = "../Views/assets/image/" . $nombreImagen;

    if (!move_uploaded_file($_FILES['imagenProducto']['tmp_name'], $rutaDestino)) {
        die("Error al subir la imagen");
    }
}

$sql = "INSERT INTO producto (idCategoria, nombreProducto, descripcionProducto, precioProducto, stockProducto, imagenProducto, estadoProducto)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("issdiss", $idCategoria, $nombre, $descripcion, $precio, $stock, $nombreImagen, $estado);

if ($stmt->execute()) {
    echo "Producto registrado correctamente";
} else {
    echo "Error al registrar producto";
}
?>