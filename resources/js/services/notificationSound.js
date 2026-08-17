/**
 * Notification sound for POS / admin / kitchen screens.
 *
 * Browsers refuse to play audio until the page has seen a user gesture, so the
 * clip is primed on the first click/keypress. Without that priming the very
 * first new-order alert of a shift stays silent.
 */

const SOUND_URL = '/audio/notification.mp3';
const PREF_KEY = 'fn_notification_sound';

let primed = false;
let listening = false;
let clip = null;

function element() {
    if (clip) return clip;
    try {
        clip = new Audio(SOUND_URL);
        clip.preload = 'auto';
    } catch (e) {
        clip = null;
    }

    return clip;
}

/** Play once muted so later calls are allowed by the autoplay policy. */
function prime() {
    if (primed) return;
    const audio = element();
    if (!audio) return;

    primed = true;
    const wasMuted = audio.muted;
    audio.muted = true;
    const played = audio.play();
    if (played && typeof played.then === 'function') {
        played.then(() => {
            audio.pause();
            audio.currentTime = 0;
            audio.muted = wasMuted;
        }).catch(() => {
            audio.muted = wasMuted;
            primed = false;
        });
    } else {
        audio.muted = wasMuted;
    }
}

export function listenForUserGesture() {
    if (listening || typeof window === 'undefined') return;
    listening = true;

    const onGesture = () => {
        prime();
        if (primed) {
            window.removeEventListener('pointerdown', onGesture);
            window.removeEventListener('keydown', onGesture);
        }
    };

    window.addEventListener('pointerdown', onGesture, {passive: true});
    window.addEventListener('keydown', onGesture);
}

export function isSoundEnabled() {
    try {
        return localStorage.getItem(PREF_KEY) !== '0';
    } catch (e) {
        return true;
    }
}

export function setSoundEnabled(on) {
    try {
        localStorage.setItem(PREF_KEY, on ? '1' : '0');
    } catch (e) {
        // ignore
    }
    if (on) {
        prime();
    }
    try {
        window.dispatchEvent(new CustomEvent('fn-notification-sound-changed', {detail: {on: !!on}}));
    } catch (e) {
        // ignore
    }
}

/**
 * @param {{repeat?: number, gap?: number, volume?: number, force?: boolean}} options
 */
export function playNotificationSound(options = {}) {
    if (!options.force && !isSoundEnabled()) return;

    const audio = element();
    if (!audio) return;

    const repeat = Math.max(1, Number(options.repeat || 1));
    const gap = Number(options.gap ?? 800);
    audio.volume = Math.min(1, Math.max(0, Number(options.volume ?? 0.8)));

    let played = 0;
    const ring = () => {
        played++;
        try {
            audio.currentTime = 0;
            const result = audio.play();
            if (result && typeof result.catch === 'function') {
                result.catch(() => {});
            }
        } catch (e) {
            // autoplay blocked or clip missing
        }
        if (played < repeat) {
            setTimeout(ring, gap);
        }
    };

    ring();
}

/** New order — ring several times so a busy counter still notices. */
export function playOrderAlertSound() {
    playNotificationSound({repeat: 3, gap: 900, volume: 1});
}

export default {
    listenForUserGesture,
    isSoundEnabled,
    setSoundEnabled,
    playNotificationSound,
    playOrderAlertSound,
};
