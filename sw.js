const CACHE_NAME = 'attendance-monitoring-v1';
const urlsToCache = [
  '/attendance-monitoring/',
  '/attendance-monitoring/Index.php',
  '/attendance-monitoring/login.php',
  '/attendance-monitoring/assets/css/style.css',
  '/attendance-monitoring/assets/pincode.css',
  '/attendance-monitoring/assets/logo.png',
  '/attendance-monitoring/manifest.json',
  '/attendance-monitoring/assets/icons/icon-72x72.png',
  '/attendance-monitoring/assets/icons/icon-96x96.png',
  '/attendance-monitoring/assets/icons/icon-128x128.png',
  '/attendance-monitoring/assets/icons/icon-144x144.png',
  '/attendance-monitoring/assets/icons/icon-152x152.png',
  '/attendance-monitoring/assets/icons/icon-192x192.png',
  '/attendance-monitoring/assets/icons/icon-384x384.png',
  '/attendance-monitoring/assets/icons/icon-512x512.png',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
];

// Install event - cache all static assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Fetch event - serve from cache, fall back to network
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Cache hit - return response
        if (response) {
          return response;
        }

        // Clone the request
        const fetchRequest = event.request.clone();

        return fetch(fetchRequest).then(
          response => {
            // Check if we received a valid response
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // Clone the response
            const responseToCache = response.clone();

            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
              });

            return response;
          }
        ).catch(() => {
          // If both cache and network fail, show offline page
          if (event.request.mode === 'navigate') {
            return caches.match('/attendance-monitoring/offline.php');
          }
        });
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
}); 