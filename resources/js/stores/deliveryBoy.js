import axios from "axios";
import { defineStore } from "pinia";
import appService from "../services/appService.js";

export const useDeliveryBoyStore = defineStore('deliveryBoy', {
    state: () => ({
        allDeliveryBoys: [],
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
        collectionPage: {},
        collectionPagination: [],
        deliveryBoyCollections: [],
    }),
    actions: {
        fetchAllDeliveryBoy: function () {
            return new Promise((resolve, reject) => {
                let url = "admin/delivery-boy/all-delivery-boy";
                axios.get(url).then((res) => {
                    this.allDeliveryBoys = res.data.data; 
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/delivery-boy";
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
                let method = axios.post;
                let url = "/admin/delivery-boy";
                if (this.temp.isEditing) {
                    method = axios.put;
                    url = `/admin/delivery-boy/${this.temp.temp_id}`;
                }
                method(url, payload.form).then((res) => {
                    this.fetch(payload.search).then().catch();
                    this.temp.temp_id = null;
                    this.temp.isEditing = false;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        edit: function (payload) {
            this.temp.temp_id = payload;
            this.temp.isEditing = true;
        },
        destroy: function (payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/delivery-boy/${payload.id}`).then((res) => {
                    this.fetch(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/delivery-boy/show/${payload}`).then((res) => {
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
                let url = 'admin/delivery-boy/export';
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
        changePassword: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/delivery-boy/change-password/${payload.id}`, payload.form).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeImage: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/delivery-boy/change-image/${payload.id}`, payload.form, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
                ).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        callMyOrders: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/delivery-boy/my-order/${payload.id}`;
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
                })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        fetchDeliveryBoyCollection: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/delivery-boy/delivery-boy-collection";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.deliveryBoyCollections = res.data.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.collectionPage = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total,
                            };
                        }
                        this.collectionPagination = res.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },

    },
});
