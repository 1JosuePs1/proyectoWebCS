<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/config/verificarSesion.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/categoriasController.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Controllers/productoController.php";

// Obtener parámetros de filtros
$idCategoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
$precioMin = isset($_GET['precio_min']) ? floatval($_GET['precio_min']) : null;
$precioMax = isset($_GET['precio_max']) ? floatval($_GET['precio_max']) : null;
$ordenar = isset($_GET['ordenar']) ? trim($_GET['ordenar']) : 'disponibilidad';
$mostrarOfertas = isset($_GET['ofertas']) && $_GET['ofertas'] == '1' ? true : false;
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';

$categorias = ObtenerCategoriasController();

// Obtener productos filtrados
if ($mostrarOfertas) {
    $listaProductos = ObtenerProductosEnOfertaController();
} else {
    $listaProductos = FiltrarProductosController($idCategoria, $precioMin, $precioMax, $ordenar);
}

if ($busqueda !== '') {
    $aMinusculas = function ($texto) {
        return function_exists('mb_strtolower')
            ? mb_strtolower($texto, 'UTF-8')
            : strtolower($texto);
    };

    $termino = $aMinusculas($busqueda);
    $listaProductos = array_values(array_filter($listaProductos, function ($p) use ($termino) {
        $aMinusculasLocal = function ($texto) {
            return function_exists('mb_strtolower')
                ? mb_strtolower($texto, 'UTF-8')
                : strtolower($texto);
        };

        $nombre = $aMinusculasLocal($p['nombreProducto'] ?? '');
        $marca = $aMinusculasLocal($p['marca'] ?? '');
        $descripcion = $aMinusculasLocal($p['descripcionProducto'] ?? '');
        return str_contains($nombre, $termino)
            || str_contains($marca, $termino)
            || str_contains($descripcion, $termino);
    }));
}

// Obtener nombre de categoría actual
$nombreCategoriaActual = null;
if ($idCategoria) {
    foreach ($categorias as $cat) {
        if ($cat['idCategoria'] == $idCategoria) {
            $nombreCategoriaActual = $cat['nombreCategoria'];
            break;
        }
    }
}

// Calcular rango de precios disponibles
$preciosProductos = array_map(function($p) { return floatval($p['precioProducto']); }, $listaProductos);
$precioMinDisponible = !empty($preciosProductos) ? min($preciosProductos) : 0;
$precioMaxDisponible = !empty($preciosProductos) ? max($preciosProductos) : 0;

