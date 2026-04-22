DELIMITER ;;

DROP PROCEDURE IF EXISTS sp_VerificarCorreoUsuario;;
CREATE PROCEDURE sp_VerificarCorreoUsuario(
    IN p_correo VARCHAR(100)
)
BEGIN
    SELECT idUsuario, nombreCompleto, emailUsuario
    FROM usuario
    WHERE emailUsuario = p_correo
    LIMIT 1;
END;;

DROP PROCEDURE IF EXISTS sp_ActualizarPasswordUsuario;;
CREATE PROCEDURE sp_ActualizarPasswordUsuario(
    IN p_correo VARCHAR(100),
    IN p_passwordHash VARCHAR(255)
)
BEGIN
    UPDATE usuario
    SET passwordUsuario = p_passwordHash
    WHERE emailUsuario = p_correo;

    SELECT ROW_COUNT() AS filasAfectadas;
END;;

DELIMITER ;
