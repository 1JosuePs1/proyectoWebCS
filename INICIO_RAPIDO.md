# 🚀 INICIO RÁPIDO - Sistema de Ofertas

## ¡Bienvenido! Aquí está todo lo que necesitas saber para empezar

### 📍 Archivos Principales Creados/Modificados

```
✅ NUEVOS ARCHIVOS:
   └─ /Views/Home/productos.php          ← Página de catálogo con filtros
   └─ GUIA_OFERTAS.md                   ← Documentación completa
   └─ RESUMEN_IMPLEMENTACION.md          ← Resumen visual
   └─ actualizar_bd.php                  ← Script de actualización (ya ejecutado)

✅ MODIFICADOS:
   ├─ Models/productosModel.php          ← +3 funciones
   ├─ Controllers/productoController.php ← +3 funciones + handler POST
   ├─ Views/Admin/editarProducto.php     ← +Switch + Campos + JS
   ├─ Views/components/cardProducto.php  ← +Badge + Precios mejorados
   ├─ Views/components/nav.php           ← +Links a productos/ofertas
   └─ Views/Home/Home.php                ← +Botón catálogo
```

---

## ⚡ 3 PASOS PARA USAR (Lo Más Importante)

### PASO 1: CREAR UNA OFERTA (Admin)
```
1. Ve a: Panel Admin → Editar Producto
2. Desplázate hasta "Gestionar Oferta"
3. ☑️ Activa el switch
4. Ingresa precio con descuento (ej: ₡25,500 en lugar de ₡30,000)
5. ✅ Observa el descuento calculado automáticamente
6. Haz click en "Guardar cambios"
```

### PASO 2: VER OFERTA EN LA TIENDA (Cliente)
```
1. En el menú: Click en "Ofertas"
   O: Click en "Productos" → Filtro "Solo productos en oferta"
2. 🏷️ Verás el producto con badge -15% (ejemplo)
3. El precio original estará tachado
4. El precio en oferta en ROJO
```

### PASO 3: USAR FILTROS (Cliente)
```
En la página de Productos (productos.php):
└─ Categoría: Elige una categoría
└─ Precio: Define mínimo y máximo
└─ Ordenar: Elige cómo ordenar (Oferta aparece en "Relevancia")
└─ Ofertas: Marca solo si quieres ver ofertas
```

---

## 🎯 EJEMPLOS DE USO

### Ejemplo 1: Ver solo ofertas
```
Ruta: /proyectoWebCS/Views/Home/productos.php?ofertas=1
```

### Ejemplo 2: Ver teclados en oferta menores de ₡50,000
```
Ruta: /proyectoWebCS/Views/Home/productos.php?categoria=1&ofertas=1&precio_max=50000&ordenar=precio_menor
```

### Ejemplo 3: Ver componentes de ₡100k a ₡500k ordenados por precio
```
Ruta: /proyectoWebCS/Views/Home/productos.php?categoria=5&precio_min=100000&precio_max=500000&ordenar=precio_menor
```

---

## 📊 CARACTERÍSTICAS NUEVAS DETALLADAS

### En Panel Admin:
```
┌─────────────────────────────────────────────┐
│ Gestionar Oferta                            │
├─────────────────────────────────────────────┤
│ ☐ Activar oferta para este producto         │
├─────────────────────────────────────────────┤
│ Cuando activas el switch ↓↓↓                │
│                                             │
│ Precio original: ₡30,000                   │
│ Precio en oferta: [___________] ₡25,500    │
│ Descuento: 15% | Ahorro: ₡4,500            │
│                                             │
│ [GUARDAR CAMBIOS]                          │
└─────────────────────────────────────────────┘
```

### En Catálogo/Tienda:
```
FILTROS (Sidebar izquierdo)
├─ Categoría (checkboxes)
├─ Rango de Precio (min/max)
├─ Solo productos en oferta
└─ [Limpiar filtros]

PRODUCTOS (Grid derecha)
┌─────────────────┐
│ [Imagen]  -15% │ ← Badge oferta
│ Nombre          │
│ Descripción     │
│ Stock: 5        │
│ ₡30,000 ✖️      │ ← Precio original (tachado)
│ ₡25,500         │ ← Precio en oferta (ROJO)
│ [🛒] [👁]       │
└─────────────────┘
```

---

## 🔄 FLUJO COMPLETO

