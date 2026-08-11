import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const usePosOrderStore = defineStore('posOrder', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        orderItems: {},
        restaurant: {},
        orderUser: {},
        posDetail: {}
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/pos-order";
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
                axios.post("/admin/pos", payload).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/pos-order/show/${payload}`).then((res) => {
                    this.show       = res.data.data;
                    this.orderItems = res.data.data.order_items;
                    this.restaurant = res.data.data.restaurant;
                    this.orderUser  = res.data.data.user;
                    this.posDetail  = res.data.data.pos_detail;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        requestDeleteOtp: function (orderId) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/pos-order/${orderId}/request-delete-otp`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        destroy: function (payload) {
            return new Promise((resolve, reject) => {
                const otp = String(payload.otp || payload.token || '').trim();
                if (!otp) {
                    reject({response: {data: {message: 'OTP is required to delete this order.'}}});
                    return;
                }
                axios.post(`admin/pos-order/${payload.id}/confirm-delete`, {
                    otp,
                    token: otp,
                }).then((res) => {
                    this.fetch(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeStatus: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/pos-order/change-status/${payload.id}`, payload).then((res) => {
                    resolve(res);
                    setTimeout(() => {
                        this.show = res.data.data;
                    }, 300);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        export: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/pos-order/export';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, {responseType: 'blob'}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        printInvoice: function (orderId) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/pos-order/${orderId}/print-invoice`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        printKot: function (orderId) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/pos-order/${orderId}/print-kot`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchTables: function () {
            return new Promise((resolve, reject) => {
                axios.get('admin/pos/tables').then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        updateTableStatus: function (tableId, status) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/pos/tables/${tableId}/status`, {status}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        saveCustomer: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post('admin/pos/customers', payload).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchCustomers: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'admin/pos/customers';
                if (payload && Object.keys(payload).length) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchOpenOrders: function () {
            return new Promise((resolve, reject) => {
                axios.get('admin/pos/open-orders').then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        openOrderForTable: function (tableId) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/pos/open-orders/table/${tableId}`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        updateOpenOrder: function (orderId, payload) {
            return new Promise((resolve, reject) => {
                axios.put(`/admin/pos/open-orders/${orderId}`, payload).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        printBill: function (orderId) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/pos/open-orders/${orderId}/print-bill`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        payOpenOrder: function (orderId, payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/pos/open-orders/${orderId}/pay`, payload).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        orderHistory: function (orderId) {
            return new Promise((resolve, reject) => {
                axios.get(`/admin/pos/open-orders/${orderId}/history`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    }
})
