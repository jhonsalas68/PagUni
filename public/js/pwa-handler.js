/**
 * PWA Handler - Gestión de Progressive Web App
 * Optimizado para experiencia móvil y offline
 */

// Variables globales
let deferredPrompt;
let isStandalone = false;

// Detectar si está en modo standalone (instalado)
if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
    isStandalone = true;
    document.body.classList.add('pwa-standalone');
}

// Evento beforeinstallprompt - Capturar prompt de instalación
window.addEventListener('beforeinstallprompt', (e) => {
    console.log('PWA: beforeinstallprompt event fired');
    e.preventDefault();
    deferredPrompt = e;
    
    // Mostrar botón de instalación si existe
    const installButton = document.getElementById('pwa-install-btn');
    if (installButton) {
        installButton.style.display = 'block';
        installButton.addEventListener('click', installPWA);
    }
});

// Función para instalar PWA
async function installPWA() {
    if (!deferredPrompt) {
        console.log('PWA: No hay prompt disponible');
        return;
    }
    
    // Mostrar prompt de instalación
    deferredPrompt.prompt();
    
    // Esperar respuesta del usuario
    const { outcome } = await deferredPrompt.userChoice;
    console.log(`PWA: Usuario ${outcome === 'accepted' ? 'aceptó' : 'rechazó'} la instalación`);
    
    // Limpiar prompt
    deferredPrompt = null;
    
    // Ocultar botón
    const installButton = document.getElementById('pwa-install-btn');
    if (installButton) {
        installButton.style.display = 'none';
    }
}

// Evento appinstalled - PWA instalada
window.addEventListener('appinstalled', () => {
    console.log('PWA: Aplicación instalada exitosamente');
    deferredPrompt = null;
    
    // Mostrar mensaje de éxito
    showNotification('¡Aplicación instalada!', 'success');
});

// Detectar cambios en la conexión
window.addEventListener('online', () => {
    console.log('PWA: Conexión restaurada');
    showNotification('Conexión restaurada', 'success');
    document.body.classList.remove('offline');
    document.body.classList.add('online');
});

window.addEventListener('offline', () => {
    console.log('PWA: Sin conexión');
    showNotification('Sin conexión a internet', 'warning');
    document.body.classList.remove('online');
    document.body.classList.add('offline');
});

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Verificar si ya existe un contenedor de notificaciones
    let container = document.getElementById('pwa-notifications');
    if (!container) {
        container = document.createElement('div');
        container.id = 'pwa-notifications';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 300px;
        `;
        document.body.appendChild(container);
    }
    
    // Crear notificación
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.style.cssText = `
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease;
    `;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    container.appendChild(notification);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Manejar actualizaciones del Service Worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.addEventListener('controllerchange', () => {
        console.log('PWA: Nueva versión disponible');
        showUpdateNotification();
    });
}

// Mostrar notificación de actualización
function showUpdateNotification() {
    const notification = document.createElement('div');
    notification.className = 'alert alert-info';
    notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-width: 300px;
        text-align: center;
    `;
    notification.innerHTML = `
        <strong>Nueva versión disponible</strong><br>
        <button class="btn btn-sm btn-primary mt-2" onclick="window.location.reload()">
            Actualizar ahora
        </button>
    `;
    
    document.body.appendChild(notification);
}

// Optimizaciones de rendimiento
document.addEventListener('DOMContentLoaded', () => {
    // Lazy loading de imágenes
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    } else {
        // Fallback para navegadores antiguos
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
        document.body.appendChild(script);
    }
    
    // Prevenir zoom en inputs en iOS
    if (/iPhone|iPad|iPod/.test(navigator.userAgent)) {
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.style.fontSize < '16px') {
                input.style.fontSize = '16px';
            }
        });
    }
    
    // Mejorar scroll en iOS
    if (/iPhone|iPad|iPod/.test(navigator.userAgent)) {
        document.body.style.webkitOverflowScrolling = 'touch';
    }
});

// Gestión de orientación
window.addEventListener('orientationchange', () => {
    // Forzar recalculo del layout
    document.body.style.height = window.innerHeight + 'px';
    setTimeout(() => {
        document.body.style.height = '';
    }, 500);
});

// Prevenir comportamiento de pull-to-refresh en algunos navegadores
let startY = 0;
document.addEventListener('touchstart', (e) => {
    startY = e.touches[0].pageY;
}, { passive: true });

document.addEventListener('touchmove', (e) => {
    const y = e.touches[0].pageY;
    if (window.scrollY === 0 && y > startY) {
        e.preventDefault();
    }
}, { passive: false });

// Añadir animación CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .pwa-standalone {
        padding-top: env(safe-area-inset-top);
        padding-bottom: env(safe-area-inset-bottom);
    }
    
    .offline .online-only {
        opacity: 0.5;
        pointer-events: none;
    }
    
    .offline::before {
        content: 'Sin conexión';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: #ffc107;
        color: #000;
        text-align: center;
        padding: 0.5rem;
        z-index: 9998;
        font-weight: 600;
    }
`;
document.head.appendChild(style);

// Log de información PWA
console.log('PWA Handler inicializado');
console.log('Modo standalone:', isStandalone);
console.log('Service Worker soportado:', 'serviceWorker' in navigator);
console.log('Notificaciones soportadas:', 'Notification' in window);
