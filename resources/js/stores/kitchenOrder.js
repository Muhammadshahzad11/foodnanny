import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useKitchenOrderStore = defineStore('kitchenOrder', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        dashboard: {},
        printPayload: null,
    }),
    actions: {
        fetchDashboard() {
            return new Promise((resolve, reject) => {
                axios.get('admin/kitchen/dashboard').then((res) => {
                    this.dashboard = res.data.data;
                    resolve(res);
                }).catch(reject);
            });
        },
        fetch(payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'admin/kitchen/orders';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    this.lists = res.data.data;
                    this.pagination = res.data;
                    if (res.data.meta) {
                        this.page = {
                            from: res.data.meta.from,
                            to: res.data.meta.to,
                            total: res.data.meta.total,
                        };
                    }
                    resolve(res);
                }).catch(reject);
            });
        },
        view(id) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/kitchen/orders/${id}`).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch(reject);
            });
        },
        accept(id, updatedAt = null) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/kitchen/orders/${id}/accept`, {updated_at: updatedAt}).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch(reject);
            });
        },
        preparing(id, updatedAt = null) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/kitchen/orders/${id}/preparing`, {updated_at: updatedAt}).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch(reject);
            });
        },
        ready(id, updatedAt = null) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/kitchen/orders/${id}/ready`, {updated_at: updatedAt}).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch(reject);
            });
        },
        priority(id, kitchenPriority) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/kitchen/orders/${id}/priority`, {
                    kitchen_priority: kitchenPriority,
                }).then((res) => {
                    this.show = res.data.data;
                    resolve(res);
                }).catch(reject);
            });
        },
        printData(id) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/kitchen/orders/${id}/print-data`).then((res) => {
                    this.printPayload = res.data.data;
                    resolve(res);
                }).catch(reject);
            });
        },
    }
});
