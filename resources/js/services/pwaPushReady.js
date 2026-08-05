/**
 * Future push notification architecture stub (Module 8).
 * Do not wire Firebase here — existing firebase-messaging-sw.js remains untouched.
 */
export function isPushSupported() {
    return typeof window !== 'undefined'
        && 'Notification' in window
        && 'serviceWorker' in navigator
        && 'PushManager' in window;
}

export async function getPushRegistration() {
    if (!('serviceWorker' in navigator)) return null;
    return navigator.serviceWorker.ready;
}

export async function subscribePush() {
    throw new Error('NOT_IMPLEMENTED: Push provider not configured for Module 8.');
}
