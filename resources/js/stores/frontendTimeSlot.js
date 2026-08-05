import axios from "axios";
import {defineStore} from "pinia";

export const useFrontendTimeSlotStore = defineStore("frontendTimeSlot", {
    state: () => ({
        today: [],
        tomorrow: [],
        now: {}
    }),
    actions: {
        fetchToday: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/time-slot/today/${payload}`).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        if(res.data.data.length > 0 && res.data.data[0].label === 'now') {
                            this.now = res.data.data[0];
                            res.data.data.shift();
                        }
                        this.today = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchTomorrow: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/time-slot/tomorrow/${payload}`).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.tomorrow = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
