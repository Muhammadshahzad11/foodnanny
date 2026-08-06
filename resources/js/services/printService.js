/**
 * Browser print orchestration for POS / kitchen tickets.
 * Supports sequential prints (receipt then KOT) without CSS collisions.
 *
 * Respects Print Preview preference:
 * - Preview ON  → window.print() (browser dialog)
 * - Preview OFF → direct print only if Silent Print is ready; otherwise throws (toast)
 */
import {
    canDirectPrint,
    isPrintPreviewOn,
    syncSilentPrintFromUrl,
} from './printPreference.js';

const PRINT_MODES = {
    RECEIPT: 'printing-receipt',
    KOT: 'printing-kot',
};

export class PrintUnavailableError extends Error {
    constructor(message) {
        super(message);
        this.name = 'PrintUnavailableError';
        this.code = 'PRINT_UNAVAILABLE';
    }
}

function clearPrintModes() {
    document.body.classList.remove(PRINT_MODES.RECEIPT, PRINT_MODES.KOT);
}

/**
 * Wait until the browser finishes the print dialog (or a safe timeout).
 */
export function waitForAfterPrint(timeoutMs = 120000) {
    return new Promise((resolve) => {
        let settled = false;
        const finish = () => {
            if (settled) return;
            settled = true;
            window.removeEventListener('afterprint', onAfterPrint);
            clearTimeout(timer);
            resolve();
        };
        const onAfterPrint = () => finish();
        const timer = setTimeout(finish, timeoutMs);
        window.addEventListener('afterprint', onAfterPrint);
        const media = window.matchMedia('print');
        if (typeof media.addEventListener === 'function') {
            const onChange = (e) => {
                if (!e.matches) {
                    media.removeEventListener('change', onChange);
                    setTimeout(finish, 150);
                }
            };
            media.addEventListener('change', onChange);
        }
    });
}

/**
 * Gate printing based on Print Preview toggle.
 * When preview is OFF, never open the browser dialog unless silent print is active.
 */
export function assertPrintAllowed() {
    syncSilentPrintFromUrl();
    if (isPrintPreviewOn()) {
        return {mode: 'preview'};
    }
    if (canDirectPrint()) {
        return {mode: 'direct'};
    }
    throw new PrintUnavailableError(
        'Printer not connected for direct print. Turn Print Preview ON, or open POS with Silent Print (kiosk printing).'
    );
}

/**
 * Print the current page in a named mode (adds body class for @media print CSS).
 * @param {'printing-receipt'|'printing-kot'} mode
 * @param {{timeoutMs?: number, forcePreview?: boolean}} options
 */
export async function printInMode(mode, options = {}) {
    if (!options.forcePreview) {
        assertPrintAllowed();
    }

    clearPrintModes();
    document.body.classList.add(mode);
    await new Promise((r) => requestAnimationFrame(() => requestAnimationFrame(r)));

    // Direct (silent) print settles faster; preview may wait on the dialog.
    const preview = isPrintPreviewOn() || options.forcePreview;
    const wait = waitForAfterPrint(options.timeoutMs ?? (preview ? 120000 : 8000));
    try {
        window.print();
    } catch (err) {
        clearPrintModes();
        throw new PrintUnavailableError(
            err?.message || 'Printer not connected. Please connect a printer and try again.'
        );
    }
    await wait;
    clearPrintModes();
}

export async function printReceipt(options = {}) {
    return printInMode(PRINT_MODES.RECEIPT, options);
}

export async function printKot(options = {}) {
    return printInMode(PRINT_MODES.KOT, options);
}

export {PRINT_MODES, clearPrintModes};

export default {
    printReceipt,
    printKot,
    printInMode,
    waitForAfterPrint,
    assertPrintAllowed,
    PrintUnavailableError,
    PRINT_MODES,
    clearPrintModes,
};
