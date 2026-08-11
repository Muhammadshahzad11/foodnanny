/* Cost to Cost Foods — Module 8 PWA service worker (extends existing laravelpwa SW) */
const SHELL_PREFIX = 'ctc-shell-';
const RUNTIME_PREFIX = 'ctc-runtime-';
const IMAGE_PREFIX = 'ctc-images-';
const FONT_PREFIX = 'ctc-fonts-';

const PRECACHE_URLS = [
    '/offline',
    '/manifest.json',
    '/images/default/pwa/icons/icon-72x72.png',
    '/images/default/pwa/icons/icon-96x96.png',
    '/images/default/pwa/icons/icon-128x128.png',
    '/images/default/pwa/icons/icon-144x144.png',
    '/images/default/pwa/icons/icon-152x152.png',
    '/images/default/pwa/icons/icon-192x192.png',
    '/images/default/pwa/icons/icon-384x384.png',
    '/images/default/pwa/icons/icon-512x512.png',
];

let activeVersion = 'v1';

function shellCache() { return SHELL_PREFIX + activeVersion; }
function runtimeCache() { return RUNTIME_PREFIX + activeVersion; }
function imageCache() { return IMAGE_PREFIX + activeVersion; }
function fontCache() { return FONT_PREFIX + activeVersion; }

async function precacheShell() {
    const cache = await caches.open(shellCache());
    await Promise.all(PRECACHE_URLS.map(async (url) => {
        try {
            await cache.add(new Request(url, { cache: 'reload' }));
        } catch (e) {}
    }));
}

self.addEventListener('install', (event) => {
    event.waitUntil((async () => {
        await precacheShell();
        await self.skipWaiting();
    })());
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const keep = new Set([shellCache(), runtimeCache(), imageCache(), fontCache()]);
        const keys = await caches.keys();
        await Promise.all(keys.map((key) => {
            const isOurs = key.startsWith('pwa-')
                || key.startsWith(SHELL_PREFIX)
                || key.startsWith(RUNTIME_PREFIX)
                || key.startsWith(IMAGE_PREFIX)
                || key.startsWith(FONT_PREFIX);
            if (isOurs && !keep.has(key)) {
                return caches.delete(key);
            }
            return null;
        }));
        await self.clients.claim();
    })());
});

self.addEventListener('message', (event) => {
    const data = event.data || {};
    if (data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    if (data.type === 'SET_CACHE_VERSION' && data.version) {
        activeVersion = 'v' + String(data.version);
    }
    if (data.type === 'CLEAR_CACHES') {
        // Only clear our versioned PWA caches — never wipe everything
        // (wiping /build assets caused blank white admin screens).
        event.waitUntil((async () => {
            const keys = await caches.keys();
            await Promise.all(keys.map((key) => {
                const isOurs = key.startsWith('pwa-')
                    || key.startsWith(SHELL_PREFIX)
                    || key.startsWith(RUNTIME_PREFIX)
                    || key.startsWith(IMAGE_PREFIX)
                    || key.startsWith(FONT_PREFIX);
                return isOurs ? caches.delete(key) : null;
            }));
            if (data.version) {
                activeVersion = 'v' + String(data.version);
            }
            await precacheShell();
        })());
    }
});

function isApiRequest(url) {
    return url.pathname.startsWith('/api/') || url.pathname.includes('/broadcasting/');
}

function isAssetRequest(url) {
    return url.pathname.startsWith('/build/')
        || url.pathname.startsWith('/themes/')
        || url.pathname.startsWith('/images/')
        || url.pathname.startsWith('/storage/')
        || /\.(?:js|css|woff2?|ttf|eot|svg|png|jpe?g|webp|gif|ico)(?:$|\?)/i.test(url.pathname);
}

function isFontRequest(url) {
    return /\.(?:woff2?|ttf|eot)(?:$|\?)/i.test(url.pathname) || url.pathname.includes('/fonts/');
}

function isImageRequest(url) {
    return /\.(?:png|jpe?g|webp|gif|svg|ico)(?:$|\?)/i.test(url.pathname) || url.pathname.startsWith('/storage/');
}

async function networkFirst(request, cacheName, fallbackUrl) {
    try {
        const fresh = await fetch(request);
        if (fresh && fresh.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, fresh.clone());
        }
        return fresh;
    } catch (e) {
        const cached = await caches.match(request);
        if (cached) return cached;
        if (fallbackUrl) {
            const offline = await caches.match(fallbackUrl);
            if (offline) return offline;
        }
        throw e;
    }
}

async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) return cached;
    const fresh = await fetch(request);
    if (fresh && fresh.ok) {
        const cache = await caches.open(cacheName);
        cache.put(request, fresh.clone());
    }
    return fresh;
}

async function staleWhileRevalidate(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    const networkPromise = fetch(request).then((fresh) => {
        if (fresh && fresh.ok) {
            cache.put(request, fresh.clone());
        }
        return fresh;
    }).catch(() => cached);
    return cached || networkPromise;
}

self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET') return;

    let url;
    try {
        url = new URL(request.url);
    } catch (e) {
        return;
    }
    if (url.origin !== self.location.origin) return;
    if (isApiRequest(url)) return;

    if (request.mode === 'navigate') {
        event.respondWith(networkFirst(request, runtimeCache(), '/offline'));
        return;
    }

    if (url.pathname === '/offline' || url.pathname === '/manifest.json') {
        event.respondWith(staleWhileRevalidate(request, shellCache()));
        return;
    }

    if (isFontRequest(url)) {
        event.respondWith(cacheFirst(request, fontCache()));
        return;
    }

    if (isImageRequest(url)) {
        event.respondWith(staleWhileRevalidate(request, imageCache()));
        return;
    }

    // Vite hashed /build assets: prefer network so deploys are not stuck on stale JS/CSS
    if (isAssetRequest(url)) {
        event.respondWith(staleWhileRevalidate(request, runtimeCache()));
    }
});
