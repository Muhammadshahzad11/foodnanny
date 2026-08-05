import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";


export const useFrontendAboutStepsStore = defineStore('frontendAboutSteps', {
    state: () => ({
        lists: [],
    }),
    actions: {
        fetch: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = "frontend/about-step";
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
                });
            });
        }
    }
})
