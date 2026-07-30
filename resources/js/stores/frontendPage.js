import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useFrontendPageStore = defineStore('frontendPage', {
    state: () => ({
        lists: [],
        show: {},
        pageInfo: {}
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "frontend/page";
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
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/page/show/${payload}`).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        info: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/page/page-info/${payload}`).then((res) => {
                    this.pageInfo = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
    }
})
