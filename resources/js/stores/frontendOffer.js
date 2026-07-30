import axios from "axios";
import _, {forEach} from "lodash";
import {defineStore} from "pinia";
import availabilityEnum from "../enums/modules/availabilityEnum.js";
import appService from "../services/appService.js";


export const useFrontendOfferStore = defineStore('frontendOffer', {
    state: () => ({
        multi: [],
        single: {},
        singleRestaurants: [],
        showRestaurants: [],
        show: {},
        find: {},
        check: {}
    }),
    actions: {
        fetchSingle: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = "frontend/offer/single";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }

                axios.get(url).then((res) => {
                    this.singleRestaurants = [];
                    this.single            = res.data.data;
                    _.forEach(res.data.data.restaurants, (pay) => {
                        if (pay.id > 0 && pay.availability === availabilityEnum.OPEN) {
                            this.singleRestaurants.push(pay);
                        }
                    });
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchMulti: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = "frontend/offer";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    this.multi = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        view: function (payload)  {
            return new Promise((resolve, reject) => {
                let url = `frontend/offer/show/${payload.slug}`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    this.showRestaurants = [];
                    this.show            = res.data.data;
                    _.forEach(res.data.data.restaurants, (pay) => {
                        if (pay.id > 0 && pay.availability === availabilityEnum.OPEN) {
                            this.showRestaurants.push(pay);
                        }
                    });
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchFind: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = "frontend/offer/find";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    this.find = {};
                    res.data.data.forEach((pay) => {
                        if (pay.id > 0) {
                            this.find[pay.id] = pay;
                        }
                    });
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchCheck: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.post(`frontend/offer/check/${payload.id}`, payload).then((res) => {
                    this.check = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
