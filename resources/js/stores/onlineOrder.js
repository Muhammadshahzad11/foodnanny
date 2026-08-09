import axios from "axios";
import { defineStore } from "pinia";
import appService from "../services/appService.js";

export const useOnlineOrderStore = defineStore('onlineOrder', {
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
            isEditing: false
        }
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/online-order";
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
                });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post("admin/online-order", payload).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/online-order/show/${payload}`).then((res) => {
                    this.show = res.data.data;
                    this.orderItems = res.data.data.order_items;
                    this.orderRestaurant = res.data.data.restaurant;
                    this.orderUser = res.data.data.user;
                    this.orderAddress = res.data.data.order_address;
                    this.orderDeliveryBoy = res.data.data.delivery_boy;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeStatus: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/online-order/change-status/${payload.id}`,payload).then((res) => {
                    resolve(res);
                    setTimeout(() => {this.show = res.data.data;}, 300);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changePaymentStatus: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/online-order/change-payment-status/${payload.id}`,payload).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        selectDeliveryBoy: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/online-order/select-delivery-boy/${payload.id}`,payload).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        reset: function () {
            this.temp.temp_id = null;
            this.temp.isEditing = false;
        },
        export: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/online-order/export';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, { responseType: 'blob' }).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        addToken: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/online-order/add-token/${payload.id}`, payload).then(res => {
                    this.show            = res.data.data;
                    this.orderItems      = res.data.data.order_items;
                    this.orderRestaurant = res.data.data.restaurant;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        printInvoice: function (orderId) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/online-order/${orderId}/print-invoice`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        printKot: function (orderId) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/online-order/${orderId}/print-kot`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        printBoth: function (orderId) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/online-order/${orderId}/print-both`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    }
})
