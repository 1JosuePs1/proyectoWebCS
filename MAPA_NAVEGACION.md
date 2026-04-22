# 🗺️ MAPA DE NAVEGACIÓN - Sistema de Ofertas

## 📍 Rutas de Acceso Principales

```
INICIO
  │
  ├─ Página Principal (Home)
  │  ├─ /Views/Home/Home.php
  │  ├─ Botón: "Ver catálogo completo" ─────────────┐
  │  │                                               │
  │  └─ Filtrar por categoría ────────────────────┐  │
  │                                               │  │
  ├─ MENÚ NAVEGACIÓN                             │  │
  │  ├─ Inicio → /Views/Home/Home.php            │  │
  │  ├─ 📦 Productos → /Views/Home/productos.php ◄──┼──┐
  │  ├─ 🏷️ Ofertas → /Views/Home/productos.php?ofertas=1
  │  ├─ 👤 Mi Perfil (si autenticado)           │  │
  │  └─ 🛒 Carrito                              │  │
  │                                             │  │
  └─ PRODUCTOS.PHP (CATÁLOGO PRINCIPAL) ◄──────┴──┘
     │
     ├─ 🔴 Filtros Sidebar (Izquierda)
     │  ├─ Categoría (checkboxes)
     │  ├─ Rango de Precio
     │  ├─ Solo Ofertas
     │  └─ Limpiar Filtros
     │
     ├─ 📊 Grid de Productos (Centro)
     │  ├─ Ordenar por:
     │  │  ├─ Disponibilidad
     │  │  ├─ Relevancia (ofertas primero)
     │  │  ├─ Precio menor
     │  │  ├─ Precio mayor
     │  │  └─ Por nombre
     │  │
     │  └─ Tarjetas de Producto
     │     ├─ Imagen con Badge (-X%)
     │     ├─ Nombre y Marca
     │     ├─ Descripción
     │     ├─ Stock
     │     ├─ Precio Original (tachado)
     │     ├─ Precio Oferta (rojo)
     │     └─ [Botones: Carrito | Ver]
     │
     └─ Hacer clic en producto → /Views/producto/{slug}
        └─ Detalle del producto
           └─ Información completa
              └─ [Agregar al carrito]
```

---

## 🔐 PANEL ADMINISTRADOR

```
ADMIN LOGIN
  │
  └─ PANEL ADMIN
     │
     ├─ 📋 Editar Producto
     │  │  /Views/Admin/editarProducto.php?id={idProducto}
     │  │
     │  ├─ Datos Básicos (Nombre, Marca, Precio, Stock, Categoría)
     │  │
     │  └─ 🏷️ GESTIONAR OFERTA ◄─────── AQUÍ ES LA MAGIA
     │     │
     │     ├─ Switch: ☐ Activar oferta
     │     │  (Al activar aparecen los campos:)
     │     │
     │     ├─ Precio original: ₡30,000 (mostrado)
     │     ├─ Precio en oferta: [________] (input)
     │     │
     │     ├─ Descuento: 15% | Ahorro: ₡4,500 (auto-calculado)
     │     │
     │     └─ [GUARDAR CAMBIOS]
     │        └─ Actualiza BD
     │           └─ Aparece en tienda con badge
     │
     ├─ 📊 Lista de Productos
     │  └─ Ver todos los productos con opción de editar
     │
     └─ 📦 Crear Producto
        └─ Registro nuevo producto
```

---

## 🛒 FLUJO DE CLIENTE

