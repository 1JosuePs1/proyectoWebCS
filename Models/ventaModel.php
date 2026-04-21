<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/UtilitarioModel.php";

function RegistrarCompraCarritoModel($idUsuario, $items)
{
    $conexion = OpenDatabase();
    $consultaProducto = null;
    $consultaInsertVenta = null;
    $consultaInsertDetalle = null;
    $consultaActualizarStock = null;
    $transaccionActiva = false;

    try {
        $consultaProducto = $conexion->prepare("SELECT idProducto, nombreProducto, precioProducto, stockProducto, estadoProducto FROM producto WHERE idProducto = ? FOR UPDATE");
        $consultaInsertVenta = $conexion->prepare("INSERT INTO venta (idUsuario, totalVenta, fechaVenta) VALUES (?, ?, CURDATE())");
        $consultaInsertDetalle = $conexion->prepare("INSERT INTO detalleventa (idVenta, idProducto, cantidadProductos, precioUnitarioHistorico) VALUES (?, ?, ?, ?)");
        $consultaActualizarStock = $conexion->prepare("UPDATE producto SET stockProducto = stockProducto - ?, estadoProducto = IF(stockProducto - ? <= 0, 'agotado', 'disponible') WHERE idProducto = ?");

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

            $precioUnitario = floatval($producto['precioProducto']);
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

        $conexion->commit();
        $transaccionActiva = false;

        return [
            'ok' => true,
            'idVenta' => $idVenta,
            'total' => $totalVenta
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
        CloseDatabase($conexion);
    }
}
