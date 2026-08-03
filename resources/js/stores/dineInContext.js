import {defineStore} from "pinia";
import axios from "axios";

export const useDineInContextStore = defineStore('dineInContext', {
    state: () => ({
        context: null,
        error: null,
        errorCode: null,
    }),
    persist: true,
    getters: {
        isActive: (state) => !!(state.context?.table_id && state.context?.qr_token && state.context?.restaurant_slug),
        tableLabel: (state) => {
            if (!state.context) return '';
            const number = state.context.table_number || '';
            const name = state.context.table_name || '';
            return name ? `${name} (${number})` : number;
        }
    },
    actions: {
        resolveToken: function (token) {
            return new Promise((resolve, reject) => {
                this.error = null;
                this.errorCode = null;
                axios.get(`frontend/table-qr/resolve/${token}`).then((res) => {
                    const data = res.data.data || {};
                    this.context = {
                        source: 'table_qr',
                        table_id: data.table_id,
                        table_uuid: data.table_uuid,
                        table_number: data.table_number,
                        table_name: data.table_name,
                        zone: data.zone,
                        qr_token: data.qr_token,
                        qr_version: data.qr_version,
                        restaurant_id: data.restaurant_id,
                        restaurant_slug: data.restaurant_slug,
                        restaurant_name: data.restaurant_name,
                        menu_path: data.menu_path,
                    };
                    resolve(data);
                }).catch((err) => {
                    this.context = null;
                    this.error = err.response?.data?.message || 'Invalid QR';
                    this.errorCode = err.response?.status || 404;
                    reject(err);
                });
            });
        },
        matchesRestaurant: function (slugOrId) {
            if (!this.isActive) return false;
            if (typeof slugOrId === 'string') {
                return this.context.restaurant_slug === slugOrId;
            }
            return Number(this.context.restaurant_id) === Number(slugOrId);
        },
        clear: function () {
            this.context = null;
            this.error = null;
            this.errorCode = null;
        }
    }
});
