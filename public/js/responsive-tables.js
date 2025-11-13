/**
 * Responsive Tables Handler
 * Convierte tablas automáticamente en cards responsivas en móviles
 */

(function() {
    'use strict';
    
    // Función para hacer tablas responsivas
    function makeTablesResponsive() {
        const tables = document.querySelectorAll('.table:not(.table-no-responsive)');
        
        tables.forEach(table => {
            // Añadir clase para cards en móvil
            if (!table.classList.contains('table-mobile-cards')) {
                table.classList.add('table-mobile-cards');
            }
            
            // Obtener headers
            const headers = [];
            const headerCells = table.querySelectorAll('thead th');
            headerCells.forEach(th => {
                headers.push(th.textContent.trim());
            });
            
            // Añadir data-label a cada celda
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (headers[index] && !cell.hasAttribute('data-label')) {
                        cell.setAttribute('data-label', headers[index]);
                    }
                });
            });
        });
    }
    
    // Función para optimizar imágenes en tablas
    function optimizeTableImages() {
        const images = document.querySelectorAll('.table img');
        images.forEach(img => {
            if (!img.style.maxWidth) {
                img.style.maxWidth = '100px';
                img.style.height = 'auto';
            }
        });
    }
    
    // Función para mejorar botones en tablas
    function optimizeTableButtons() {
        const buttonGroups = document.querySelectorAll('.table .btn-group');
        buttonGroups.forEach(group => {
            // En móvil, convertir a columna
            if (window.innerWidth < 768) {
                group.style.flexDirection = 'column';
                group.style.width = '100%';
                
                const buttons = group.querySelectorAll('.btn');
                buttons.forEach(btn => {
                    btn.style.width = '100%';
                    btn.style.marginBottom = '0.25rem';
                });
            }
        });
    }
    
    // Función para añadir indicador de scroll
    function addScrollIndicator() {
        const scrollContainers = document.querySelectorAll('.table-responsive');
        
        scrollContainers.forEach(container => {
            if (container.scrollWidth > container.clientWidth) {
                container.classList.add('has-scroll');
                
                // Añadir indicador si no existe
                if (!container.querySelector('.scroll-indicator')) {
                    const indicator = document.createElement('div');
                    indicator.className = 'scroll-indicator';
                    indicator.innerHTML = '<i class="fas fa-arrows-alt-h"></i> Desliza';
                    indicator.style.cssText = `
                        position: absolute;
                        right: 10px;
                        top: 10px;
                        background: rgba(13, 110, 253, 0.9);
                        color: white;
                        padding: 0.25rem 0.5rem;
                        border-radius: 0.25rem;
                        font-size: 0.75rem;
                        pointer-events: none;
                        z-index: 10;
                    `;
                    container.style.position = 'relative';
                    container.appendChild(indicator);
                    
                    // Ocultar al hacer scroll
                    container.addEventListener('scroll', function() {
                        if (this.scrollLeft > 10) {
                            indicator.style.opacity = '0';
                        } else {
                            indicator.style.opacity = '1';
                        }
                    });
                }
            }
        });
    }
    
    // Función para lazy load de tablas grandes
    function lazyLoadTableRows() {
        const tables = document.querySelectorAll('.table-lazy-load');
        
        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            const rowsPerPage = 20;
            let currentPage = 1;
            
            // Ocultar filas después de la primera página
            rows.forEach((row, index) => {
                if (index >= rowsPerPage) {
                    row.style.display = 'none';
                    row.classList.add('lazy-row');
                }
            });
            
            // Añadir botón "Cargar más"
            if (rows.length > rowsPerPage) {
                const loadMoreBtn = document.createElement('button');
                loadMoreBtn.className = 'btn btn-outline-primary btn-block mt-3';
                loadMoreBtn.textContent = 'Cargar más';
                loadMoreBtn.onclick = function() {
                    currentPage++;
                    const start = (currentPage - 1) * rowsPerPage;
                    const end = start + rowsPerPage;
                    
                    for (let i = start; i < end && i < rows.length; i++) {
                        rows[i].style.display = '';
                    }
                    
                    if (end >= rows.length) {
                        loadMoreBtn.style.display = 'none';
                    }
                };
                
                table.parentElement.appendChild(loadMoreBtn);
            }
        });
    }
    
    // Función para añadir búsqueda rápida en tablas
    function addQuickSearch() {
        const tables = document.querySelectorAll('.table-searchable');
        
        tables.forEach(table => {
            // Verificar si ya tiene búsqueda
            if (table.previousElementSibling?.classList.contains('table-search')) {
                return;
            }
            
            // Crear input de búsqueda
            const searchContainer = document.createElement('div');
            searchContainer.className = 'table-search mb-3';
            searchContainer.innerHTML = `
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Buscar en la tabla...">
                </div>
            `;
            
            table.parentElement.insertBefore(searchContainer, table);
            
            const searchInput = searchContainer.querySelector('input');
            const rows = table.querySelectorAll('tbody tr');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    }
    
    // Función para mejorar accesibilidad
    function improveAccessibility() {
        // Añadir roles ARIA
        const tables = document.querySelectorAll('.table');
        tables.forEach(table => {
            if (!table.hasAttribute('role')) {
                table.setAttribute('role', 'table');
            }
        });
        
        // Mejorar navegación por teclado
        const clickableRows = document.querySelectorAll('.table tbody tr[onclick], .table tbody tr[data-href]');
        clickableRows.forEach(row => {
            row.setAttribute('tabindex', '0');
            row.setAttribute('role', 'button');
            
            row.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    }
    
    // Función para añadir animaciones suaves
    function addAnimations() {
        const rows = document.querySelectorAll('.table tbody tr');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 50);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        rows.forEach(row => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(20px)';
            row.style.transition = 'all 0.3s ease';
            observer.observe(row);
        });
    }
    
    // Inicializar cuando el DOM esté listo
    function init() {
        makeTablesResponsive();
        optimizeTableImages();
        optimizeTableButtons();
        addScrollIndicator();
        improveAccessibility();
        
        // Características opcionales (comentar si no se necesitan)
        // lazyLoadTableRows();
        // addQuickSearch();
        // addAnimations();
    }
    
    // Ejecutar al cargar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Re-ejecutar al cambiar tamaño de ventana
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            optimizeTableButtons();
            addScrollIndicator();
        }, 250);
    });
    
    // Exponer funciones globalmente para uso manual
    window.ResponsiveTables = {
        init: init,
        makeResponsive: makeTablesResponsive,
        addSearch: addQuickSearch,
        addAnimations: addAnimations
    };
    
})();
