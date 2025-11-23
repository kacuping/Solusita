const CACHE_NAME = 'solusita-cache-v3';
const URLS_TO_CACHE = [
  '/customer/home',
  '/manifest.webmanifest',
  '/icons/pic.png'
];

self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(URLS_TO_CACHE))
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil((async () => {
    const keys = await caches.keys();
    await Promise.all(keys.map((k) => { if (k !== CACHE_NAME) return caches.delete(k); }));
    await self.clients.claim();
  })());
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  const accept = req.headers.get('accept') || '';
  const isHTML = req.mode === 'navigate' || accept.includes('text/html');

  if (req.method && req.method !== 'GET') {
    event.respondWith(fetch(req));
    return;
  }

  try {
    const url = new URL(req.url);
    if (url.pathname === '/service-worker.js') {
      event.respondWith(fetch(req, { cache: 'no-store' }));
      return;
    }
  } catch (e) { }

  if (isHTML) {
    event.respondWith((async () => {
      try {
        const netResp = await fetch(req, { cache: 'no-store' });
        const cache = await caches.open(CACHE_NAME);
        cache.put(req, netResp.clone());
        return netResp;
      } catch (e) {
        const cached = await caches.match(req);
        if (cached) return cached;
        const fallback = await caches.match('/customer/home');
        return fallback || Response.error();
      }
    })());
    return;
  }

  event.respondWith((async () => {
    const cached = await caches.match(req);
    const fetchPromise = fetch(req).then((resp) => {
      const copy = resp.clone();
      caches.open(CACHE_NAME).then((cache) => cache.put(req, copy));
      return resp;
    }).catch(() => cached);
    return cached || fetchPromise;
  })());
});

self.addEventListener('push', function (event) {
  let data = {};
  try { data = event.data ? event.data.json() : {}; } catch (e) { data = {}; }
  const title = data.title || '';
  const body = data.body || 'Ada pembaruan';
  const url = data.url || '/customer/home';
  const icon = data.icon || '/icons/solusita_notif.png';
  event.waitUntil(self.registration.showNotification(title, { body, icon, data: { url } }));
});

self.addEventListener('notificationclick', function (event) {
  const url = (event.notification && event.notification.data && event.notification.data.url) || '/customer/home';
  event.notification.close();
  event.waitUntil(clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (list) {
    for (let i = 0; i < list.length; i++) { const c = list[i]; if (c.url && c.url.includes(url)) { c.focus(); return; } }
    if (clients.openWindow) return clients.openWindow(url);
  }));
});
