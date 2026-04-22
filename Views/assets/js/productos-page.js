function aplicarFiltros() {
    const params = new URLSearchParams();
    const busquedaActual = document.body.dataset.busquedaActual || '';

    if (busquedaActual && busquedaActual.trim() !== '') {
        params.set('q', busquedaActual);
    }

    const categoriasSeleccionadas = document.querySelectorAll('.filtro-categoria:checked');
    if (categoriasSeleccionadas.length > 0 && categoriasSeleccionadas[0].value) {
        params.append('categoria', categoriasSeleccionadas[0].value);
    }

    const precioMin = document.getElementById('precioMin').value;
    const precioMax = document.getElementById('precioMax').value;
    if (precioMin) params.append('precio_min', precioMin);
    if (precioMax) params.append('precio_max', precioMax);

    if (document.getElementById('soloOfertas').checked) {
        params.append('ofertas', '1');
    }

    const ordenar = document.getElementById('ordenar').value;
    if (ordenar && ordenar !== 'disponibilidad') {
        params.append('ordenar', ordenar);
    }

    const url = '/proyectoWebCS/Views/Home/productos.php?' + params.toString();
    window.location.href = url;
}
