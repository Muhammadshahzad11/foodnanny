import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useWaiterOrderStore = defineStore('waiterOrder', {
    state: () => ({
        lists: [],
        page: {},
        pagination: [],
        show: {},
        context: {
            tableId: null,
            orderId: null,
            orderNote: '',
            token: '',
            updatedAt: null,
            isDraft: true,
        },
    }),
    actions: {
        setContext(payload) {
            this.context = {...this.context, ...payload};
        },
        resetContext() {
            this.context = {
                tableId: null,
                orderId: null,
                orderNote: '',
                token: '',
                updatedAt: null,
                isDraft: true,
            };
            this.show = {};
        },
        fetch(payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'admin/waiter/orders';
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
        view(id) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/waiter/orders/${id}`).then((res) => {
                    this.show = res.data.data;
                    this.setContext({
                        tableId: res.data.data.table_id,
                        orderId: res.data.data.id,
                        orderNote: res.data.data.order_note || '',
                        token: res.data.data.token || '',
                        updatedAt: res.data.data.updated_at,
                        isDraft: !!res.data.data.is_draft,
                    });
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        create(payload) {
            return new Promise((resolve, reject) => {
                axios.post('admin/waiter/orders', payload).then((res) => {
                    this.show = res.data.data;
                    this.setContext({
                        tableId: res.data.data.table_id,
                        orderId: res.data.data.id,
                        orderNote: res.data.data.order_note || '',
                        token: res.data.data.token || '',
                        updatedAt: res.data.data.updated_at,
                        isDraft: !!res.data.data.is_draft,
                    });
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        update(id, payload) {
            return new Promise((resolve, reject) => {
                axios.put(`admin/waiter/orders/${id}`, payload).then((res) => {
                    this.show = res.data.data;
                    this.setContext({
                        orderId: res.data.data.id,
                        orderNote: res.data.data.order_note || '',
                        token: res.data.data.token || '',
                        updatedAt: res.data.data.updated_at,
                        isDraft: !!res.data.data.is_draft,
                    });
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        sendKitchen(id, updatedAt = null) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/waiter/orders/${id}/send-kitchen`, {
                    updated_at: updatedAt,
                }).then((res) => {
                    this.show = res.data.data;
                    this.setContext({
                        orderId: res.data.data.id,
                        updatedAt: res.data.data.updated_at,
                        isDraft: !!res.data.data.is_draft,
                    });
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        cancelDraft(id) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/waiter/orders/${id}/cancel-draft`).then((res) => {
                    this.resetContext();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    }
});
