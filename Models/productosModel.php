<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/slugify.php";
function ObtenerProductoPorNombreModel($slugNombre)
{
    $conexion = OpenDatabase();
    $consultaSQL = "CALL sp_ConsultarProductosConOferta()";
    $resultado = $conexion->query($consultaSQL);
    $producto = null;
    while ($row = $resultado->fetch_assoc()) {
        if (slugify($row['nombreProducto']) === $slugNombre) {
            $producto = $row;
            break;
        }
    }
    $resultado->free();
    $conexion->next_result();
    CloseDatabase($conexion);
    return $producto;
}
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

function editarProductoModel($idProducto, $idCategoria, $nombre, $marca, $descripcion, $precio, $stock)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_EditarProductoAdmin(?, ?, ?, ?, ?, ?, ?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    if (!$consultaPreparada) {
        CloseDatabase($conexion);
        return false;
    }

    $consultaPreparada->bind_param("iisssdi", $idProducto, $idCategoria, $nombre, $marca, $descripcion, $precio, $stock);
    $ok = $consultaPreparada->execute();

    $consultaPreparada->close();
    while ($conexion->next_result()) {
        $extra = $conexion->store_result();
        if ($extra) {
            $extra->free();
        }
    }
    CloseDatabase($conexion);

    return $ok;
}

function actualizarImagenesProductoModel($idProducto, $imagenJson)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_ActualizarImagenesProducto(?, ?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("is", $idProducto, $imagenJson);
    $result = $consultaPreparada->execute();

    $consultaPreparada->close();
    while ($conexion->next_result()) {
        $extra = $conexion->store_result();
        if ($extra) {
            $extra->free();
        }
    }
    CloseDatabase($conexion);
    return $result;
}

function ObtenerProductosModel()
{
    $conexion = OpenDatabase();

    $resultadoProductos = $conexion->query("CALL sp_ConsultarProductosConOferta()");
    $listaProductos = [];

    while ($producto = $resultadoProductos->fetch_assoc()) {
        $listaProductos[] = $producto;
    }

    $resultadoProductos->free();
    $conexion->next_result();
    CloseDatabase($conexion);

    return OrdenarProductosPorDisponibilidadModel($listaProductos);
}

function ProductoEstaAgotadoModel($producto)
{
    $stockProducto = intval($producto['stockProducto'] ?? 0);
    $estadoProducto = strtolower(trim($producto['estadoProducto'] ?? ''));
    return $stockProducto <= 0 || $estadoProducto === 'agotado';
}

function OrdenarProductosPorDisponibilidadModel($listaProductos)
{
    $disponibles = [];
    $agotados = [];

    foreach ($listaProductos as $producto) {
        if (ProductoEstaAgotadoModel($producto)) {
            $agotados[] = $producto;
        } else {
            $disponibles[] = $producto;
        }
    }

    return array_merge($disponibles, $agotados);
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


function ObtenerProductoPorIdModel($idProducto)
{
    $conexion = OpenDatabase();
    $consultaSQL = "CALL sp_ObtenerProductoPorId(?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("i", $idProducto);
    $consultaPreparada->execute();
    $resultado = $consultaPreparada->get_result();
    $producto = $resultado->fetch_assoc();
    $consultaPreparada->close();
    CloseDatabase($conexion);
    return $producto;
}

function ObtenerProductosPorCategoriaModel($idCategoria)
{
    $conexion = OpenDatabase();
    $consultaSQL = "CALL sp_ConsultarProductosPorCategoriaConOferta(?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("i", $idCategoria);
    $consultaPreparada->execute();
    $resultado = $consultaPreparada->get_result();
    $listaProductos = [];
    while ($producto = $resultado->fetch_assoc()) {
        $listaProductos[] = $producto;
    }
    $resultado->free();
    $consultaPreparada->close();
    $conexion->next_result();
    CloseDatabase($conexion);
    return OrdenarProductosPorDisponibilidadModel($listaProductos);
}

function ObtenerProductosEnOfertaModel()
{
    $conexion = OpenDatabase();
    $consultaSQL = "CALL sp_ConsultarProductosOfertaActiva()";
    $resultado = $conexion->query($consultaSQL);
    $listaProductos = [];
    
    while ($producto = $resultado->fetch_assoc()) {
        $listaProductos[] = $producto;
    }
    
    $resultado->free();
    $conexion->next_result();
    CloseDatabase($conexion);
    return $listaProductos;
}

function FiltrarProductosModel($idCategoria = null, $precioMin = null, $precioMax = null, $ordenar = 'disponibilidad')
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_FiltrarProductosConOferta(?, ?, ?, ?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $categoriaParam = ($idCategoria !== null && $idCategoria > 0) ? intval($idCategoria) : null;
    $precioMinParam = ($precioMin !== null) ? floatval($precioMin) : null;
    $precioMaxParam = ($precioMax !== null) ? floatval($precioMax) : null;
    $ordenarParam = trim((string) $ordenar);

    $consultaPreparada->bind_param("idds", $categoriaParam, $precioMinParam, $precioMaxParam, $ordenarParam);
    $consultaPreparada->execute();
    $resultado = $consultaPreparada->get_result();
    $listaProductos = [];
    
    while ($producto = $resultado->fetch_assoc()) {
        $listaProductos[] = $producto;
    }
    
    $resultado->free();
    $consultaPreparada->close();
    $conexion->next_result();
    CloseDatabase($conexion);
    return $listaProductos;
}

function ActualizarOfertaProductoModel($idProducto, $enOferta, $precioOferta = null)
{
    $conexion = OpenDatabase();
    
    $enOferta = $enOferta ? 1 : 0;
    $precioOferta = ($enOferta && $precioOferta !== null) ? floatval($precioOferta) : null;
    
    $sql = "CALL sp_ActualizarOfertaProducto(?, ?, ?)";
    $consultaPreparada = $conexion->prepare($sql);
    
    if (!$consultaPreparada) {
        CloseDatabase($conexion);
        return false;
    }
    
    $consultaPreparada->bind_param("iid", $idProducto, $enOferta, $precioOferta);
    $result = $consultaPreparada->execute();
    
    $consultaPreparada->close();
    while ($conexion->next_result()) {
        $extra = $conexion->store_result();
        if ($extra) {
            $extra->free();
        }
    }
    CloseDatabase($conexion);
    
    return $result;
}


