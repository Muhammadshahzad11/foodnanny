import axios from "axios";
import {defineStore} from "pinia";

export const usePermissionStore = defineStore('permission', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/system-setting/permission/${payload}`).then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/system-setting/permission/${payload.id}`, { permissions: payload.form }).then(res => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
