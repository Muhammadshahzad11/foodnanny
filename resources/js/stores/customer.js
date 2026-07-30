import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useCustomerStore = defineStore('customer', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_id: null,
            isEditing: false,
        },
        myOrders: [],
        orderPage: {},
        orderPagination: [],
        allCustomers: []
    }),
    actions: {
        fetchAllCustomer: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = "admin/customer/all-customer";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    this.allCustomers = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/customer";
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
                let method = axios.post;
                let url    = "/admin/customer";
                if (this.temp.isEditing) {
                    method = axios.post;
                    url    = `/admin/customer/${this.temp.temp_id}`;
                }
                method(url, payload.form)
                    .then((res) => {
                        this.fetch(payload.search).then().catch();
                        this.temp.temp_id   = null;
                        this.temp.isEditing = false;
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        edit: function (payload) {
            return new Promise((resolve) => {
                this.temp.temp_id   = payload;
                this.temp.isEditing = true;
                resolve();
            })
        },
        destroy: function (payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/customer/${payload.id}`).then((res) => {
                        this.fetch(payload.search).then().catch();
                        resolve(res);
                    }).catch((err) => {
                        reject(err);
                    });
            });
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/customer/show/${payload}`).then((res) => {
                        this.show = res.data.data;
                        resolve(res);
                    }).catch((err) => {
                        reject(err);
                    });
            });
        },
        reset: function () {
            this.temp.temp_id   = null;
            this.temp.isEditing = false;
        },

        export: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/customer/export';
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
        changePassword: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/customer/change-password/${payload.id}`, payload.form).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeImage: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/customer/change-image/${payload.id}`, payload.form,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                ).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                })
                .catch((err) => {
                    reject(err);
                });
            });
        },
        callMyOrders: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/customer/my-order/${payload.id}`;
                if (payload.search) {
                    url = url + appService.requestHandler(payload.search);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.myOrders = res.data.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.orderPage = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total,
                            };
                        }
                        this.orderPagination = res.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
});
