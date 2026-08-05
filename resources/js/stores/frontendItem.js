import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useFrontendItemStore = defineStore('frontendItem', {
    state: () => ({
        lists: [],
        featured: [],
        popular: {},
        searchItems: [],
        show: {}
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'frontend/item';
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
        },
        fetchFeatured: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "frontend/item/featured-items";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.featured = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchSearchItems: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/item/search-items/${payload.slug}`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.searchItems = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/item/show/${payload}`).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.show = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
