import axios from "axios";
import appService from "../services/appService.js";
import {defineStore} from "pinia";


export const useFrontendPaymentGatewayStore = defineStore('frontendPaymentGateway', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "frontend/payment-gateway";
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
            })
        }
    }
})
