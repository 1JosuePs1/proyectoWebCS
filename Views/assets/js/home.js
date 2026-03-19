
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
