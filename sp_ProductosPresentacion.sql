DELIMITER ;;

DROP PROCEDURE IF EXISTS sp_ConsultarProductosConOferta;;
CREATE PROCEDURE sp_ConsultarProductosConOferta()
BEGIN
    SELECT
        idProducto,
        idCategoria,
        nombreProducto,
        marca,
        descripcionProducto,
        precioProducto,
        stockProducto,
        estadoProducto,
        enOferta,
        precioOferta,
        imagenProducto,
        JSON_UNQUOTE(JSON_EXTRACT(imagenProducto, '$[0]')) AS primeraImagen
    FROM producto;
END;;

DROP PROCEDURE IF EXISTS sp_ConsultarProductosPorCategoriaConOferta;;
CREATE PROCEDURE sp_ConsultarProductosPorCategoriaConOferta(
    IN p_idCategoria INT
)
BEGIN
    SELECT
        idProducto,
        idCategoria,
        nombreProducto,
        marca,
        descripcionProducto,
        precioProducto,
        stockProducto,
        estadoProducto,
        enOferta,
        precioOferta,
        imagenProducto,
        JSON_UNQUOTE(JSON_EXTRACT(imagenProducto, '$[0]')) AS primeraImagen
    FROM producto
    WHERE idCategoria = p_idCategoria;
END;;

DROP PROCEDURE IF EXISTS sp_ConsultarProductosOfertaActiva;;
CREATE PROCEDURE sp_ConsultarProductosOfertaActiva()
BEGIN
    SELECT
        idProducto,
        idCategoria,
        nombreProducto,
        marca,
        descripcionProducto,
        precioProducto,
        stockProducto,
        estadoProducto,
        enOferta,
        precioOferta,
        imagenProducto,
        JSON_UNQUOTE(JSON_EXTRACT(imagenProducto, '$[0]')) AS primeraImagen
    FROM producto
    WHERE enOferta = 1 AND estadoProducto = 'disponible'
    ORDER BY precioProducto DESC;
END;;

DROP PROCEDURE IF EXISTS sp_FiltrarProductosConOferta;;
CREATE PROCEDURE sp_FiltrarProductosConOferta(
    IN p_idCategoria INT,
    IN p_precioMin DECIMAL(10,2),
    IN p_precioMax DECIMAL(10,2),
    IN p_ordenar VARCHAR(20)
)
BEGIN
    SELECT
        idProducto,
        idCategoria,
        nombreProducto,
        marca,
        descripcionProducto,
        precioProducto,
        stockProducto,
        estadoProducto,
        enOferta,
        precioOferta,
        imagenProducto,
        JSON_UNQUOTE(JSON_EXTRACT(imagenProducto, '$[0]')) AS primeraImagen
    FROM producto
    WHERE (p_idCategoria IS NULL OR p_idCategoria <= 0 OR idCategoria = p_idCategoria)
      AND (p_precioMin IS NULL OR precioProducto >= p_precioMin)
      AND (p_precioMax IS NULL OR precioProducto <= p_precioMax)
    ORDER BY
      CASE WHEN p_ordenar = 'precio_menor' THEN precioProducto END ASC,
      CASE WHEN p_ordenar = 'precio_mayor' THEN precioProducto END DESC,
      CASE WHEN p_ordenar = 'nombre' THEN nombreProducto END ASC,
      CASE WHEN p_ordenar = 'relevancia' THEN enOferta END DESC,
      CASE WHEN p_ordenar = 'relevancia' THEN stockProducto END DESC,
      CASE WHEN p_ordenar IS NULL OR p_ordenar = '' OR p_ordenar = 'disponibilidad' THEN (CASE WHEN estadoProducto = 'disponible' THEN 0 ELSE 1 END) END ASC,
      CASE WHEN p_ordenar IS NULL OR p_ordenar = '' OR p_ordenar = 'disponibilidad' THEN precioProducto END DESC;
END;;

DROP PROCEDURE IF EXISTS sp_ActualizarImagenesProducto;;
CREATE PROCEDURE sp_ActualizarImagenesProducto(
    IN p_idProducto INT,
    IN p_imagenJson JSON
)
BEGIN
    UPDATE producto
    SET imagenProducto = p_imagenJson
    WHERE idProducto = p_idProducto;

    SELECT ROW_COUNT() AS filasAfectadas;
END;;

DROP PROCEDURE IF EXISTS sp_ActualizarOfertaProducto;;
CREATE PROCEDURE sp_ActualizarOfertaProducto(
    IN p_idProducto INT,
    IN p_enOferta TINYINT,
    IN p_precioOferta DECIMAL(10,2)
)
BEGIN
    UPDATE producto
    SET enOferta = p_enOferta,
        precioOferta = p_precioOferta
    WHERE idProducto = p_idProducto;

    SELECT ROW_COUNT() AS filasAfectadas;
END;;

DELIMITER ;
