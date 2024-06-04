const CACHE_NAME = 'my-site-cache-v1';
const STATIC_FILES = [
    '/', // Ajoutez ici les fichiers statiques non dynamiques
];

self.addEventListener('install', event => {
    event.waitUntil(
        fetch('/index.php?c=tools&a=getListFileToBeCache')
            .then(response => response.json())
            .then(files => {
                const urlsToCache = STATIC_FILES.concat(files);
                return caches.open(CACHE_NAME).then(cache => {
                    return cache.addAll(urlsToCache);
                });
            })
    );
});

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

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            if (response) {
                return response;
            }
            return fetch(event.request);
        })
    );
});
