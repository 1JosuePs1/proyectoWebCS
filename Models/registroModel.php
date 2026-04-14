<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/UtilitarioModel.php";

function ValidarAccesoModel($correo)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_ValidarAcceso(?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("s", $correo);
    $consultaPreparada->execute();

    $resultado = $consultaPreparada->get_result();
    $usuario = $resultado->fetch_assoc();

    $consultaPreparada->close();
    $conexion->next_result();
    CloseDatabase($conexion);
    return $usuario;
}

function VerificarCorreoExistenteModel($correo)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_VerificarCorreoExistente(?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("s", $correo);
    $consultaPreparada->execute();

    $resultado = $consultaPreparada->get_result();
    $fila = $resultado->fetch_assoc();

    $consultaPreparada->close();
    
    
    $existe = ($fila && $fila['total'] > 0);

    
    while($conexion->next_result()) $conexion->store_result();
    
    CloseDatabase($conexion);

    return $existe;
}

function RegistrarUsuarioModel($nombre, $correo, $passwordHash)
{
    $conexion = OpenDatabase();

    $consultaSQL = "CALL sp_RegistrarUsuario(?, ?, ?)";
    $consultaPreparada = $conexion->prepare($consultaSQL);
    $consultaPreparada->bind_param("sss", $nombre, $correo, $passwordHash);

    $resultado = $consultaPreparada->execute();

    $consultaPreparada->close();
    $conexion->next_result();
    CloseDatabase($conexion);

    return $resultado;
}
?>
