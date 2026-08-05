import axios from "axios";
import {defineStore} from "pinia";

export const useFrontendRestaurantSignupStore = defineStore('frontendRestaurantSignup', {
    persist: true,
    state: () => ({
        code: "",
        phone: "",
        token: "",
        verify: false,
        ownerName: "",
        ownerEmail: "",
        ownerPassword: "",
        ownerVerify: false
    }),
    actions: {
        callPhone: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup-restaurant/phone";
                axios.post(url, payload).then((res) => {
                    this.code   = payload.code;
                    this.phone  = payload.phone;
                    this.verify = false;
                    resolve(res);
                }).catch((err) => {
                    this.verify = false;
                    reject(err);
                });
            });
        },
        callVerify: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup-restaurant/verify";
                axios.post(url, payload).then((res) => {
                    this.token  = payload.token;
                    this.verify = true;
                    resolve(res);
                }).catch((err) => {
                    this.verify = false;
                    reject(err);
                });
            });
        },
        callOwner: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup-restaurant/verify-owner";
                axios.post(url, payload).then((res) => {
                    this.ownerName     = payload.name;
                    this.ownerEmail    = payload.email;
                    this.ownerPassword = payload.password;
                    this.ownerVerify   = true;
                    resolve(res);
                }).catch((err) => {
                    this.ownerVerify = false;
                    reject(err);
                })
            })
        },
        callRegister: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup-restaurant/register-restaurant";
                axios.post(url, payload).then((res) => {
                    this.code          = "";
                    this.phone         = "";
                    this.verify        = false;
                    this.ownerName     = "";
                    this.ownerEmail    = "";
                    this.ownerPassword = "";
                    this.ownerVerify   = false;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
