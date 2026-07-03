const CACHE_NAME = 'scholarlink-v1';
const urlsToCache = [
  '/',
  '/index.php',
  '/assets/img/tau_logo.png'
];

// Install Service Worker at i-save ang files sa cache
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

// Gamitin ang cache kung mabagal ang internet
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // I-return ang cache kung meron, kung wala, kumuha sa internet
        return response || fetch(event.request);
      })
  );
});