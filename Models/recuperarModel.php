<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/UtilitarioModel.php");

function VerificarCorreoUsuarioModel($correo)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_VerificarCorreoUsuario(?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("s", $correo);
    $consultaPreparada->execute();

    $resultado = $consultaPreparada->get_result();
    $usuario = $resultado->fetch_assoc();

    $resultado->free();
    $consultaPreparada->close();
    $conexion->next_result();
    CloseDatabase($conexion);

    return $usuario;
}

function ActualizarPasswordUsuarioModel($correo, $passwordHash)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_ActualizarPasswordUsuario(?, ?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("ss", $correo, $passwordHash);

    $resultado = $consultaPreparada->execute();

    $consultaPreparada->close();
    $conexion->next_result();
    CloseDatabase($conexion);

    return $resultado;
}
?>