-- Script para agregar campos de oferta a la tabla producto
-- Ejecutar este script en la base de datos tiendagaming

ALTER TABLE `producto` 
ADD COLUMN `enOferta` TINYINT(1) DEFAULT 0 AFTER `estadoProducto`,
ADD COLUMN `precioOferta` DECIMAL(10,2) NULL AFTER `enOferta`;

-- Crear índice para búsquedas rápidas de productos en oferta
CREATE INDEX idx_en_oferta ON `producto`(`enOferta`);