```
CLIENTE ANÓNIMO
  │
  ├─ Navega por Home
  │  └─ Ve "Ofertas" en menú
  │     └─ Click → productos.php?ofertas=1
  │        └─ Ve solo productos en oferta
  │           └─ Elige uno
  │              └─ Agrega al carrito
  │                 └─ Checkout
  │                    └─ Pago
  │                       └─ ¡Comprado con descuento! ✅
  │
  └─ O navega por Productos
     └─ Aplica filtros
        ├─ Por categoría
        ├─ Por precio
        ├─ Solo ofertas
        └─ Por orden
           └─ Encuentra lo que busca
              └─ Lo compra

CLIENTE AUTENTICADO
  │
  ├─ Lo mismo que anónimo +
  │
  ├─ Ver "Mis Pedidos"
  │  └─ Historial de compras
  │
  └─ Perfil
     └─ Datos personales
```

---

## 📊 ESTRUCTURA DE BD

```
TABLA: producto
├─ idProducto (PK)
├─ idCategoria (FK)
├─ nombreProducto
├─ marca
├─ descripcionProducto
├─ precioProducto ◄─────── Precio normal
├─ enOferta (TINYINT) ◄─── Nuevo: 0 o 1
├─ precioOferta (DECIMAL) ◄ Nuevo: precio con descuento
├─ stockProducto
├─ imagenProducto
├─ estadoProducto
└─ Índice: idx_en_oferta

TABLA: categoria
├─ idCategoria (PK)
├─ nombreCategoria
└─ descripcionCategoria
```

---

## 🔧 FLUJO TÉCNICO DE ACTUALIZAR OFERTA

```
FORMULARIO ADMIN (JavaScript)
  │
  ├─ Usuario activa switch ☑
  │  └─ Se abre campo de precio
  │
  ├─ Usuario ingresa precio
  │  ├─ JavaScript calcula descuento EN TIEMPO REAL
  │  └─ Muestra: Porcentaje + Ahorro en ₡
  │
  ├─ Validación JavaScript:
  │  ├─ ¿Precio vacío? ❌
  │  ├─ ¿Precio >= original? ❌
  │  └─ ¿Precio válido? ✅
  │
  └─ Usuario hace click: Guardar Cambios
     │
     └─ FORMULARIO POST
        │
        ├─ Validación Servidor (PHP):
        │  ├─ ¿Admin? ✅
        │  ├─ ¿ID Producto válido? ✅
        │  ├─ ¿Precio válido? ✅
        │  └─ ¿Precio < original? ✅
        │
        └─ SI TODO OK:
           │
           └─ SQL: UPDATE producto SET enOferta=1, precioOferta=25500
              │
              └─ BD ACTUALIZADA ✅
                 │
                 └─ Cliente VE INMEDIATAMENTE:
                    ├─ Badge rojo: -15%
                    ├─ Precio original: ₡30,000 (tachado)
                    └─ Precio oferta: ₡25,500 (rojo)
```

---

## 🎯 FLUJO DE FILTROS

```
PÁGINA PRODUCTOS
  │
  ├─ Usuario selecciona filtro
  │  ├─ Categoría: Teclados
  │  ├─ Precio: ₡10,000 - ₡50,000
  │  ├─ Ordenar: Precio Menor
  │  └─ ☑ Solo ofertas
  │
  └─ JavaScript prepara URL:
     │
     └─ /Views/Home/productos.php?
           categoria=1
           &precio_min=10000
           &precio_max=50000
           &ordenar=precio_menor
           &ofertas=1
     │
     └─ Redirecciona
        │
        └─ PHP ejecuta:
           │
           ├─ Obtiene parámetros
           │
           ├─ Llamada a:
           │  FiltrarProductosController(
           │    idCategoria: 1,
           │    precioMin: 10000,
           │    precioMax: 50000,
           │    ordenar: 'precio_menor'
           │  )
           │
           ├─ SQL construida dinámicamente:
           │  SELECT * FROM producto
           │  WHERE idCategoria=1
           │  AND precioProducto >= 10000
           │  AND precioProducto <= 50000
           │  AND enOferta=1
           │  ORDER BY precioProducto ASC
           │
           └─ Retorna solo los productos que coinciden
              │
              └─ Se renderizan en el HTML
                 └─ Cliente ve resultados filtrados ✅
```

---

