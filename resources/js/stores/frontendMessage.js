import axios from "axios";
import {defineStore} from "pinia";

export const useFrontendMessageStore = defineStore('frontendMessage', {
    state: () => ({
        messages: []
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/message/show/${payload}`).then((res) => {
                    this.messages = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/frontend/message`, payload).then(res => {
                    this.fetch(payload.order_id).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
