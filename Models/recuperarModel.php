<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Models/UtilitarioModel.php");

function VerificarCorreoUsuarioModel($correo)
{
    $conexion = OpenDatabase();

    try {
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
    } catch (mysqli_sql_exception $e) {
        if (stripos($e->getMessage(), 'sp_VerificarCorreoUsuario') === false || stripos($e->getMessage(), 'does not exist') === false) {
            CloseDatabase($conexion);
            throw $e;
        }

        $consultaSQL = "SELECT idUsuario, nombreCompleto, emailUsuario FROM usuario WHERE emailUsuario = ? LIMIT 1";
        $consultaPreparada = $conexion->prepare($consultaSQL);
        $consultaPreparada->bind_param("s", $correo);
        $consultaPreparada->execute();

        $resultado = $consultaPreparada->get_result();
        $usuario = $resultado->fetch_assoc();

        $resultado->free();
        $consultaPreparada->close();
        CloseDatabase($conexion);

        return $usuario;
    }
}

function ActualizarPasswordUsuarioModel($correo, $passwordHash)
{
    $conexion = OpenDatabase();

    try {
        $consultaSQL = "CALL sp_ActualizarPasswordUsuario(?, ?)";
        $consultaPreparada = $conexion->prepare($consultaSQL);
        $consultaPreparada->bind_param("ss", $correo, $passwordHash);

        $resultado = $consultaPreparada->execute();

        $consultaPreparada->close();
        $conexion->next_result();
        CloseDatabase($conexion);

        return $resultado;
    } catch (mysqli_sql_exception $e) {
        if (stripos($e->getMessage(), 'sp_ActualizarPasswordUsuario') === false || stripos($e->getMessage(), 'does not exist') === false) {
            CloseDatabase($conexion);
            throw $e;
        }

        $consultaSQL = "UPDATE usuario SET passwordUsuario = ? WHERE emailUsuario = ?";
        $consultaPreparada = $conexion->prepare($consultaSQL);
        $consultaPreparada->bind_param("ss", $passwordHash, $correo);
        $consultaPreparada->execute();
        $filasAfectadas = $consultaPreparada->affected_rows;

        $consultaPreparada->close();
        CloseDatabase($conexion);

        return $filasAfectadas > 0;
    }
}
?>