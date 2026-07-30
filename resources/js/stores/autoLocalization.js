import axios from "axios";
import {defineStore} from "pinia";

export const useAutoLocalizationStore = defineStore('autoLocalization', {
    state: () => ({
        lists: [],
    }),
    actions: {
        fetch: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.get("frontend/auto-localization").then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.lists = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
