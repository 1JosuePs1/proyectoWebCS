<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/UtilitarioModel.php";

function registrarProductoModel($idCategoria, $nombre, $marca, $descripcion, $precio, $stock, $imagenJson)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_RegistrarProducto(?, ?, ?, ?, ?, ?, ?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("isssdis", $idCategoria, $nombre, $marca, $descripcion, $precio, $stock, $imagenJson);
    $consultaPreparada->execute();

    $resultadoConsulta = $consultaPreparada->get_result();
    $filaProducto = $resultadoConsulta->fetch_assoc();
    $idProductoInsertado = $filaProducto['idProducto'] ?? null;

    $consultaPreparada->close();
    CloseDatabase($conexion);
    return $idProductoInsertado;
}

function actualizarImagenesProductoModel($idProducto, $imagenJson)
{
    $conexion = OpenDatabase();

    $consultaSQL = "UPDATE producto SET imagenProducto = ? WHERE idProducto = ?";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("si", $imagenJson, $idProducto);
    $result = $consultaPreparada->execute();

    $consultaPreparada->close();
    CloseDatabase($conexion);
    return $result;
}

function ObtenerProductosModel()
{
    $conexion = OpenDatabase();

    $resultadoProductos = $conexion->query("CALL sp_ConsultarProductos()");
    $listaProductos = [];

    while ($producto = $resultadoProductos->fetch_assoc()) {
        $listaProductos[] = $producto;
    }

    $resultadoProductos->free();
    $conexion->next_result();
    CloseDatabase($conexion);
    return $listaProductos;
}

function ObtenerCategoriasModel()
{
    $conexion = OpenDatabase();

    $resultadoCategorias = $conexion->query("CALL sp_ConsultarCategorias()");
    $listaCategorias = [];

    while ($categoria = $resultadoCategorias->fetch_assoc()) {
        $listaCategorias[] = $categoria;
    }

    $resultadoCategorias->free();
    $conexion->next_result();
    CloseDatabase($conexion);
    return $listaCategorias;
}
