import {createApp} from 'vue';
import DefaultComponent from "./components/DefaultComponent.vue";
import router from "./router/index.js";
import i18n from "./i18n.js";
import {createPinia} from "pinia";
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import ENV from "./config/env.js";
import axios from 'axios';
import VueSimpleAlert from "vue3-simple-alert";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import VueAwesomePaginate from "vue-awesome-paginate";
import "vue-awesome-paginate/dist/style.css"
import VueNextSelect from 'vue-next-select';
import 'vue-next-select/dist/index.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import "@vuepic/vue-datepicker/dist/main.css";
import 'swiper/css';
import 'swiper/css/bundle';
import './echo.js';
import VueApexCharts from "vue3-apexcharts";

// Capture PWA install prompt before Vue mounts (browser may fire early)
window.__ctcPwa = window.__ctcPwa || { deferredPrompt: null, installed: false };
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    window.__ctcPwa.deferredPrompt = e;
    window.dispatchEvent(new CustomEvent('ctc-pwa-prompt-ready'));
});

/* Start axios code*/
const API_URL = ENV.API_URL;
const API_KEY = ENV.API_KEY;

axios.defaults.baseURL = API_URL + '/api';
axios.interceptors.request.use(
    config => {
        config.headers['x-api-key'] = API_KEY;
        if (localStorage.getItem('auth')) {
            const auth                      = JSON.parse(localStorage.getItem('auth'));
            const token                     = auth.token;
            config.headers['Authorization'] = token ? `Bearer ${token}` : '';
        }

        if (localStorage.getItem('common')) {
            const common                     = JSON.parse(localStorage.getItem('common'));
            config.headers['x-localization'] = common.language_code;
        }
        return config;
    }, error => Promise.reject(error),
);
/* End axios code */

const pinia = createPinia().use(piniaPluginPersistedstate);
const app   = createApp(DefaultComponent);
app.component('vue-select', VueNextSelect)
app.use(router)
app.use(VueAwesomePaginate)
app.use(i18n)
app.use(pinia)
app.use(VueSimpleAlert)
app.use(VueApexCharts);
app.use(Toast, {
    position: "top-right",
    timeout: 30000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false,
    containerClassName: "app-toast-container",
})
app.mount('#app');
