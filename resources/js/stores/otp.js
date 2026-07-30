import axios from 'axios'
import {defineStore} from "pinia";

export const useOtpStore = defineStore('otp', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get('admin/system-setting/otp').then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.put(`/admin/system-setting/otp`, payload).then(res => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
