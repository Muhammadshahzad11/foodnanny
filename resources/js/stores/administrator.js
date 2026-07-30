import axios from "axios";
import { defineStore } from "pinia";
import appService from "../services/appService.js";

export const useAdministratorStore = defineStore('administrator', {
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
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/administrator";
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
                let url = "/admin/administrator";
                if (this.temp.isEditing) {
                    method = axios.post;
                    url = `/admin/administrator/${this.temp.temp_id}`;
                }
                method(url, payload.form)
                    .then((res) => {
                        this.fetch(payload.search).then().catch();
                        this.temp.temp_id = null;
                        this.temp.isEditing = false;
                        resolve(res);
                    })
                    .catch((err) => {
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
                axios
                    .delete(`admin/administrator/${payload.id}`)
                    .then((res) => {
                        this.fetch(payload.search).then().catch();
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/administrator/show/${payload}`).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                })
                    .catch((err) => {
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
                let url = 'admin/administrator/export';
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
                axios.post(`/admin/administrator/change-password/${payload.id}`, payload.form).then((res) => {
                    this.view(payload.id).then().catch();
                    resolve(res);
                })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        changeImage: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/administrator/change-image/${payload.id}`, payload.form, {
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
                let url = `admin/administrator/my-order/${payload.id}`;
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
        }
    }
})
