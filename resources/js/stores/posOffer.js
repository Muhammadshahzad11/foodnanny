import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";


export const usePosOfferStore = defineStore('posOffer', {
    state: () => ({
        check: {}
    }),
    actions: {
        fetch: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.get('admin/pos-offer').then((res) => {
                    this.check = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
