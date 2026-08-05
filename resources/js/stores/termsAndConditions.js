import axios from 'axios'
import {defineStore} from "pinia";

export const useTermsAndConditionsStore = defineStore('termsAndConditions', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get('admin/system-setting/terms-and-conditions').then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.put(`/admin/system-setting/terms-and-conditions`, payload).then(res => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
