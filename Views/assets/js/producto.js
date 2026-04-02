function mostrarImagen(idx) {
    var miniaturas = document.querySelectorAll('.producto-galeria-miniaturas img');
    var principal = document.getElementById('imgPrincipal');
    miniaturas.forEach(function(img, i) {
        img.classList.toggle('active', i === idx);
    });
    principal.src = miniaturas[idx].src;
}
