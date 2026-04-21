DELIMITER ;;

DROP PROCEDURE IF EXISTS sp_EditarProductoAdmin;;
CREATE PROCEDURE sp_EditarProductoAdmin(
    IN p_idProducto INT,
    IN p_idCategoria INT,
    IN p_nombreProducto VARCHAR(100),
    IN p_marca VARCHAR(100),
    IN p_descripcionProducto TEXT,
    IN p_precioProducto DECIMAL(10,2),
    IN p_stockProducto INT
)
BEGIN
    UPDATE producto
    SET
        idCategoria = p_idCategoria,
        nombreProducto = p_nombreProducto,
        marca = p_marca,
        descripcionProducto = p_descripcionProducto,
        precioProducto = p_precioProducto,
        stockProducto = p_stockProducto,
        estadoProducto = CASE
            WHEN p_stockProducto > 0 THEN 'disponible'
            ELSE 'agotado'
        END
    WHERE idProducto = p_idProducto;

    SELECT ROW_COUNT() AS filasAfectadas;
END;;

DELIMITER ;
