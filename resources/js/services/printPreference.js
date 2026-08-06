/**
 * POS / Waiter print preferences (local to this browser / workstation).
 *
 * Print Preview ON  → browser print dialog (current Chrome behavior)
 * Print Preview OFF → direct print only when Silent Print mode is active
 *                     (Chrome launched with --kiosk-printing + ?silent_print=1)
 */

const PREVIEW_KEY = 'fn_print_preview';
const SILENT_KEY = 'fn_silent_print_ready';

export function syncSilentPrintFromUrl() {
    try {
        const params = new URLSearchParams(window.location.search || '');
        if (params.get('silent_print') === '1') {
            localStorage.setItem(SILENT_KEY, '1');
            // Clean URL without reload
            params.delete('silent_print');
            const qs = params.toString();
            const next = window.location.pathname + (qs ? `?${qs}` : '') + (window.location.hash || '');
            window.history.replaceState({}, '', next);
        }
    } catch (e) {
        // ignore
    }
}

export function isSilentPrintReady() {
    try {
        return localStorage.getItem(SILENT_KEY) === '1';
    } catch (e) {
        return false;
    }
}

export function setSilentPrintReady(ready) {
    try {
        if (ready) {
            localStorage.setItem(SILENT_KEY, '1');
        } else {
            localStorage.removeItem(SILENT_KEY);
        }
    } catch (e) {
        // ignore
    }
}

/** Default ON so first-time users can still print via the dialog. */
export function isPrintPreviewOn() {
    try {
        const v = localStorage.getItem(PREVIEW_KEY);
        if (v === null || v === undefined || v === '') {
            return true;
        }
        return v !== '0';
    } catch (e) {
        return true;
    }
}

export function setPrintPreviewOn(on) {
    try {
        localStorage.setItem(PREVIEW_KEY, on ? '1' : '0');
    } catch (e) {
        // ignore
    }
    try {
        window.dispatchEvent(new CustomEvent('fn-print-preview-changed', {detail: {on: !!on}}));
    } catch (e) {
        // ignore
    }
}

export function canDirectPrint() {
    return isSilentPrintReady();
}

export default {
    syncSilentPrintFromUrl,
    isSilentPrintReady,
    setSilentPrintReady,
    isPrintPreviewOn,
    setPrintPreviewOn,
    canDirectPrint,
};
