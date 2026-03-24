<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/UtilitarioModel.php");

function ObtenerCantidadTotalVentasModel()
{
    $conexion = OpenDatabase();
    $resultado = $conexion->query("CALL sp_cantidad_total_ventas()");
    $data = $resultado->fetch_assoc();

    $resultado->free();
    $conexion->next_result();
    CloseDatabase($conexion);

    return $data;
}

function ObtenerVentasPorProductoModel()
{
    $conexion = OpenDatabase();
    $resultado = $conexion->query("CALL sp_ventas_por_producto()");
    $lista = [];

    while ($fila = $resultado->fetch_assoc()) {
        $lista[] = $fila;
    }

    $resultado->free();
    $conexion->next_result();
    CloseDatabase($conexion);

    return $lista;
}

function ObtenerCategoriaMasVendeModel()
{
    $conexion = OpenDatabase();
    $resultado = $conexion->query("CALL sp_categoria_mas_vende()");
    $data = $resultado->fetch_assoc();

    $resultado->free();
    $conexion->next_result();
    CloseDatabase($conexion);

    return $data;
}

function ObtenerDetalleVentasDashboardModel()
{
    $conexion = OpenDatabase();
    $resultado = $conexion->query("CALL sp_detalle_ventas_dashboard()");
    $lista = [];

    while ($fila = $resultado->fetch_assoc()) {
        $lista[] = $fila;
    }

    $resultado->free();
    $conexion->next_result();
    CloseDatabase($conexion);

    return $lista;
}
?>