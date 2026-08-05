import axios from "axios";
import {defineStore} from "pinia";

export const useCountryCodeStore = defineStore('countryCode', {
    state: () => ({
        lists: [],
        show: {},
        find: {}
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/country-code").then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/country-code/show/${payload}`;
                axios.get(url).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            })
        },
        fetchFind: function(payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/country-code/find";
                axios.post(url, payload).then(res => {
                    this.find = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            })
        }
    }
})
