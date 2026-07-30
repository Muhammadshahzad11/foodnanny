import axios from 'axios'
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useAiAgentStore = defineStore('aiAgent', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'admin/system-setting/ai-agent';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                } 
                axios.get(url).then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/system-setting/ai-agent/update`, payload.form).then(res => {
                    this.fetch(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
