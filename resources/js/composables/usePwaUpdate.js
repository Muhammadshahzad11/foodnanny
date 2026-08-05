import {reactive} from 'vue';
import axios from 'axios';

const state = reactive({
    updateReady: false,
    registration: null,
    autoUpdate: true,
});

export function usePwaUpdate() {
    async function init(options = {}) {
        if (!('serviceWorker' in navigator)) return state;
        state.autoUpdate = options.autoUpdate !== false;

        try {
            const res = await axios.get('frontend/pwa/install-config');
            const cfg = res.data?.data || {};
            state.autoUpdate = cfg.auto_update !== false;
        } catch (e) {}

        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (state._reloading) return;
            state._reloading = true;
            window.location.reload();
        });

        const registration = await navigator.serviceWorker.getRegistration();
        if (!registration) return state;
        state.registration = registration;

        if (registration.waiting) {
            onWaiting(registration.waiting);
        }

        registration.addEventListener('updatefound', () => {
            const worker = registration.installing;
            if (!worker) return;
            worker.addEventListener('statechange', () => {
                if (worker.state === 'installed' && navigator.serviceWorker.controller) {
                    onWaiting(worker);
                }
            });
        });

        // Periodic update check
        window.setInterval(() => {
            registration.update().catch(() => {});
        }, 15 * 60 * 1000);

        return state;
    }

    function onWaiting(worker) {
        if (state.autoUpdate) {
            worker.postMessage({type: 'SKIP_WAITING'});
            return;
        }
        state.updateReady = true;
        state.waitingWorker = worker;
    }

    function updateNow() {
        const worker = state.waitingWorker || state.registration?.waiting;
        if (worker) {
            worker.postMessage({type: 'SKIP_WAITING'});
        }
        state.updateReady = false;
    }

    function updateLater() {
        state.updateReady = false;
    }

    return {
        state,
        init,
        updateNow,
        updateLater,
    };
}
