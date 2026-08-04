/**
 * Future offline order / background sync architecture stub (Module 8).
 * Queue API mutations for later flush when online — not used for live orders yet.
 */
const DB_NAME = 'ctc-pwa-sync';
const STORE = 'requests';

function openDb() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(DB_NAME, 1);
        req.onupgradeneeded = () => {
            const db = req.result;
            if (!db.objectStoreNames.contains(STORE)) {
                db.createObjectStore(STORE, {keyPath: 'id', autoIncrement: true});
            }
        };
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

export async function enqueue(requestPayload) {
    const db = await openDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE, 'readwrite');
        tx.objectStore(STORE).add({
            ...requestPayload,
            created_at: Date.now(),
        });
        tx.oncomplete = () => resolve(true);
        tx.onerror = () => reject(tx.error);
    });
}

export async function flush() {
    // Intentionally empty — Module 8 prepares architecture only.
    return {flushed: 0, pending: true};
}

export function onOnline(callback) {
    window.addEventListener('online', callback);
    return () => window.removeEventListener('online', callback);
}
