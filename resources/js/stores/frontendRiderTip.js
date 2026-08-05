import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useFrontendRiderTipStore = defineStore('frontendRiderTip', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "frontend/rider-tip";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
