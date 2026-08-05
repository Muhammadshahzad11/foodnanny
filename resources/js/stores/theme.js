import axios from 'axios'
import {defineStore} from "pinia";

export const useThemeStore = defineStore('theme', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get('admin/system-setting/theme').then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post("/admin/system-setting/theme", payload.form,{
                    headers:{
                        "Content-Type": "multipart/form-data"
                    }
                }).then(res => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