```
ADMINISTRADOR
    ↓
1. Edita Producto
    ↓
2. Activa switch de oferta
    ↓
3. Ingresa precio en oferta
    ↓
4. Sistema calcula descuento
    ↓
5. Guarda cambios
    ↓
CLIENTE VE:
    ↓
1. Badge rojo con -X%
2. Precio original tachado
3. Precio en oferta en rojo
    ↓
4. Puede filtrar por ofertas
5. Puede ordenar por relevancia
    ↓
COMPRA CON DESCUENTO ✅
```

---

## 🛠️ VALIDACIONES IMPORTANTES

### Qué NO permite hacer el sistema:

❌ Guardar oferta sin precio en oferta
❌ Guardar precio en oferta igual o mayor al original
❌ Guardar precio en oferta inválido
❌ Acceso a admin sin permisos (solo admin rol=1)
❌ Manipular datos vía URL (validación servidor)

### Qué SÍ permite:

✅ Activar/desactivar oferta cuando quieras
✅ Cambiar precio en oferta cuando quieras
✅ Ver descuento en tiempo real
✅ Filtrar por múltiples criterios
✅ Ordenar de varias formas

---

## 📱 RESPONSIVE DESIGN

| Dispositivo | Vista | Filtros |
|---|---|---|
| **Desktop** | Sidebar + Grid 3 cols | Visible siempre |
| **Tablet** | Sidebar + Grid 2 cols | Se adapta |
| **Mobile** | Full ancho + Grid 1 col | Colapsable |

---

## 🔍 TESTING - Verifica que todo funcione

### Test 1: Crear Oferta
```
1. Admin → Editar Producto
2. Busca "Gestionar Oferta"
3. Activa el switch
4. Ingresa: ₡25,500 (en un producto de ₡30,000)
5. Verifica que diga "-15%" y "Ahorro: ₡4,500"
6. Guarda
7. En tienda debe mostrar badge -15%
   ✅ ÉXITO
```

### Test 2: Filtrar Ofertas
```
1. Cliente → Menú → Ofertas
   O: Productos → Marca "Solo productos en oferta"
2. Debe mostrar solo productos en oferta
3. Aplica otros filtros (precio, categoría)
4. Debe funcionar correctamente
   ✅ ÉXITO
```

### Test 3: Validación
```
1. Intenta guardar oferta sin precio
   → Debe mostrar error ✅
2. Intenta guardar precio mayor al original
   → Debe mostrar error ✅
3. Intenta guardar precio inválido
   → Debe mostrar error ✅
```

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

- [x] Base de datos actualizada (BD actualizar_bd.php)
- [x] Modelos con funciones de filtro y oferta
- [x] Controlador maneja actualizaciones de oferta
- [x] Panel admin con switch y campos
- [x] Página de productos con filtros completos
- [x] Tarjetas muestran ofertas correctamente
- [x] Navegación actualizada
- [x] Validaciones en cliente y servidor
- [x] Responsive design
- [x] Documentación completa

---

## 🎓 PARA APRENDER MÁS

**Lee estos archivos:**
1. `GUIA_OFERTAS.md` ← Guía técnica completa
2. `RESUMEN_IMPLEMENTACION.md` ← Resumen visual
3. Código en los archivos PHP con comentarios

---

## 🆘 TROUBLESHOOTING

### Problema: "No veo la sección de ofertas en admin"
**Solución:** Verifica que el archivo editarProducto.php esté actualizado

### Problema: "Los filtros no funcionan"
**Solución:** Verifica que FiltrarProductosController() exista en productoController.php

### Problema: "No veo badges en los productos"
**Solución:** Verifica que cardProducto.php esté actualizado

### Problema: "La BD no tiene las columnas nuevas"
**Solución:** Ejecuta: `actualizar_bd.php` desde el navegador

---

## 💡 PRÓXIMAS MEJORAS (Opcional)

- [ ] Búsqueda por nombre
- [ ] Filtro por marca
- [ ] Ofertas con fecha de vencimiento
- [ ] Notificaciones de nuevas ofertas
- [ ] Email al usuario cuando un producto entra en oferta
- [ ] Descuentos automáticos por cantidad

---

## 📞 RESUMEN FINAL

✅ El sistema está **100% funcional**
✅ Listo para **usar en producción**
✅ Con **validaciones completas**
✅ Interfaz **amigable y responsive**
✅ **Totalmente documentado**

**¡A disfrutar del nuevo sistema! 🎉**

---

**Fecha de Implementación:** 21 de abril de 2026
**Estado:** ✅ LISTO PARA USAR
**Versión:** 1.0
