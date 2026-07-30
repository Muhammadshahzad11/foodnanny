import axios from "axios";
import { defineStore } from "pinia";
import appService from "../services/appService.js";

export const useRestaurantStore = defineStore('restaurant', {
    state: () => ({
        allRestaurants: [],
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_id: null,
            isEditing: false
        }
    }),
    actions: {
        fetchAllRestaurant: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/restaurant/all-restaurant").then((res) => {
                    this.allRestaurants = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/restaurant";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.lists = res.data.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.page = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total,
                            };
                        }
                        this.pagination = res.data;
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
                let url    = "/admin/restaurant";
                if (this.temp.isEditing) {
                    method = axios.put;
                    url    = `/admin/restaurant/${this.temp.temp_id}`;
                }
                method(url, payload.form).then((res) => {
                    this.fetch(payload.search).then().catch();
                    this.temp.temp_id   = null;
                    this.temp.isEditing = false;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        edit: function (payload) {
            return new Promise((resolve, reject) => {
                this.temp.temp_id = payload;
                this.temp.isEditing = true;
                resolve();
            })
        },
        destroy: function (payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/restaurant/${payload.id}`).then((res) => {
                    this.fetch(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/restaurant/show/${payload}`).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeImage: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/restaurant/change-image/${payload.id}`, payload.form, {headers: {"Content-Type": "multipart/form-data"}})
                    .then((res) => {
                        this.show = res.data.data;
                        resolve(res);
                    }).catch((err) => {
                        reject(err);
                    });
            });
        },
        changeLogo: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/restaurant/change-logo/${payload.id}`, payload.form,{ headers: {"Content-Type": "multipart/form-data"}})
                .then((res) => {
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
        userSave: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/restaurant/user/${payload.id}`, payload.form).then(res => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        export: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/restaurant/export';
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
    },
});