$iconosPorCategoria = [
    'teclados'            => 'bi-keyboard',
    'mouses'              => 'bi-mouse',
    'monitores'           => 'bi-display',
    'audifonos'           => 'bi-headphones',
    'componentes'         => 'bi-cpu',
    'computadoras'        => 'bi-laptop',
    'impresoras'          => 'bi-printer',
    'gaming streaming'    => 'bi-joystick',
    'conectividad redes'  => 'bi-wifi',
    'periféricos'         => 'bi-pc-display-horizontal',
    'audio'               => 'bi-speaker',
    'energía'             => 'bi-lightning-charge',
    'almacenamiento'      => 'bi-hdd-rack',
    'seguridad'           => 'bi-shield',
    'accesorios'          => 'bi-tools',
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Catálogo de Productos | My Pc Gaming</title>
    <meta name="description" content="Explora nuestro catálogo completo de componentes PC, periféricos y laptops gaming con filtros avanzados.">

    <link rel="icon" type="image/png" href="../assets/image/imgLogo/logo.png">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/home.css">
    
    <style>
        .filtros-sidebar {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .filtro-titulo {
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items-center;
            gap: 8px;
        }

        .filtro-titulo i {
            color: #e53935;
        }

        .filtro-opcion {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .filtro-opcion:last-child {
            border-bottom: none;
        }

        .filtro-opcion label {
            margin: 0;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .filtro-opcion .form-check-input {
            accent-color: #e53935;
            border-color: #e53935;
        }

        .filtro-opcion .form-check-input:checked {
            background-color: #e53935;
            border-color: #e53935;
        }

        .filtro-opcion .form-check-input:focus {
            border-color: #e53935;
            box-shadow: 0 0 0 0.2rem rgba(229, 57, 53, 0.25);
        }

        .rango-precio {
            margin: 15px 0;
        }

        .rango-precio input {
            width: 100%;
            margin: 5px 0;
        }

        .badge-filtro {
            display: inline-block;
            margin: 5px 5px 5px 0;
        }

        .titulo-catalogo {
            border-bottom: 3px solid #e53935;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .sin-resultados {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .sin-resultados i.bi-inbox {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .sin-resultados .btn-color {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            white-space: nowrap;
        }

        .sin-resultados .btn-color i {
            font-size: 1.2rem;
        }

        .controles-catalogo {
            display: flex;
            justify-content: space-between;
            align-items-center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .controles-catalogo .form-select {
            border-color: #ddd !important;
            color: #333;
            background-color: #fff;
        }

        .controles-catalogo .form-select:focus {
            border-color: #e53935 !important;
            box-shadow: 0 0 0 0.2rem rgba(229, 57, 53, 0.25) !important;
        }
        @media (max-width: 768px) {
            .filtros-sidebar {
                position: relative;
                top: auto;
                margin-bottom: 30px;
            }

            .controles-catalogo {
                flex-direction: column;
                align-items: stretch;
            }

            .controles-catalogo select {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar componente-->
    <?php 
    require_once $rutaNavbar;
    ?>

    <div class="container my-5">
        <div class="titulo-catalogo">
            <?php if ($mostrarOfertas): ?>
                <h2><i class="bi bi-tag-fill text-danger me-2"></i>Productos en Oferta</h2>
            <?php elseif ($nombreCategoriaActual): ?>
                <h2><i class="bi bi-grid me-2"></i><?= htmlspecialchars($nombreCategoriaActual) ?></h2>
            <?php else: ?>
                <h2><i class="bi bi-grid me-2"></i>Todos los Productos</h2>
            <?php endif; ?>
        </div>

        <div class="row">
            <!-- Sidebar de Filtros -->
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="filtros-sidebar">
                    <h5 class="mb-4">
                        <i class="bi bi-funnel" style="color: #e53935;"></i> Filtros
                    </h5>

                    <!-- Filtro de Categorías -->
                    <div class="mb-4">
                        <div class="filtro-titulo">
                            <i class="bi bi-tag"></i> Categoría
                        </div>
                        <div>
                            <div class="filtro-opcion">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input filtro-categoria" value="" 
                                        <?= !$idCategoria ? 'checked' : '' ?> onchange="aplicarFiltros()">
                                    Todas
                                </label>
                            </div>
                            <?php foreach ($categorias as $cat): ?>
                                <div class="filtro-opcion">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input filtro-categoria" 
                                            value="<?= intval($cat['idCategoria']) ?>"
                                            <?= $idCategoria == $cat['idCategoria'] ? 'checked' : '' ?>
                                            onchange="aplicarFiltros()">
                                        <?= htmlspecialchars($cat['nombreCategoria']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- Filtro de Precio -->
                    <div class="mb-4">
                        <div class="filtro-titulo">
                            <i class="bi bi-currency-dollar"></i> Rango de Precio
                        </div>
                        <div class="rango-precio">
                            <div class="mb-2">
                                <label class="form-label small mb-1">Precio mínimo (₡)</label>
                                <input type="number" class="form-control form-control-sm" id="precioMin" 
                                    value="<?= $precioMin ?? $precioMinDisponible ?>" 
                                    placeholder="0" onchange="aplicarFiltros()">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small mb-1">Precio máximo (₡)</label>
                                <input type="number" class="form-control form-control-sm" id="precioMax" 
                                    value="<?= $precioMax ?? $precioMaxDisponible ?>" 
                                    placeholder="999999" onchange="aplicarFiltros()">
                            </div>
                            <small class="text-muted d-block mt-2">
                                Disponible: ₡<?= number_format($precioMinDisponible, 0, ',', '.') ?> - ₡<?= number_format($precioMaxDisponible, 0, ',', '.') ?>
                            </small>
                        </div>
                    </div>

                    <hr>

                    <!-- Filtro de Ofertas -->
                    <div class="mb-4">
                        <div class="filtro-titulo">
                            <i class="bi bi-tag-fill text-danger"></i> Ofertas
                        </div>
                        <div class="filtro-opcion">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="soloOfertas" 
                                    <?= $mostrarOfertas ? 'checked' : '' ?> onchange="aplicarFiltros()">
                                Solo productos en oferta
                            </label>
                        </div>
                    </div>

                    <a href="/proyectoWebCS/Views/Home/productos.php" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise me-1"></i> Limpiar filtros
                    </a>
                </div>
            </div>

            <!-- Productos -->
            <div class="col-lg-9 col-md-8">
                <!-- Controles del catálogo -->
                <div class="controles-catalogo">
                    <div>
                        <span class="text-muted">Mostrando <strong><?= count($listaProductos) ?></strong> producto(s)</span>
                    </div>
                    <div>
                        <label for="ordenar" class="form-label small mb-0 me-2">Ordenar por:</label>
                        <select id="ordenar" class="form-select form-select-sm d-inline-block" style="width: auto;" onchange="aplicarFiltros()">
                            <option value="disponibilidad" <?= $ordenar === 'disponibilidad' ? 'selected' : '' ?>>Disponibilidad</option>
                            <option value="relevancia" <?= $ordenar === 'relevancia' ? 'selected' : '' ?>>Relevancia (Ofertas primero)</option>
                            <option value="precio_menor" <?= $ordenar === 'precio_menor' ? 'selected' : '' ?>>Menor precio</option>
                            <option value="precio_mayor" <?= $ordenar === 'precio_mayor' ? 'selected' : '' ?>>Mayor precio</option>
                            <option value="nombre" <?= $ordenar === 'nombre' ? 'selected' : '' ?>>Por nombre (A-Z)</option>
                        </select>
                    </div>
                </div>

                <!-- Grid de Productos -->
                <?php if (!empty($listaProductos)): ?>
                    <div class="row g-4">
                        <?php include $_SERVER["DOCUMENT_ROOT"] . "/proyectoWebCS/Views/components/cardProducto.php"; ?>
                    </div>
                <?php else: ?>
                    <div class="sin-resultados">
                        <i class="bi bi-inbox"></i>
                        <h4>No hay productos que coincidan con los filtros</h4>
                        <p class="mb-3">Intenta ajustar los filtros o explorar otras categorías</p>
                        <a href="/proyectoWebCS/Views/Home/productos.php" class="btn btn-color btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Ver todos los productos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer componente-->
    <?php require('../components/footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        function aplicarFiltros() {
            const params = new URLSearchParams();
            const busquedaActual = <?= json_encode($busqueda, JSON_UNESCAPED_UNICODE) ?>;

            if (busquedaActual && busquedaActual.trim() !== '') {
                params.set('q', busquedaActual);
            }

            // Categoría
            const categoriasSeleccionadas = document.querySelectorAll('.filtro-categoria:checked');
            if (categoriasSeleccionadas.length > 0 && categoriasSeleccionadas[0].value) {
                params.append('categoria', categoriasSeleccionadas[0].value);
            }

            // Precio
            const precioMin = document.getElementById('precioMin').value;
            const precioMax = document.getElementById('precioMax').value;
            if (precioMin) params.append('precio_min', precioMin);
            if (precioMax) params.append('precio_max', precioMax);

            // Ofertas
            if (document.getElementById('soloOfertas').checked) {
                params.append('ofertas', '1');
            }

            // Ordenar
            const ordenar = document.getElementById('ordenar').value;
            if (ordenar && ordenar !== 'disponibilidad') {
                params.append('ordenar', ordenar);
            }

            // Redirigir con parámetros
            const url = '/proyectoWebCS/Views/Home/productos.php?' + params.toString();
            window.location.href = url;
        }
    </script>
</body>
</html>
