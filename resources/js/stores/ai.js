import axios from "axios";
import {defineStore} from "pinia";

export const useAiStore = defineStore("ai", {
    state: () => ({
        status: false,
        name: null,
        description: null,
        chatHistory: [],
        usageLimit: null,
    }),
    actions: {
        fetchStatus: function () {
            return new Promise((resolve, reject) => {
                axios.get("/admin/ai/status").then((res) => {
                    this.status = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchName: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post("/admin/ai/name", payload).then((res) => {
                    this.name = res.data.data.response;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchDescription: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post("/admin/ai/description", payload).then((res) => {
                    this.description = res.data.data.response;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        sendChatMessage: function (message) {
            return new Promise((resolve, reject) => {
                let url = "/admin/ai/chat";
                axios.post(url, {name: message}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        setChatResponse: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `/admin/ai/chat-response/${payload.id}`;
                axios.post(url, {name: payload.name}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchChatHistory: function () {
            return new Promise((resolve, reject) => {
                let url = "/admin/ai/chat-history";
                axios.get(url).then((res) => {
                    if (res.data.data) {
                        this.chatHistory = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
});
