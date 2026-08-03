import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useRestaurantTableStore = defineStore('restaurantTable', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_id: null,
            isEditing: false,
        }
    }),
    actions: {
        fetch: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = "admin/table";
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
                            }
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
                let url = "admin/table";
                if (this.temp.isEditing) {
                    method = axios.put;
                    url = `admin/table/${this.temp.temp_id}`;
                }
                method(url, payload.form).then((res) => {
                    this.fetch(payload.search).then().catch();
                    this.reset();
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
        show: function (id) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/table/show/${id}`).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        destroy: function (payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/table/${payload.id}`).then((res) => {
                    this.fetch(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeStatus: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/table/change-status/${payload.id}`, {
                    status: payload.status
                }).then((res) => {
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
});
