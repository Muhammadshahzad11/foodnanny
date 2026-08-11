/**
 * Ask all service workers to drop CTC/PWA caches after a server cache flush.
 */
export async function clearClientPwaCaches(version = null) {
    if (typeof window === 'undefined') return;
    try {
        if ('serviceWorker' in navigator) {
            const regs = await navigator.serviceWorker.getRegistrations();
            for (const reg of regs) {
                const worker = reg.active || reg.waiting || reg.installing;
                if (worker) {
                    worker.postMessage({type: 'CLEAR_CACHES', version});
                    if (version) {
                        worker.postMessage({type: 'SET_CACHE_VERSION', version});
                    }
                    reg.update().catch(() => {});
                }
            }
        }
        if (window.caches?.keys) {
            const keys = await caches.keys();
            await Promise.all(
                keys
                    .filter((k) => k.startsWith('pwa-') || k.startsWith('ctc-'))
                    .map((k) => caches.delete(k))
            );
        }
    } catch (e) {
        // non-fatal
    }
}
