import {reactive, computed} from 'vue';
import alertService from '../services/alertService.js';

const STORAGE_LATER = 'ctc_pwa_later_until';
const STORAGE_NEVER = 'ctc_pwa_never_install';
const STORAGE_INSTALLED = 'ctc_pwa_installed';
const DEFAULT_ICON = '/images/default/pwa/icons/icon-192x192.png';

const state = reactive({
    ready: false,
    config: null,
    deferredPrompt: null,
    showPopup: false,
    installed: false,
    justInstalled: false,
    isIos: false,
    isAndroid: false,
    isDesktop: false,
    canPrompt: false,
    showManualHelp: false,
    installing: false,
});

let popupTimer = null;
let listenersBound = false;

function isStandalone() {
    return window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true
        || document.referrer.includes('android-app://');
}

function isIosDevice() {
    return /iphone|ipad|ipod/i.test(window.navigator.userAgent)
        || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
}

function isAndroidDevice() {
    return /android/i.test(window.navigator.userAgent);
}

function readNever() {
    return localStorage.getItem(STORAGE_NEVER) === '1';
}

function readLaterUntil() {
    const raw = localStorage.getItem(STORAGE_LATER);
    return raw ? Number(raw) : 0;
}

function syncDeferredFromWindow() {
    const early = window.__ctcPwa?.deferredPrompt || null;
    if (early) {
        state.deferredPrompt = early;
        state.canPrompt = true;
        state.showManualHelp = false;
    }
}

async function ensureServiceWorker() {
    if (!('serviceWorker' in navigator)) return null;
    try {
        const existing = await navigator.serviceWorker.getRegistration('/');
        if (existing) {
            await existing.update().catch(() => {});
            return existing;
        }
        return await navigator.serviceWorker.register('/serviceworker.js', {
            scope: '/',
            updateViaCache: 'none',
        });
    } catch (e) {
        return null;
    }
}

function waitForPrompt(timeoutMs = 2500) {
    return new Promise((resolve) => {
        syncDeferredFromWindow();
        if (state.deferredPrompt || window.__ctcPwa?.deferredPrompt) {
            resolve(state.deferredPrompt || window.__ctcPwa.deferredPrompt);
            return;
        }

        const onReady = () => {
            cleanup();
            syncDeferredFromWindow();
            resolve(state.deferredPrompt || window.__ctcPwa?.deferredPrompt || null);
        };
        const onBip = (e) => {
            e.preventDefault();
            state.deferredPrompt = e;
            state.canPrompt = true;
            if (window.__ctcPwa) window.__ctcPwa.deferredPrompt = e;
            cleanup();
            resolve(e);
        };
        const timer = window.setTimeout(() => {
            cleanup();
            resolve(null);
        }, timeoutMs);

        function cleanup() {
            window.clearTimeout(timer);
            window.removeEventListener('ctc-pwa-prompt-ready', onReady);
            window.removeEventListener('beforeinstallprompt', onBip);
        }

        window.addEventListener('ctc-pwa-prompt-ready', onReady, {once: true});
        window.addEventListener('beforeinstallprompt', onBip, {once: true});
    });
}

async function loadConfig() {
    try {
        const res = await fetch('/pwa/install-config', {
            headers: {Accept: 'application/json'},
            credentials: 'same-origin',
        });
        if (!res.ok) throw new Error('config failed');
        const json = await res.json();
        state.config = json?.data || json || null;
    } catch (e) {
        state.config = {
            name: 'Cost to Cost Foods',
            short_name: 'CTC Foods',
            description: 'Install for faster access to orders, tables, kitchen, and POS.',
            theme_color: '#148A3C',
            icon: DEFAULT_ICON,
            enable_install_popup: true,
            popup_delay_seconds: 2,
            popup_frequency_hours: 24,
            auto_update: true,
            cache_version: 1,
        };
    }
    state.config.icon = DEFAULT_ICON;
}

function schedulePopup() {
    if (!state.config?.enable_install_popup) return;
    if (state.installed || readNever()) return;
    if (Date.now() < readLaterUntil()) return;

    if (popupTimer) window.clearTimeout(popupTimer);
    const delay = Math.max(0, Number(state.config.popup_delay_seconds ?? 2)) * 1000;
    popupTimer = window.setTimeout(() => {
        if (!state.installed && !readNever() && Date.now() >= readLaterUntil()) {
            state.showPopup = true;
        }
    }, delay);
}

