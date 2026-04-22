<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/slugify.php";
function ObtenerProductoPorNombreModel($slugNombre)
{
    $conexion = OpenDatabase();
    $consultaSQL = "SELECT * FROM producto";
    $resultado = $conexion->query($consultaSQL);
    $producto = null;
    while ($row = $resultado->fetch_assoc()) {
        if (slugify($row['nombreProducto']) === $slugNombre) {
            $producto = $row;
            break;
        }
    }
    $resultado->free();
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
    $consultaSQL = "CALL sp_ConsultarProductosPorCategoria(?)";
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
    CloseDatabase($conexion);
    return OrdenarProductosPorDisponibilidadModel($listaProductos);
}

function ObtenerProductosEnOfertaModel()
{
    $conexion = OpenDatabase();
    $consultaSQL = "SELECT 
        idProducto,
        idCategoria,
        nombreProducto,
        marca,
        descripcionProducto,
        precioProducto,
        stockProducto,
        estadoProducto,
        enOferta,
        precioOferta,
        imagenProducto,
        JSON_UNQUOTE(JSON_EXTRACT(imagenProducto, '\$[0]')) AS primeraImagen
    FROM producto WHERE enOferta = 1 AND estadoProducto = 'disponible' ORDER BY precioProducto DESC";
    $resultado = $conexion->query($consultaSQL);
    $listaProductos = [];
    
    while ($producto = $resultado->fetch_assoc()) {
        $listaProductos[] = $producto;
    }
    
    $resultado->free();
    CloseDatabase($conexion);
    return $listaProductos;
}

function FiltrarProductosModel($idCategoria = null, $precioMin = null, $precioMax = null, $ordenar = 'disponibilidad')
{
    $conexion = OpenDatabase();
    
    $sql = "SELECT 
        idProducto,
        idCategoria,
        nombreProducto,
        marca,
        descripcionProducto,
        precioProducto,
        stockProducto,
        estadoProducto,
        enOferta,
        precioOferta,
        imagenProducto,
        JSON_UNQUOTE(JSON_EXTRACT(imagenProducto, '\$[0]')) AS primeraImagen
    FROM producto WHERE 1=1";
    
    if ($idCategoria !== null && $idCategoria > 0) {
        $idCategoria = intval($idCategoria);
        $sql .= " AND idCategoria = $idCategoria";
    }
    
    if ($precioMin !== null) {
        $precioMin = floatval($precioMin);
        $sql .= " AND precioProducto >= $precioMin";
    }
    
    if ($precioMax !== null) {
        $precioMax = floatval($precioMax);
        $sql .= " AND precioProducto <= $precioMax";
    }
    
    // Ordenar según parámetro
    switch ($ordenar) {
        case 'precio_menor':
            $sql .= " ORDER BY precioProducto ASC";
            break;
        case 'precio_mayor':
            $sql .= " ORDER BY precioProducto DESC";
            break;
        case 'nombre':
            $sql .= " ORDER BY nombreProducto ASC";
            break;
        case 'relevancia':
            $sql .= " ORDER BY enOferta DESC, stockProducto DESC";
            break;
        case 'disponibilidad':
        default:
            $sql .= " ORDER BY CASE WHEN estadoProducto = 'disponible' THEN 0 ELSE 1 END, precioProducto DESC";
            break;
    }
    
    $resultado = $conexion->query($sql);
    $listaProductos = [];
    
    while ($producto = $resultado->fetch_assoc()) {
        $listaProductos[] = $producto;
    }
    
    $resultado->free();
    CloseDatabase($conexion);
    return $listaProductos;
}

function ActualizarOfertaProductoModel($idProducto, $enOferta, $precioOferta = null)
{
    $conexion = OpenDatabase();
    
    $enOferta = $enOferta ? 1 : 0;
    $precioOferta = ($enOferta && $precioOferta !== null) ? floatval($precioOferta) : null;
    
    $sql = "UPDATE producto SET enOferta = ?, precioOferta = ? WHERE idProducto = ?";
    $consultaPreparada = $conexion->prepare($sql);
    
    if (!$consultaPreparada) {
        CloseDatabase($conexion);
        return false;
    }
    
    $consultaPreparada->bind_param("idi", $enOferta, $precioOferta, $idProducto);
    $result = $consultaPreparada->execute();
    
    $consultaPreparada->close();
    CloseDatabase($conexion);
    
    return $result;
}


