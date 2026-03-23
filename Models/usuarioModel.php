<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/UtilitarioModel.php";

function ObtenerUsuarioPorIdModel($idUsuario)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_ObtenerUsuarioPorId(?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("i", $idUsuario);
    $consultaPreparada->execute();

    $resultado = $consultaPreparada->get_result();
    $usuario = $resultado->fetch_assoc();

    $consultaPreparada->close();
    $conexion->next_result();
    CloseDatabase($conexion);
    return $usuario;
}

function ActualizarUsuarioModel($idUsuario, $nombreCompleto, $passwordHash)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_ActualizarUsuario(?, ?, ?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("iss", $idUsuario, $nombreCompleto, $passwordHash);
    $consultaPreparada->execute();

    $resultado = $consultaPreparada->get_result();
    $fila = $resultado->fetch_assoc();

    $consultaPreparada->close();
    $conexion->next_result();
    CloseDatabase($conexion);
    return $fila;
}
