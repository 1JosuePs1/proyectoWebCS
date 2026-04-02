function confirmarEliminar(id, nombre) {
    document.getElementById('nombreProductoEliminar').textContent = nombre;
    document.getElementById('btnConfirmarEliminar').href = '/proyectoWebCS/Controllers/productoController.php?eliminar=' + id;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
