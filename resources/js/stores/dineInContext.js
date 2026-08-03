import {defineStore} from "pinia";
import axios from "axios";

export const useDineInContextStore = defineStore('dineInContext', {
    state: () => ({
        context: null,
        error: null,
    }),
    persist: true,
    actions: {
        resolveToken: function (token) {
            return new Promise((resolve, reject) => {
                this.error = null;
                axios.get(`frontend/table-qr/resolve/${token}`).then((res) => {
                    this.context = res.data.data?.dine_in_context || {
                        source: 'table_qr',
                        table_uuid: res.data.data?.table_uuid,
                        restaurant_slug: res.data.data?.restaurant_slug,
                        qr_version: res.data.data?.qr_version,
                    };
                    this.context = {
                        ...this.context,
                        table_number: res.data.data?.table_number,
                        table_name: res.data.data?.table_name,
                        restaurant_name: res.data.data?.restaurant_name,
                        menu_path: res.data.data?.menu_path,
                    };
                    resolve(res.data.data);
                }).catch((err) => {
                    this.context = null;
                    this.error = err.response?.data?.message || 'Invalid QR';
                    reject(err);
                });
            });
        },
        clear: function () {
            this.context = null;
            this.error = null;
        }
    }
});
