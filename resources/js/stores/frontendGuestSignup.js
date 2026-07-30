import axios from "axios";
import {defineStore} from "pinia";

export const useFrontendGuestSignupStore = defineStore('useFrontendGuestSignupStore', {
    persist: true,
    state: () => ({
        code: "",
        phone: "",
        token: "",
        verify: false
    }),
    actions: {
        callPhone: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/guest-signup/phone";
                axios.post(url, payload).then((res) => {
                    this.code   = payload.code;
                    this.phone  = payload.phone;
                    this.verify = false;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        callReset: function () {
            this.code   = "";
            this.phone  = "";
            this.token  = "";
            this.verify = false;
        }
    }
})
