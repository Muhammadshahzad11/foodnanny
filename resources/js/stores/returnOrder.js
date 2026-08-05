import axios from "axios";
import { defineStore } from "pinia";
import appService from "../services/appService.js";

export const useReturnOrderStore = defineStore('returnOrder', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        orderItems: {},
        orderRestaurant: {},
        orderUser: {},
        orderAddress: {},
        orderDeliveryBoy: {}
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/return-order";
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
                                total: res.data.meta.total,
                            };
                        }
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post('/admin/return-order', payload.form).then((res) => {
                    this.fetch(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/return-order/show/${payload}`).then((res) => {
                    this.show             = res.data.data;
                    this.orderItems       = res.data.data.order_items;
                    this.orderRestaurant  = res.data.data.restaurant;
                    this.orderUser        = res.data.data.user;
                    this.orderAddress     = res.data.data.order_address;
                    this.orderDeliveryBoy = res.data.data.delivery_boy;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        destroy: function (payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/return-order/${payload.id}`).then((res) => {
                        this.fetch(payload.search).then().catch();
                        resolve(res);
                    }).catch((err) => {
                        reject(err);
                    });
            });
        },
        export: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/return-order/export';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, { responseType: 'blob' }).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
