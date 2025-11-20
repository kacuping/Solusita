const CACHE_NAME = 'solusita-cache-v1';
const URLS_TO_CACHE = [
  '/',
  '/customer/home',
  '/manifest.webmanifest',
  '/icons/pic.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(URLS_TO_CACHE))
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(keys.map((k) => {
      if (k !== CACHE_NAME) return caches.delete(k);
    })))
  );
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  event.respondWith(
    caches.match(req).then((res) => {
      return res || fetch(req).then((response) => {
        const copy = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(req, copy));
        return response;
      }).catch(() => {
        if (req.mode === 'navigate') {
          return caches.match('/customer/home');
        }
      });
    })
  );
});
