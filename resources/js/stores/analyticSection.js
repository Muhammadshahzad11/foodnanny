import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useAnalyticSectionStore = defineStore('analyticSection', {
    state: () => ({
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
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/system-setting/analytic-section/${payload.analyticId}`;
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
                let url = `/admin/system-setting/analytic-section/${payload.analyticId}`;
                if (this.temp.isEditing) {
                    url = `/admin/system-setting/analytic-section/${payload.analyticId}/${this.temp.temp_id}`;
                }
                axios.post(url, payload.form).then((res) => {
                    this.fetch({analyticId: payload.analyticId, search: payload.search}).then().catch();
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
                axios.delete(`admin/system-setting/analytic-section/${payload.analyticId}/${payload.id}`).then((res) => {
                    this.fetch({analyticId: payload.analyticId, search: payload.search}).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/system-setting/analytic/analytic-section/${payload}`).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        reset: function () {
            this.temp.temp_id   = null;
            this.temp.isEditing = false;
        }
    }
})
