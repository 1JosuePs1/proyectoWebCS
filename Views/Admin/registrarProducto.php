<?php
session_start();
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/categoriasController.php";

// Sin validación de sesión (sesión cerrada)
$categorias = ObtenerCategoriasController();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Producto</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body id="inicio" class="bg-light">

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 col-lg-6 mx-auto">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header backgroundPrincipal text-white text-center py-4">
                    <h3 class="mb-0">Registrar Producto</h3>
                </div>

                <div class="card-body p-4">
                    <form action="../../Controllers/productoController.php" method="POST" enctype="multipart/form-data">

                        

                        <div class="mb-3">
                            <label class="form-label">Nombre del producto</label>
                            <input type="text" name="nombreProducto" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marcaProducto" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcionProducto" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" step="0.01" name="precioProducto" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stockProducto" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Imágenes del producto</label>
                            <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple required>
                            <small class="text-muted">Puedes seleccionar varias imágenes (se mostrará un slider)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select name="estadoProducto" class="form-control" required>
                                <option value="disponible">Disponible</option>
                                <option value="agotado">Agotado</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="idCategoria" class="form-control" required>
                                <option value="">Seleccione una categoría</option>
                                <?php
                                if (is_array($categorias) && count($categorias) > 0) {
                                    foreach ($categorias as $categoria) {
                                        echo "<option value='" . htmlspecialchars($categoria['idCategoria']) . "'>" . htmlspecialchars($categoria['nombreCategoria']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn backgroundPrincipal btn-lg text-white">Guardar producto</button>
                        </div>
                    </form>
                </div>

                <div class="text-center py-3">
                    <a href="dashboard.php" class="text-primary">Volver al panel</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>