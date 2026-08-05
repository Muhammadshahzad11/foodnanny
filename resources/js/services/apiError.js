/**
 * Prefer API message / first validation error over a generic fallback.
 */
export function apiErrorMessage(err, fallback = 'Request failed. Please try again.') {
    const data = err?.response?.data;
    const status = err?.response?.status;

    if (data) {
        if (typeof data.message === 'string' && data.message.trim() !== '') {
            const msg = data.message.trim();
            // Prefer first field error when Laravel returns the generic validation title
            if (msg !== 'The given data was invalid.' && msg !== 'The given data was invalid') {
                return msg;
            }
        }

        if (data.errors && typeof data.errors === 'object') {
            const first = Object.values(data.errors).flat().find((v) => typeof v === 'string' && v.trim());
            if (first) return first;
        }

        if (typeof data.message === 'string' && data.message.trim()) {
            return data.message.trim();
        }
    }

    if (status === 401) return 'Your session expired. Please log in again.';
    if (status === 403) return 'You do not have permission for this action.';
    if (status === 404) return 'The requested item was not found.';
    if (status === 419) return 'Page expired. Refresh and try again.';
    if (status === 429) return 'Too many requests. Please wait a few seconds and try again.';
    if (status === 422) return 'Please check the form and try again.';
    if (status >= 500) return 'Server error. Please try again in a moment.';
    if (err?.message && !String(err.message).startsWith('Request failed with status')) {
        return err.message;
    }
    if (!err?.response) return 'Network error. Check your connection and try again.';

    return fallback;
}
