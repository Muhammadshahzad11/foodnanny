import axios from "axios";
import {defineStore} from "pinia";

export const useMessageStore = defineStore('message', {
    state: () => ({
        lists: [],
        messages: []
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get(`admin/message`).then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        show: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/message/show/${payload}`).then((res) => {
                    this.messages = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/message`, payload).then(res => {
                    this.show(payload.order_id).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
});
