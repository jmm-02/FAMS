const CACHE_NAME = 'attendance-cache-v1';
const urlsToCache = [
  '/attendance-monitoring/index.php',
  '/attendance-monitoring/all.min.css',
  '/attendance-monitoring/logo.png',
  '/attendance-monitoring/fa-solid-900.woff2',
  '/attendance-monitoring/manifest.json',
  '/attendance-monitoring/assets/icons/icon-144x144.png',
  // Add more assets as needed
];

// Install event: cache files
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

// Activate event: cleanup old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames =>
      Promise.all(
        cacheNames.filter(name => name !== CACHE_NAME)
          .map(name => caches.delete(name))
      )
    )
  );
});

// Fetch event: serve cached files if offline
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
}); 