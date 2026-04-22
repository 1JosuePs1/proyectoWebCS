# Sistema de Ofertas y Filtros - Guía de Uso

## ✨ Cambios Realizados

### 1. **Base de Datos**
Se agregaron dos nuevas columnas a la tabla `producto`:
- `enOferta` (TINYINT(1)): Boolean que indica si el producto está en oferta
- `precioOferta` (DECIMAL(10,2)): Almacena el precio con descuento

```sql
ALTER TABLE `producto` 
ADD COLUMN `enOferta` TINYINT(1) DEFAULT 0,
ADD COLUMN `precioOferta` DECIMAL(10,2) NULL;
```

### 2. **Funciones de Modelo (Models/productosModel.php)**

#### `ObtenerProductosEnOfertaModel()`
- Obtiene todos los productos que están en oferta
- Retorna un array con los productos en oferta

#### `FiltrarProductosModel($idCategoria, $precioMin, $precioMax, $ordenar)`
- Filtra productos por:
  - Categoría
  - Rango de precios
  - Orden: 'disponibilidad', 'relevancia', 'precio_menor', 'precio_mayor', 'nombre'

#### `ActualizarOfertaProductoModel($idProducto, $enOferta, $precioOferta)`
- Actualiza el estado de oferta de un producto
- Valida que el precio en oferta sea menor al original

### 3. **Controlador (Controllers/productoController.php)**

Se agregaron funciones públicas:
- `ActualizarOfertaController()`
- `ObtenerProductosEnOfertaController()`
- `FiltrarProductosController()`

El controlador maneja la acción `actualizar_oferta` vía POST/AJAX.

### 4. **Panel Admin Mejorado**

**Archivo:** `Views/Admin/editarProducto.php`

**Nuevas características:**
- ✅ Switch para activar/desactivar ofertas
- ✅ Campo de precio en oferta (se desbloquea al activar)
- ✅ Cálculo automático de descuento en tiempo real
- ✅ Muestra precio original, precio en oferta y porcentaje de descuento
- ✅ Validación que el precio en oferta sea menor al original

**Cómo usar:**
1. Ve a `Gestionar Oferta` en la página de editar producto
2. Activa el switch "Activar oferta para este producto"
3. Ingresa el precio en oferta
4. El sistema calcula automáticamente el descuento
5. Guarda los cambios

### 5. **Página de Productos Mejorada**

**Archivo nuevo:** `Views/Home/productos.php`

**Filtros disponibles:**
- 🏷️ **Categoría**: Filtrar por categoría específica
- 💰 **Rango de Precio**: Establecer precio mínimo y máximo
- 🏷️ **Ofertas**: Ver solo productos en oferta
- 📊 **Ordenar**: 
  - Disponibilidad
  - Relevancia (ofertas primero)
  - Menor precio
  - Mayor precio
  - Por nombre (A-Z)

**Características:**
- Interfaz lateral con filtros pegajosos (sticky)
- Muestra cantidad de productos encontrados
- Rango de precios disponibles
- Diseño responsive
- Mensaje cuando no hay resultados

### 6. **Componente de Tarjeta de Producto**

**Archivo:** `Views/components/cardProducto.php`

**Cambios:**
- 🏷️ Badge rojo con porcentaje de descuento en esquina superior
- 💰 Precio original tachado en gris
- 🔴 Nuevo precio en rojo cuando hay oferta
- Información de descuento clara

### 7. **Navegación Actualizada**

**Archivo:** `Views/components/nav.php`

- 📱 Link directo a "Productos" (página de catálogo)
- 🏷️ Link directo a "Ofertas" con filtro pre-aplicado
- Iconos intuitivos

## 🔧 Cómo Funciona

### Para Administrador:
1. **Crear Oferta:**
   - Ve a Editar Producto
   - Activa el switch de oferta
   - Ingresa el precio con descuento
   - Guarda

2. **Ver Estadísticas:**
   - Los productos en oferta aparecen con badge en la tienda
   - Se resaltan en la página de ofertas

### Para Cliente:
1. **Ver Ofertas:**
   - Click en "Ofertas" en el menú
   - O aplicar filtro "Solo productos en oferta" en catálogo

2. **Filtrar Productos:**
   - Usar los filtros laterales
   - Ver descuentos inmediatamente
   - Ordenar como desee

## 📄 Parámetros URL

La página de productos acepta estos parámetros GET:

```
/Views/Home/productos.php
  ?categoria=1          // ID de categoría
  &precio_min=10000     // Precio mínimo
  &precio_max=100000    // Precio máximo
  &ordenar=precio_menor // Orden: disponibilidad|relevancia|precio_menor|precio_mayor|nombre
  &ofertas=1            // Solo ofertas (1 = sí)
```

**Ejemplos:**
- Todos los productos en oferta: `?ofertas=1`
- Computadoras menores a ₡200,000: `?categoria=11&precio_max=200000`
- Ofertas de teclados ordenadas por precio: `?categoria=1&ofertas=1&ordenar=precio_menor`

## ⚠️ Validaciones

### Precio en Oferta:
- No puede estar vacío si la oferta está activada
- Debe ser mayor a 0
- Debe ser menor al precio original
- Sistema previene guardado de valores inválidos

### Campos Requeridos:
- Nombre del producto
- Marca
- Precio original
- Stock
- Categoría

## 🎨 Estilos

### Indicadores Visuales:
- 🔴 **Oferta activa**: Badge rojo con %descuento
- ⭐ **Precio original**: Tachado en gris
- 💰 **Precio oferta**: Texto grande en rojo
- 🎯 **Relevancia**: Productos en oferta aparecen primero

## 🧪 Testing

Para probar el sistema:

1. **Agregar Oferta:**
   - Edita un producto existente
   - Activa la oferta
   - Ingresa precio menor
   - Verifica que aparezca con badge en catálogo

2. **Filtrar:**
   - Aplica filtro de ofertas
   - Verifica que solo muestre productos en oferta
   - Prueba otros filtros combinados

3. **Validación:**
   - Intenta guardar oferta sin precio
   - Intenta guardar precio igual o mayor al original
   - El sistema debe rechazar

## 📱 Responsive

- ✅ Desktop: Filtros en lateral izquierdo
- ✅ Tablet: Filtros se adaptan
- ✅ Mobile: Filtros colapsables en sidebar

## 🔐 Seguridad

- Solo administradores pueden crear/editar ofertas
- Validación en servidor de todos los datos
- Protección contra inyección SQL (prepared statements)
- Sanitización de entradas

## 🚀 Próximas Mejoras (Opcionales)

- [ ] Búsqueda por nombre de producto
- [ ] Marcas disponibles como filtro
- [ ] Rango de fechas para ofertas temporales
- [ ] Descuentos automáticos por cantidad
- [ ] Email a clientes cuando hay oferta
- [ ] Historial de cambios de precio
- [ ] Reportes de productos más vendidos en oferta

---

**Creado:** 21 de abril de 2026
**Versión:** 1.0
