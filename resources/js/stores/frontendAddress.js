import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useFrontendAddressStore = defineStore('frontendAddress', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_id: null,
            isEditing: false,
        },
        id: "",
    }),
    actions: {
        fetch: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = `frontend/address`;
                if (payload) {
                    url = url + appService.requestHandler(payload.search);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.lists      = res.data.data;
                        this.pagination = res.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.page = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total
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
                let url = `/frontend/address`;
                if (this.temp.isEditing) {
                    method = axios.put;
                    url = `/frontend/address/${this.temp.temp_id}`;
                }
                method(url, payload.form).then(res => {
                    this.temp.temp_id = null;
                    this.temp.isEditing = false;
                    this.fetch({search: payload.search}).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        edit: function (payload) {
            this.temp.temp_id = payload;
            this.temp.isEditing = true;
        },
        destroy: function (payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`frontend/address/${payload.id}`).then((res) => {
                    this.fetch({search: payload.search}).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        view: function(payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/address/show/${payload}`).then((res) => {
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
    }
})
