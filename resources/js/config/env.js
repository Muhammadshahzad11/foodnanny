const ENV = {
    API_URL: import.meta.env.VITE_HOST,
    API_KEY: import.meta.env.VITE_API_KEY,
    GOOGLE_MAP_KEY: import.meta.env.VITE_GOOGLE_MAP_KEY,
    PUSHER_KEY: import.meta.env.VITE_PUSHER_APP_KEY,
    PUSHER_CLUSTER :import.meta.env.VITE_PUSHER_APP_CLUSTER,
    TIMEZONE: import.meta.env.VITE_TIMEZONE,
    DEMO: import.meta.env.VITE_DEMO,
};
export default ENV;
