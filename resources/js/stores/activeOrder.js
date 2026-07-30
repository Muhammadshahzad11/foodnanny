import axios from 'axios'
import { defineStore } from "pinia";
import appService from "../services/appService.js";

export const useActiveOrderStore = defineStore('activeOrder', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        orderItems: {},
        orderRestaurant: {},
        orderUser: {},
        orderAddress: {},
        orderDeliveryBoy: {},
        temp: {
            temp_id: null,
            isEditing: false,
        },
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/active-order';
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
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/active-order/show/${payload}`).then((res) => {
                    this.show = res.data.data;
                    this.orderItems = res.data.data.order_items;
                    this.orderRestaurant = res.data.data.restaurant;
                    this.orderUser = res.data.data.user;
                    this.orderAddress = res.data.data.order_address;
                    this.orderDeliveryBoy = res.data.data.delivery_boy;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        changeStatus: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/active-order/change-status/${payload}`).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        receivedStatus: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/active-order/received-status/${payload}`).then(res => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        changePaymentStatus: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/active-order/change-payment-status/${payload}`).then(res => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        reset: function () {
            this.temp.temp_id = null;
            this.temp.isEditing = false;
        },
        export: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/active-order/export';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, {responseType: 'blob'}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
