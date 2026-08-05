import axios from 'axios'
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useSmsGatewayStore = defineStore('smsGateway', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'admin/system-setting/sms-gateway';
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
                axios.post(`/admin/system-setting/sms-gateway/update`, payload.form).then(res => {
                    this.fetch(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
