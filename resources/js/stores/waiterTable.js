import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useWaiterTableStore = defineStore('waiterTable', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        dashboard: {},
    }),
    actions: {
        fetchDashboard() {
            return new Promise((resolve, reject) => {
                axios.get('admin/waiter/dashboard').then((res) => {
                    this.dashboard = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetch(payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'admin/waiter/tables';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    this.lists = res.data.data;
                    this.pagination = res.data;
                    if (typeof res.data.meta !== 'undefined' && res.data.meta !== null) {
                        this.page = {
                            from: res.data.meta.from,
                            to: res.data.meta.to,
                            total: res.data.meta.total,
                        };
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        show(id) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/waiter/tables/${id}`).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    }
});
