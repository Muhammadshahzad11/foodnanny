import axios from "axios";
import {defineStore} from "pinia";

export const useFrontendCouponStore = defineStore('frontendCoupon', {
    state: () => ({
        lists: [],
        show: {}
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/coupon/${payload}`).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.lists = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/coupon/${payload}/show`).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.show = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        checking: function (payload) {
            if (payload) {
                return new Promise((resolve, reject) => {
                    axios.post(`frontend/coupon/${payload.restaurant_id}/coupon-checking`, payload).then((res) => {
                        resolve(res);
                    }).catch((err) => {
                        reject(err);
                    })
                })
            }
        }
    }
})
