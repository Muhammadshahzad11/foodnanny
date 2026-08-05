import axios from 'axios';
import {defineStore} from "pinia";
import appService from "../services/appService.js";


export const useFrontendOrderStore = defineStore("frontendOrder", {
    state: () => ({
        activeOrder: [],
        previousOrder: [],
        show: {},
        orderItems: {},
        orderRestaurant: {},
        orderUser: {},
        orderAddress: {},
        orderDeliveryBoy: {},
        orderCoupon: {},
        page: {},
        pagination: [],
    }),
    actions: {
        fetchActiveOrder: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'frontend/order';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.activeOrder = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchPreviousOrder: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'frontend/order';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.previousOrder = res.data.data;
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
                axios.post("/frontend/order", payload).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/order/show/${payload}`).then((res) => {
                    this.show             = res.data.data;
                    this.orderItems       = res.data.data?.order_items;
                    this.orderRestaurant  = res.data.data?.restaurant;
                    this.orderUser        = res.data.data?.user;
                    this.orderAddress     = res.data.data?.order_address;
                    this.orderDeliveryBoy = res.data.data?.delivery_boy;
                    this.orderCoupon      = res.data.data?.coupon || {};
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        cancel: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`frontend/order/cancel/${payload.id}`, payload).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
