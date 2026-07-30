import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useItemAttributeStore = defineStore('itemAttribute', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        temp: {
            temp_id: null,
            isEditing: false,
        }
    }),
    actions: {
        fetch: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = "admin/restaurant-setting/item-attribute";
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
                let url = "admin/restaurant-setting/item-attribute";
                if (this.temp.isEditing) {
                    url = `admin/restaurant-setting/item-attribute/update/${this.temp.temp_id}`;
                } 
                axios.post(url, payload.form).then((res) => {
                    this.fetch(payload.search).then().catch();
                    this.reset();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        edit: function (payload) {
            this.temp.temp_id   = payload;
            this.temp.isEditing = true;
        },
        destroy: function (payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/restaurant-setting/item-attribute/${payload.id}`).then((res) => {
                    this.fetch(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        reset: function () {
            this.temp.temp_id   = null;
            this.temp.isEditing = false;
        }
    }
})