function bindListeners() {
    if (listenersBound) return;
    listenersBound = true;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        state.deferredPrompt = e;
        state.canPrompt = true;
        state.showManualHelp = false;
        if (window.__ctcPwa) window.__ctcPwa.deferredPrompt = e;
        schedulePopup();
    });

    window.addEventListener('ctc-pwa-prompt-ready', () => {
        syncDeferredFromWindow();
        schedulePopup();
    });

    window.addEventListener('appinstalled', onInstalled);
    window.addEventListener('ctc-pwa-installed', onInstalled);
}

function onInstalled() {
    state.installed = true;
    state.justInstalled = true;
    state.showPopup = false;
    state.showManualHelp = false;
    state.canPrompt = false;
    state.deferredPrompt = null;
    state.installing = false;
    if (window.__ctcPwa) window.__ctcPwa.deferredPrompt = null;
    localStorage.setItem(STORAGE_INSTALLED, '1');
    try {
        alertService.success('Installed successfully');
    } catch (e) {}
    window.setTimeout(() => {
        state.justInstalled = false;
    }, 4000);
}

export function usePwaInstall() {
    const canInstall = computed(() => state.ready && !state.installed);

    async function init() {
        state.isIos = isIosDevice();
        state.isAndroid = isAndroidDevice();
        state.isDesktop = !state.isIos && !state.isAndroid;
        state.installed = isStandalone();
        if (!state.installed) {
            localStorage.removeItem(STORAGE_INSTALLED);
        }

        syncDeferredFromWindow();

        if (!state.config) {
            await loadConfig();
        }

        bindListeners();
        await ensureServiceWorker();

        window.setTimeout(() => syncDeferredFromWindow(), 1000);
        window.setTimeout(() => syncDeferredFromWindow(), 3000);

        if (!state.installed) {
            schedulePopup();
        }

        state.ready = true;
        return state;
    }

    async function promptInstall() {
        if (state.installing) return null;
        state.installing = true;

        try {
            syncDeferredFromWindow();
            let promptEvent = state.deferredPrompt || window.__ctcPwa?.deferredPrompt || null;

            // Make sure SW is ready — Chrome often waits for this before offering install
            await ensureServiceWorker();
            if (!promptEvent) {
                promptEvent = await waitForPrompt(3000);
            }

            if (promptEvent && typeof promptEvent.prompt === 'function') {
                await promptEvent.prompt();
                const choice = await promptEvent.userChoice;
                state.deferredPrompt = null;
                state.canPrompt = false;
                if (window.__ctcPwa) window.__ctcPwa.deferredPrompt = null;
                state.showPopup = false;
                state.showManualHelp = false;
                if (choice?.outcome === 'accepted') {
                    onInstalled();
                } else {
                    try {
                        alertService.info('Install cancelled. You can try again anytime.');
                    } catch (e) {}
                }
                return choice;
            }

            // Native prompt unavailable (Safari, dismissed criteria, etc.)
            state.showManualHelp = true;
            state.showPopup = true;

            if (state.isIos) {
                alertService.info('On iPhone/iPad: tap Share → Add to Home Screen.');
            } else if (state.isAndroid) {
                alertService.info('Tap the browser menu (⋮) → Install app / Add to Home screen.');
            } else {
                alertService.info(
                    'Chrome has not enabled install yet for this site. Use Chrome/Edge, open this exact URL, then DevTools → Application → Clear site data → reload. Menu → Install App appears only after Chrome accepts the PWA.'
                );
            }
            return null;
        } catch (e) {
            state.showManualHelp = true;
            state.showPopup = true;
            try {
                alertService.error('Could not open the install dialog. Use Chrome/Edge and try the install icon in the address bar.');
            } catch (err) {}
            return null;
        } finally {
            state.installing = false;
        }
    }

    function openInstallUi() {
        if (state.installed) return;
        syncDeferredFromWindow();
        state.showPopup = true;
    }

    function maybeLater() {
        const hours = Number(state.config?.popup_frequency_hours || 24);
        localStorage.setItem(STORAGE_LATER, String(Date.now() + hours * 60 * 60 * 1000));
        state.showPopup = false;
        state.showManualHelp = false;
    }

    function neverAgain() {
        localStorage.setItem(STORAGE_NEVER, '1');
        state.showPopup = false;
        state.showManualHelp = false;
    }

    function closePopup() {
        maybeLater();
    }

    return {
        state,
        canInstall,
        init,
        promptInstall,
        openInstallUi,
        maybeLater,
        neverAgain,
        closePopup,
    };
}
