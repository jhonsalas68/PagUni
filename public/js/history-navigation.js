/**
 * Sistema de navegación con historial del navegador
 * Permite que los filtros y búsquedas se mantengan en la URL
 * y el botón "atrás" funcione correctamente
 */

(function() {
    'use strict';

    // Función para actualizar la URL sin recargar la página
    function updateURLWithoutReload(params) {
        const url = new URL(window.location);
        
        // Actualizar o agregar parámetros
        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== '' && params[key] !== undefined) {
                url.searchParams.set(key, params[key]);
            } else {
                url.searchParams.delete(key);
            }
        });

        // Usar pushState para agregar al historial
        window.history.pushState({ path: url.href }, '', url.href);
    }

    // Función para obtener parámetros de la URL
    function getURLParams() {
        const params = {};
        const searchParams = new URLSearchParams(window.location.search);
        
        for (const [key, value] of searchParams) {
            params[key] = value;
        }
        
        return params;
    }

    // Función para aplicar filtros desde la URL
    function applyFiltersFromURL() {
        const params = getURLParams();
        
        // Aplicar cada parámetro a su campo correspondiente
        Object.keys(params).forEach(key => {
            const element = document.getElementById(key) || 
                          document.querySelector(`[name="${key}"]`);
            
            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = params[key] === 'true' || params[key] === '1';
                } else {
                    element.value = params[key];
                }
            }
        });
    }

    // Función para manejar el envío de formularios con AJAX
    function handleFormSubmit(form, callback) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const params = {};
            
            // Convertir FormData a objeto
            for (const [key, value] of formData) {
                if (value !== '' && value !== null) {
                    params[key] = value;
                }
            }
            
            // Actualizar URL
            updateURLWithoutReload(params);
            
            // Ejecutar callback si existe
            if (typeof callback === 'function') {
                callback(params);
            }
        });
    }

    // Función para manejar cambios en inputs individuales
    function handleInputChange(input, callback) {
        input.addEventListener('change', function() {
            const params = getURLParams();
            params[input.name || input.id] = input.value;
            
            // Actualizar URL
            updateURLWithoutReload(params);
            
            // Ejecutar callback si existe
            if (typeof callback === 'function') {
                callback(params);
            }
        });
    }

    // Manejar el evento popstate (botón atrás/adelante del navegador)
    window.addEventListener('popstate', function(event) {
        // Simplemente ir a la URL anterior sin recargar
        if (event.state && event.state.path) {
            window.location.href = event.state.path;
        } else {
            // Si no hay estado, usar history.back()
            window.history.back();
        }
    });

    // Función para cargar contenido con AJAX
    function loadContent(url, targetElement, showLoader = true) {
        if (showLoader) {
            targetElement.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-3">Cargando...</p></div>';
        }

        return fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.text();
        })
        .then(html => {
            targetElement.innerHTML = html;
            
            // Reinicializar eventos en el nuevo contenido
            initializeEvents();
            
            return html;
        })
        .catch(error => {
            console.error('Error al cargar contenido:', error);
            targetElement.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error al cargar el contenido. Por favor, intenta de nuevo.
                </div>
            `;
        });
    }

    // Función para inicializar eventos
    function initializeEvents() {
        // Aplicar filtros desde la URL al cargar
        applyFiltersFromURL();
        
        // Manejar formularios de filtro
        document.querySelectorAll('.filter-form').forEach(form => {
            handleFormSubmit(form, function(params) {
                const targetId = form.dataset.target || 'content-area';
                const target = document.getElementById(targetId);
                
                if (target) {
                    const url = form.action + '?' + new URLSearchParams(params).toString();
                    loadContent(url, target);
                }
            });
        });
        
        // Manejar inputs de filtro individuales
        document.querySelectorAll('.filter-input').forEach(input => {
            handleInputChange(input, function(params) {
                const targetId = input.dataset.target || 'content-area';
                const target = document.getElementById(targetId);
                const baseUrl = input.dataset.url || window.location.pathname;
                
                if (target) {
                    const url = baseUrl + '?' + new URLSearchParams(params).toString();
                    loadContent(url, target);
                }
            });
        });
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeEvents);
    } else {
        initializeEvents();
    }

    // Exportar funciones globales
    window.HistoryNavigation = {
        updateURL: updateURLWithoutReload,
        getParams: getURLParams,
        applyFilters: applyFiltersFromURL,
        loadContent: loadContent,
        handleForm: handleFormSubmit,
        handleInput: handleInputChange
    };

})();
