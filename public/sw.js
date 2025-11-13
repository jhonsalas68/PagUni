const CACHE_NAME = 'sgu-v2.1.0';
const STATIC_CACHE = 'sgu-static-v2.1.0';
const DYNAMIC_CACHE = 'sgu-dynamic-v2.1.0';

const urlsToCache = [
  '/',
  '/offline.html',
  '/manifest.json',
  '/css/responsive.css',
  '/js/pwa-handler.js',
  '/js/history-navigation.js',
  '/js/pagination-scroll.js',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-512x512.png',
  // Rutas principales
  '/login',
  // Assets críticos
  'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
  'https://code.jquery.com/jquery-3.6.0.min.js'
];

// Instalación del Service Worker
self.addEventListener('install', event => {
  console.log('SW: Instalando...');
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        console.log('SW: Cache estático abierto');
        return cache.addAll(urlsToCache);
      })
      .then(() => {
        console.log('SW: Recursos cacheados exitosamente');
        return self.skipWaiting();
      })
      .catch(error => {
        console.error('SW: Error al cachear recursos:', error);
      })
  );
});

// Activación del Service Worker
self.addEventListener('activate', event => {
  console.log('SW: Activando...');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
            console.log('SW: Eliminando cache antiguo:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => {
      console.log('SW: Activado y listo');
      return self.clients.claim();
    })
  );
});

// Interceptar peticiones de red
self.addEventListener('fetch', event => {
  // Solo cachear peticiones GET
  if (event.request.method !== 'GET') {
    return;
  }

  // Ignorar peticiones de extensiones del navegador
  if (event.request.url.startsWith('chrome-extension://') || 
      event.request.url.startsWith('moz-extension://')) {
    return;
  }

  // Estrategia: Cache First para recursos estáticos
  if (isStaticResource(event.request.url)) {
    event.respondWith(cacheFirst(event.request));
    return;
  }

  // Estrategia: Network First para páginas dinámicas
  if (isPageRequest(event.request)) {
    event.respondWith(networkFirst(event.request));
    return;
  }

  // Estrategia: Stale While Revalidate para APIs
  if (isApiRequest(event.request.url)) {
    event.respondWith(staleWhileRevalidate(event.request));
    return;
  }
});

// Funciones auxiliares
function isStaticResource(url) {
  return url.includes('/css/') || 
         url.includes('/js/') || 
         url.includes('/images/') ||
         url.includes('bootstrap') ||
         url.includes('font-awesome') ||
         url.includes('jquery') ||
         url.includes('.png') ||
         url.includes('.jpg') ||
         url.includes('.jpeg') ||
         url.includes('.svg') ||
         url.includes('.ico');
}

function isPageRequest(request) {
  return request.headers.get('accept').includes('text/html');
}

function isApiRequest(url) {
  return url.includes('/api/') || url.includes('/reportes/');
}

// Estrategia Cache First
async function cacheFirst(request) {
  try {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }

    const networkResponse = await fetch(request);
    if (networkResponse.ok) {
      const cache = await caches.open(STATIC_CACHE);
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  } catch (error) {
    console.log('SW: Error en cache first:', error);
    return caches.match('/offline.html');
  }
}

// Estrategia Network First
async function networkFirst(request) {
  try {
    const networkResponse = await fetch(request);
    if (networkResponse.ok) {
      const cache = await caches.open(DYNAMIC_CACHE);
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  } catch (error) {
    console.log('SW: Sin conexión, buscando en cache...');
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }
    
    // Si es una página HTML, mostrar página offline
    if (isPageRequest(request)) {
      return caches.match('/offline.html');
    }
    
    throw error;
  }
}

// Estrategia Stale While Revalidate
async function staleWhileRevalidate(request) {
  const cache = await caches.open(DYNAMIC_CACHE);
  const cachedResponse = await cache.match(request);
  
  const fetchPromise = fetch(request).then(networkResponse => {
    if (networkResponse.ok) {
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  }).catch(() => cachedResponse);

  return cachedResponse || fetchPromise;
}

// Sincronización en segundo plano
self.addEventListener('sync', event => {
  if (event.tag === 'background-sync') {
    event.waitUntil(doBackgroundSync());
  }
});

function doBackgroundSync() {
  // Aquí se pueden sincronizar datos pendientes cuando se recupere la conexión
  return new Promise((resolve) => {
    console.log('Sincronización en segundo plano ejecutada');
    resolve();
  });
}

// Notificaciones push
self.addEventListener('push', event => {
  const options = {
    body: event.data ? event.data.text() : 'Nueva notificación del SGU',
    icon: '/images/icons/icon-192x192.png',
    badge: '/images/icons/icon-72x72.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'Ver detalles',
        icon: '/images/icons/checkmark.png'
      },
      {
        action: 'close',
        title: 'Cerrar',
        icon: '/images/icons/xmark.png'
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification('Sistema de Gestión Universitaria', options)
  );
});

// Manejo de clics en notificaciones
self.addEventListener('notificationclick', event => {
  event.notification.close();

  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/')
    );
  }
});