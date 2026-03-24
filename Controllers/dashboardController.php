<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/dashboardModel.php");

function ObtenerCantidadTotalVentasController()
{
    return ObtenerCantidadTotalVentasModel();
}

function ObtenerVentasPorProductoController()
{
    return ObtenerVentasPorProductoModel();
}

function ObtenerCategoriaMasVendeController()
{
    return ObtenerCategoriaMasVendeModel();
}

function ObtenerDetalleVentasDashboardController()
{
    return ObtenerDetalleVentasDashboardModel();
}
?>