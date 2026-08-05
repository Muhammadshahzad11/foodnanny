import axios from "axios";
import {defineStore} from "pinia";

export const useFrontendDeliveryBoySignupStore = defineStore('frontendDeliveryBoySignup', {
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
                let url = "auth/signup-delivery-boy/phone";
                axios.post(url, payload).then(res => {
                    this.code   = payload.code;
                    this.phone  = payload.phone;
                    this.verify = false;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        callVerify: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup-delivery-boy/verify";
                axios.post(url, payload).then((res) => {
                    this.token  = payload.token;
                    this.verify = true;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        callRegister: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup-delivery-boy/register-delivery-boy";
                axios.post(url, payload).then((res) => {
                    this.code   = "";
                    this.phone  = "";
                    this.token  = "";
                    this.verify = false;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
