/**
 * Mantener posición del scroll al paginar
 */
(function() {
    'use strict';
    
    // Guardar posición del scroll antes de cambiar de página
    document.addEventListener('DOMContentLoaded', function() {
        // Restaurar posición si existe
        const scrollPos = sessionStorage.getItem('scrollPosition');
        if (scrollPos) {
            window.scrollTo(0, parseInt(scrollPos));
            sessionStorage.removeItem('scrollPosition');
        }
        
        // Guardar posición al hacer clic en enlaces de paginación
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                sessionStorage.setItem('scrollPosition', window.pageYOffset);
            });
        });
    });
})();
