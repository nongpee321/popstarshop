/*
 * PopCentral POS — Service Worker
 * Only cache static assets (CSS, JS, Fonts, Icons)
 * Always allow fresh network requests for POS and APIs
 */

const CACHE_NAME = 'popcentral-pos-v2.0.0';

const STATIC_ASSETS = [
  './manifest.webmanifest',
  './pos-icon.svg',
  './vendor/bootstrap-icons/bootstrap-icons.min.css',
  './vendor/bootstrap-icons/fonts/bootstrap-icons.woff2',
  './vendor/alpinejs/alpine.min.js',
  './vendor/sweetalert2/sweetalert2.all.min.js'
];

self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS)).catch(() => {})
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);

  // Never intercept API calls, dynamic pages, or pos routes
  if (url.pathname.includes('/pos') || url.pathname.includes('/api') || url.pathname.includes('/sanctum')) {
    return;
  }

  // Cache-first for static assets only
  if (url.pathname.endsWith('.css') || url.pathname.endsWith('.woff2') || url.pathname.endsWith('.js') || url.pathname.endsWith('.svg') || url.pathname.endsWith('.png')) {
    event.respondWith(
      caches.match(request).then((cached) => {
        return cached || fetch(request).then((response) => {
          if (response && response.status === 200) {
            const copy = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
          }
          return response;
        });
      })
    );
  }
});
