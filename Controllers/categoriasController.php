<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/productosModel.php";

function ObtenerCategoriasController() {
    return ObtenerCategoriasModel();
}

function ObtenerProductosController() {
    return ObtenerProductosModel();
}

function ObtenerProductosPorCategoriaController($idCategoria) {
    return ObtenerProductosPorCategoriaModel($idCategoria);
}
?>
