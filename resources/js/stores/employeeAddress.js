import axios from 'axios'
import { defineStore } from "pinia";
import appService from "../services/appService.js";

export const useEmployeeAddressStore = defineStore('employeeAddress', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_id: null,
            isEditing: false,
        },
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/employee/address/${payload.id}`;
                if (payload) {
                    url = url + appService.requestHandler(payload.search);
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
                let url = `/admin/employee/address/${payload.id}`;
                if (this.temp.isEditing) {
                    method = axios.put;
                    url = `/admin/employee/address/${payload.id}/${this.temp.temp_id}`;
                }
                method(url, payload.form).then(res => {
                    this.fetch({ id: payload.id, search: payload.search }).then().catch();
                    this.temp.temp_id = null;
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
                axios.delete(`admin/employee/address/${payload.id}/${payload.addressId}`).then((res) => {
                    this.fetch({ id: payload.id, search: payload.search }).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/employee/address/show/${payload.id}/${payload.addressId}`).then((res) => {
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
    },
})
