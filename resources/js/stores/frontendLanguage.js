import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";


export const useFrontendLanguageStore = defineStore('frontendLanguage', {
    persist: true,
    state: () => ({
        lists: [],
        show: {},
    }),
    actions: {
        fetch: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = "frontend/language";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.lists = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            });
        },
        view: function (payload) {
            if (payload) {
                return new Promise((resolve, reject) => {
                    axios.get(`frontend/language/show/${payload}`).then((res) => {
                        this.show = res.data.data;
                        resolve(res);
                    }).catch((err) => {
                        reject(err);
                    });
                })
            }
        }
    }
})
