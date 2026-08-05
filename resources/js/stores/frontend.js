import axios from 'axios'
import {defineStore} from "pinia";

export const useFrontendStore = defineStore('frontend', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get('admin/system-setting/frontend').then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/system-setting/frontend`, payload.form).then(res => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchTranslations: function (locale) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/system-setting/frontend/translations?locale=${locale}`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        saveTranslations: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post('/admin/system-setting/frontend/translations', payload.form).then(res => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
