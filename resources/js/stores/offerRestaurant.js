import axios from "axios";
import { defineStore } from "pinia";
import appService from "../services/appService.js";

export const useOfferRestaurantStore = defineStore('offerRestaurant', { 
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_id: null
        }
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/offer/restaurant/${payload.id}`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.lists = res.data.data;
                        this.pagination = res.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.page = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total
                            };
                        }
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `/admin/offer/restaurant/${payload.id}`;
                if (this.temp.isEditing) {
                    url = `/admin/offer/restaurant/${payload.id}/${this.temp.temp_id}`;
                }
                axios.post(url, payload.form).then((res) => {
                    this.fetch(payload.search).then().catch();
                    this.reset();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        verify: function (payload) {
            this.temp.temp_id = payload;
        },
        verifyRestaurant: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/offer/restaurant/verify/${payload.offerId}/${payload.id}`, payload).then((res) => {
                    this.fetch(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        destroy: function (payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/offer/restaurant/${payload.offer}/${payload.id}`).then((res) => {
                    this.fetch(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        reset: function () {
            this.temp.temp_id = null;
            this.temp.isEditing = false;
        }
    }
})
