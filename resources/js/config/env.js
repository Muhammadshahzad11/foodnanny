// Values printed by master.blade.php are read first so a .env change takes
// effect without rebuilding; the build-time values stay as a fallback.
const runtime = typeof window !== 'undefined' ? window : {};

const ENV = {
    API_URL: runtime.apiUrl || import.meta.env.VITE_HOST,
    API_KEY: runtime.apiKey || import.meta.env.VITE_API_KEY,
    GOOGLE_MAP_KEY: runtime.googleMapKey || import.meta.env.VITE_GOOGLE_MAP_KEY,
    PUSHER_KEY: runtime.pusherKey || import.meta.env.VITE_PUSHER_APP_KEY,
    PUSHER_CLUSTER: runtime.pusherCluster || import.meta.env.VITE_PUSHER_APP_CLUSTER,
    TIMEZONE: runtime.timezone || import.meta.env.VITE_TIMEZONE,
    DEMO: import.meta.env.VITE_DEMO,
};
export default ENV;
