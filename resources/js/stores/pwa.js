import axios from 'axios'
import { defineStore } from "pinia";

export const usePwaStore = defineStore('pwa', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios
                    .get("admin/system-setting/pwa")
                    .then((res) => {
                        this.lists = res.data.data;
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let url = "/admin/system-setting/pwa";
                method(url, payload.form)
                    .then((res) => {
                        this.lists = res.data.data;
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
    },
})