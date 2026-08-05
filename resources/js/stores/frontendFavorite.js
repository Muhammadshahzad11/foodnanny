import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";


export const useFrontendFavoriteStore = defineStore('frontendFavorite', {
    state: () => ({
        lists: [],
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "frontend/favorite";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, payload).then((res) => {
                    resolve(res);
                    this.lists = res.data.data;
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        toggle: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post("frontend/favorite/toggle", payload).then((res) => {
                    this.fetch().then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
