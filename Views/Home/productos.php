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
    $normalizarTexto = function ($texto) {
        $texto = trim((string) $texto);
        $texto = function_exists('mb_strtolower')
            ? mb_strtolower($texto, 'UTF-8')
            : strtolower($texto);

        if (function_exists('iconv')) {
            $sinAcentos = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
            if ($sinAcentos !== false) {
                $texto = $sinAcentos;
            }
        }

        return preg_replace('/\s+/', ' ', $texto);
    };

    $termino = $normalizarTexto($busqueda);

    $obtenerPuntajeBusqueda = function ($producto) use ($termino, $normalizarTexto) {
        $nombre = $normalizarTexto($producto['nombreProducto'] ?? '');
        $marca = $normalizarTexto($producto['marca'] ?? '');
        $descripcion = $normalizarTexto($producto['descripcionProducto'] ?? '');

        $puntaje = 0;

        if ($nombre === $termino) {
            $puntaje += 1000;
        } elseif (str_starts_with($nombre, $termino)) {
            $puntaje += 700;
        } elseif (str_contains($nombre, $termino)) {
            $puntaje += 500;
        }

        if ($marca === $termino) {
            $puntaje += 400;
        } elseif (str_starts_with($marca, $termino)) {
            $puntaje += 250;
        } elseif (str_contains($marca, $termino)) {
            $puntaje += 180;
        }

        if (str_contains($descripcion, $termino)) {
            $puntaje += 80;
        }

        if ($puntaje > 0) {
            $stock = intval($producto['stockProducto'] ?? 0);
            $estado = strtolower(trim((string) ($producto['estadoProducto'] ?? '')));
            if ($stock > 0 && $estado !== 'agotado') {
                $puntaje += 20;
            }
        }

        return $puntaje;
    };

    $listaProductos = array_values(array_filter($listaProductos, function ($p) use ($obtenerPuntajeBusqueda) {
        return $obtenerPuntajeBusqueda($p) > 0;
    }));

    usort($listaProductos, function ($a, $b) use ($obtenerPuntajeBusqueda) {
        $puntajeA = $obtenerPuntajeBusqueda($a);
        $puntajeB = $obtenerPuntajeBusqueda($b);

        if ($puntajeA === $puntajeB) {
            return 0;
        }

        return ($puntajeA > $puntajeB) ? -1 : 1;
    });
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
    <link rel="stylesheet" href="../assets/css/productos-page.css">
</head>

<body data-busqueda-actual="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>">
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
                        <?php if ($busqueda !== ''): ?>
                            <h4>No hay coincidencias para "<?= htmlspecialchars($busqueda) ?>"</h4>
                            <p class="mb-3">Prueba con otro nombre o marca.</p>
                        <?php else: ?>
                            <h4>No hay productos que coincidan con los filtros</h4>
                            <p class="mb-3">Intenta ajustar los filtros o explorar otras categorías</p>
                        <?php endif; ?>
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
    <script src="/proyectoWebCS/Views/assets/js/productos-page.js"></script>
</body>
</html>
