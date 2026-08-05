import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useFrontendRestaurantStore = defineStore('frontendRestaurant', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        showById: {},
        favoriteRestaurants: [],
        favoriteRestaurantPage: {},
        favoriteRestaurantPagination: [],
    }),
    actions: {
        fetch: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = "frontend/restaurant/restaurant-by-lat-long-radius";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.lists      = res.data.data;
                        this.pagination = res.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.page = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total,
                            }
                        }
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/restaurant/show/${payload.slug}`
                if (payload) {
                    url = url + appService.requestHandler(payload.search);
                }

                axios.get(url).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        viewById: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/restaurant/show-by-id/${payload.id}`
                if (payload) {
                    url = url + appService.requestHandler(payload.search);
                }

                axios.get(url).then((res) => {
                    this.showById = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchFavoriteRestaurants: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = `frontend/restaurant/favorite`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, payload).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.favoriteRestaurants          = res.data.data;
                        this.favoriteRestaurantPagination = res.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.favoriteRestaurantPage = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total
                            }
                        }
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