## 📱 ESTRUCTURA RESPONSIVE

```
DESKTOP (>992px)
┌─────────────────────────────────────┐
│         NAVBAR (menú principal)      │
├────────────┬───────────────────────┤
│ FILTROS    │  PRODUCTOS EN GRID    │
│ (sidebar)  │  (3 columnas)         │
│            │                       │
│ Pegajoso   │ [Prod1] [Prod2] [Prod3]
│ (sticky)   │ [Prod4] [Prod5] [Prod6]
│            │                       │
└────────────┴───────────────────────┘
│         FOOTER                      │
└─────────────────────────────────────┘

TABLET (768px - 992px)
┌─────────────────────┐
│   NAVBAR            │
├──────┬──────────────┤
│FILTRO│ GRID 2 COLS  │
│      │              │
│ Auto │[Prod1][Prod2]
│ ajus │[Prod3][Prod4]
│      │              │
└──────┴──────────────┘
│   FOOTER            │
└─────────────────────┘

MOBILE (<768px)
┌──────────────┐
│    NAVBAR    │
├──────────────┤
│  FILTROS     │ Colapsable
├──────────────┤
│ GRID 1 COL   │
│ [Prod1]      │
│              │
│ [Prod2]      │
│              │
│ [Prod3]      │
└──────────────┘
│    FOOTER    │
└──────────────┘
```

---

## 🔄 CICLO DE VIDA DE UNA OFERTA

```
DÍA 1:
  Admin crea oferta (switch ON)
  │
  └─ Producto aparece con badge en tienda

DÍA 2-30:
  Clientes ven el badge
  │
  └─ Algunos compran con descuento

DÍA 31:
  Admin decide terminar oferta
  │
  ├─ Va a Editar Producto
  ├─ Desactiva switch OFF
  └─ Guarda cambios
     │
     └─ Badge desaparece inmediatamente
        └─ Vuelve a precio normal

OPCIONAL:
  Admin puede reactivarla en cualquier momento
  └─ Vuelve con el mismo precio de oferta O uno nuevo
```

---

## 🎨 ELEMENTOS VISUALES

```
BADGE OFERTA
┌────────────┐
│ 🏷️ -15%   │ Rojo + blanco
└────────────┘

PRECIO ORIGINAL
₡30,000    ← Gris + Tachado

PRECIO EN OFERTA
₡25,500    ← Rojo + Grande

BOTONES
[🛒 Agregar]  ← Secundario
[👁️ Ver]     ← Terciario

INDICADORES
✅ En stock
⚠️ Pocas unidades
❌ Agotado
🏷️ En oferta
```

---

## ✅ CHECKLIST DE NAVEGACIÓN

- [x] Home → Menú → Productos (catalogo con filtros)
- [x] Home → Menú → Ofertas (solo productos en oferta)
- [x] Productos → Filtros laterales (categoria, precio, ofertas)
- [x] Productos → Ordenar (5 opciones diferentes)
- [x] Productos → Click producto → Detalle
- [x] Detalle → [Agregar al carrito]
- [x] Admin → Editar → Gestionar Oferta
- [x] Admin → Activar switch + precio
- [x] Admin → Guardar → Actualiza tienda inmediatamente

---

## 📊 RESUMEN DE RUTAS

| Descripción | URL |
|---|---|
| Home | `/Views/Home/Home.php` |
| Catálogo Completo | `/Views/Home/productos.php` |
| Solo Ofertas | `/Views/Home/productos.php?ofertas=1` |
| Categoría X | `/Views/Home/productos.php?categoria=1` |
| Filtro Completo | `/Views/Home/productos.php?categoria=5&precio_min=100000&precio_max=500000&ordenar=precio_menor` |
| Editar Producto | `/Views/Admin/editarProducto.php?id=1` |

---

**Creado:** 21 de abril de 2026
**Versión:** 1.0
**Estado:** ✅ LISTO PARA NAVEGAR
