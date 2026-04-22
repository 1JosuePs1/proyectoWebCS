<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/UtilitarioModel.php";

function ObtenerPrecioEfectivoVenta($producto)
{
    $precioNormal = floatval($producto['precioProducto'] ?? 0);
    $enOferta = intval($producto['enOferta'] ?? 0) === 1;
    $precioOferta = isset($producto['precioOferta']) ? floatval($producto['precioOferta']) : 0;

    if ($enOferta && $precioOferta > 0 && $precioOferta < $precioNormal) {
        return $precioOferta;
    }

    return $precioNormal;
}

function RegistrarCompraCarritoModel($idUsuario, $items, $datosEnvio = [])
{
    $conexion = OpenDatabase();
    $consultaProducto = null;
    $consultaInsertVenta = null;
    $consultaInsertDetalle = null;
    $consultaActualizarStock = null;
    $consultaInsertPedido = null;
    $transaccionActiva = false;

    $nombreDestinatario = substr(trim($datosEnvio['nombreDestinatario'] ?? ''), 0, 100);
    $telefonoRaw = substr(trim($datosEnvio['telefonoEnvio'] ?? ''), 0, 9);
    $telefonoSoloDigitos = preg_replace('/\D+/', '', $telefonoRaw);
    if (!is_string($telefonoSoloDigitos)) {
        $telefonoSoloDigitos = '';
    }
    $telefonoEnvio = strlen($telefonoSoloDigitos) === 8
        ? substr($telefonoSoloDigitos, 0, 4) . '-' . substr($telefonoSoloDigitos, 4)
        : '';
    $direccionEnvio = substr(trim($datosEnvio['direccionEnvio'] ?? ''), 0, 350);

    if ($nombreDestinatario === '' || $telefonoEnvio === '' || $direccionEnvio === '') {
        return [
            'ok' => false,
            'mensaje' => 'Completa los datos de envio antes de pagar.'
        ];
    }

    try {
        $consultaProducto = $conexion->prepare("SELECT idProducto, nombreProducto, precioProducto, enOferta, precioOferta, stockProducto, estadoProducto FROM producto WHERE idProducto = ? FOR UPDATE");
        $consultaInsertVenta = $conexion->prepare("INSERT INTO venta (idUsuario, totalVenta, fechaVenta) VALUES (?, ?, CURDATE())");
        $consultaInsertDetalle = $conexion->prepare("INSERT INTO detalleventa (idVenta, idProducto, cantidadProductos, precioUnitarioHistorico) VALUES (?, ?, ?, ?)");
        $consultaActualizarStock = $conexion->prepare("UPDATE producto SET stockProducto = stockProducto - ?, estadoProducto = IF(stockProducto - ? <= 0, 'agotado', 'disponible') WHERE idProducto = ?");
        $consultaInsertPedido = $conexion->prepare("INSERT INTO pedido (idVenta, idUsuario, nombreDestinatario, telefonoEnvio, direccionEnvio, estadoPedido, fechaPedido) VALUES (?, ?, ?, ?, ?, 'pendiente', NOW())");

        $conexion->begin_transaction();
        $transaccionActiva = true;

        $detalleVenta = [];
        $totalVenta = 0;

        foreach ($items as $item) {
            $idProducto = intval($item['idProducto'] ?? 0);
            $cantidad = intval($item['cantidad'] ?? 0);

            if ($idProducto <= 0 || $cantidad <= 0) {
                throw new Exception("Hay productos invalidos en el carrito.");
            }

            $consultaProducto->bind_param("i", $idProducto);
            $consultaProducto->execute();
            $resultadoProducto = $consultaProducto->get_result();
            $producto = $resultadoProducto->fetch_assoc();
            $resultadoProducto->free();

            if (!$producto || $producto['estadoProducto'] !== 'disponible') {
                throw new Exception("Uno de los productos ya no esta disponible.");
            }

            $stockActual = intval($producto['stockProducto']);
            if ($stockActual < $cantidad) {
                throw new Exception("Stock insuficiente para " . $producto['nombreProducto'] . ".");
            }

            $precioUnitario = ObtenerPrecioEfectivoVenta($producto);
            $totalVenta += $precioUnitario * $cantidad;

            $detalleVenta[] = [
                'idProducto' => $idProducto,
                'cantidad' => $cantidad,
                'precio' => $precioUnitario
            ];
        }

        $consultaInsertVenta->bind_param("id", $idUsuario, $totalVenta);
        $consultaInsertVenta->execute();
        $idVenta = $conexion->insert_id;

        foreach ($detalleVenta as $detalle) {
            $idProducto = intval($detalle['idProducto']);
            $cantidad = intval($detalle['cantidad']);
            $precio = floatval($detalle['precio']);

            $consultaInsertDetalle->bind_param("iiid", $idVenta, $idProducto, $cantidad, $precio);
            $consultaInsertDetalle->execute();

            $consultaActualizarStock->bind_param("iii", $cantidad, $cantidad, $idProducto);
            $consultaActualizarStock->execute();
        }

        $consultaInsertPedido->bind_param("iisss", $idVenta, $idUsuario, $nombreDestinatario, $telefonoEnvio, $direccionEnvio);
        $consultaInsertPedido->execute();
        $idPedido = $conexion->insert_id;

        $conexion->commit();
        $transaccionActiva = false;

        return [
            'ok' => true,
            'idVenta' => $idVenta,
            'idPedido' => $idPedido,
            'total' => $totalVenta,
            'estadoPedido' => 'pendiente'
        ];
    } catch (Throwable $error) {
        if ($transaccionActiva) {
            $conexion->rollback();
        }

        return [
            'ok' => false,
            'mensaje' => $error->getMessage()
        ];
    } finally {
        if ($consultaProducto) {
            $consultaProducto->close();
        }
        if ($consultaInsertVenta) {
            $consultaInsertVenta->close();
        }
        if ($consultaInsertDetalle) {
            $consultaInsertDetalle->close();
        }
        if ($consultaActualizarStock) {
            $consultaActualizarStock->close();
        }
        if ($consultaInsertPedido) {
            $consultaInsertPedido->close();
        }
        CloseDatabase($conexion);
    }
}

function ObtenerPedidosUsuarioModel($idUsuario)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_ObtenerPedidosUsuario(?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("i", $idUsuario);
    $consultaPreparada->execute();

    $resultado = $consultaPreparada->get_result();
    $lista = [];

    while ($fila = $resultado->fetch_assoc()) {
        $lista[] = $fila;
    }

    $consultaPreparada->close();
    while ($conexion->next_result()) {
        $extra = $conexion->store_result();
        if ($extra) {
            $extra->free();
        }
    }
    CloseDatabase($conexion);

    return $lista;
}

function ObtenerPedidosAdminModel()
{
    $conexion = OpenDatabase();
    $resultado = $conexion->query("CALL sp_ObtenerPedidosAdmin()");
    $lista = [];

    while ($fila = $resultado->fetch_assoc()) {
        $lista[] = $fila;
    }

    $resultado->free();
    $conexion->next_result();
    CloseDatabase($conexion);

    return $lista;
}

function MarcarPedidoCompletadoModel($idPedido)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_MarcarPedidoCompletado(?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("i", $idPedido);
    $consultaPreparada->execute();

    $resultado = $consultaPreparada->get_result();
    $fila = $resultado->fetch_assoc();
    $consultaPreparada->close();

    while ($conexion->next_result()) {
        $extra = $conexion->store_result();
        if ($extra) {
            $extra->free();
        }
    }
    CloseDatabase($conexion);

    return intval($fila['filasAfectadas'] ?? 0) > 0;
}
