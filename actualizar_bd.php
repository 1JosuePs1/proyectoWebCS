<?php
// Script para actualizar la base de datos con campos de oferta
// Ejecutar desde el navegador o línea de comandos: php actualizar_bd.php

// Conexión directa a MySQL
$host = "127.0.0.1";
$usuario = "root";
$contrasena = "123456";
$baseDatos = "tiendaGaming";
$puerto = 3306;

$conexion = new mysqli($host, $usuario, $contrasena, $baseDatos, $puerto);

if ($conexion->connect_error) {
    die("✗ Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");

try {
    // Verificar si la columna enOferta ya existe
    $resultado = $conexion->query("SHOW COLUMNS FROM `producto` LIKE 'enOferta'");
    
    if ($resultado->num_rows === 0) {
        // La columna no existe, agregarla
        $sql1 = "ALTER TABLE `producto` 
                 ADD COLUMN `enOferta` TINYINT(1) DEFAULT 0 AFTER `estadoProducto`";
        
        if ($conexion->query($sql1)) {
            echo "✓ Columna 'enOferta' agregada correctamente.\n";
        } else {
            echo "✗ Error al agregar 'enOferta': " . $conexion->error . "\n";
        }
    } else {
        echo "✓ La columna 'enOferta' ya existe.\n";
    }
    
    // Verificar si la columna precioOferta ya existe
    $resultado = $conexion->query("SHOW COLUMNS FROM `producto` LIKE 'precioOferta'");
    
    if ($resultado->num_rows === 0) {
        // La columna no existe, agregarla
        $sql2 = "ALTER TABLE `producto` 
                 ADD COLUMN `precioOferta` DECIMAL(10,2) NULL AFTER `enOferta`";
        
        if ($conexion->query($sql2)) {
            echo "✓ Columna 'precioOferta' agregada correctamente.\n";
        } else {
            echo "✗ Error al agregar 'precioOferta': " . $conexion->error . "\n";
        }
    } else {
        echo "✓ La columna 'precioOferta' ya existe.\n";
    }
    
    // Crear índice para búsquedas rápidas
    $sql3 = "CREATE INDEX idx_en_oferta ON `producto`(`enOferta`)";
    
    if ($conexion->query($sql3)) {
        echo "✓ Índice creado correctamente.\n";
    } else {
        if (strpos($conexion->error, "already exists") === false) {
            echo "✗ Error al crear índice: " . $conexion->error . "\n";
        } else {
            echo "✓ El índice ya existe.\n";
        }
    }
    
    $conexion->close();
    echo "\n✓ Base de datos actualizada correctamente.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
