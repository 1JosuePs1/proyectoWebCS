function mostrarModalError(mensaje) {
    document.getElementById('mensajeError').textContent = mensaje;
    new bootstrap.Modal(document.getElementById('modalError')).show();
}
