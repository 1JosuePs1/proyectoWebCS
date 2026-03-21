
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('categoriesSlider');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
  
    const scrollAmount = 200; 
    

    function scrollRight() {
        slider.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
        updateButtonStates();
    }
    
    function scrollLeft() {
        slider.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
        updateButtonStates();
    }
    

    function updateButtonStates() {
   
        if (slider.scrollLeft <= 0) {
            prevBtn.style.opacity = '0.5';
            prevBtn.style.cursor = 'not-allowed';
        } else {
            prevBtn.style.opacity = '1';
            prevBtn.style.cursor = 'pointer';
        }
        
   
        if (slider.scrollLeft >= slider.scrollWidth - slider.clientWidth - 10) {
            nextBtn.style.opacity = '0.5';
            nextBtn.style.cursor = 'not-allowed';
        } else {
            nextBtn.style.opacity = '1';
            nextBtn.style.cursor = 'pointer';
        }
    }
    
 
    prevBtn.addEventListener('click', scrollLeft);
    nextBtn.addEventListener('click', scrollRight);
    

    slider.addEventListener('scroll', updateButtonStates);
    
 
    updateButtonStates();
    
 
});

// Slider de imágenes por producto
document.querySelectorAll('.producto-slider').forEach(function(sliderDiv) {
    const btnPrev = sliderDiv.querySelector('.slider-prev');
    const btnNext = sliderDiv.querySelector('.slider-next');
    const img = sliderDiv.querySelector('.producto-imagen');

    if (!img) return;

    const imagenes = JSON.parse(sliderDiv.dataset.imagenes || '[]');
    let indiceActual = 0;

    if (imagenes.length <= 1) {
        if (btnPrev) btnPrev.style.display = 'none';
        if (btnNext) btnNext.style.display = 'none';
        return;
    }

    btnPrev.addEventListener('click', function() {
        indiceActual = (indiceActual - 1 + imagenes.length) % imagenes.length;
        img.src = imagenes[indiceActual];
    });

    btnNext.addEventListener('click', function() {
        indiceActual = (indiceActual + 1) % imagenes.length;
        img.src = imagenes[indiceActual];
    });
});
