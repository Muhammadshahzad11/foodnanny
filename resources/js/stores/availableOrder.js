import axios from "axios";
import { defineStore } from "pinia";
import appService from "../services/appService.js";

export const useAvailableOrderStore = defineStore('availableOrder', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        temp: {
            temp_id: null,
            isEditing: false,
        },
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/available-order";
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
        changeStatus: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/available-order/change-status/${payload.id}`).then((res) => {
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
        },
        export: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/available-order/export';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, { responseType: 'blob' }).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
