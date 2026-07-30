import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useCampaignAndOfferStore = defineStore('campaignAndOffer', {
    state: () => ({
        campaignLists: [],
        offerLists: [],
        campaignPagination: [],
        campaignPage: {},
        showCampaign: {},
        offerPagination: [],
        offerPage: {},
        showOffer: {},
    }),
    actions: {
        fetchCampaignLists: function (payload = {}) {
            return new Promise((resolve, reject) => {
                let url = "admin/campaign-and-offer/campaign";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.campaignLists = res.data.data;
                        this.campaignPagination    = res.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.campaignPage = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total,
                            }
                        }
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchOfferLists: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/campaign-and-offer/offer';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.offerLists      = res.data.data;
                        this.offerPagination = res.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.offerPage = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total,
                            }
                        }
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchApplyCampaign: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/campaign-and-offer/campaign/apply/${payload.id}`).then((res) => {
                    this.fetchCampaignLists(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchApplyOffer: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/campaign-and-offer/offer/apply/${payload.id}`).then((res) => {
                    this.fetchOfferLists(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchShowCampaign: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/campaign-and-offer/campaign/show/${payload}`).then((res) => {
                    this.showCampaign = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchShowOffer: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/campaign-and-offer/offer/show/${payload}`).then((res) => {
                    this.showOffer = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        exportCampaign: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/campaign-and-offer/campaign/export';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, {responseType: 'blob'}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        exportOffer: function (payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/campaign-and-offer/offer/export';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, {responseType: 'blob'}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        leaveCampaign: function (payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/campaign-and-offer/campaign/${payload.id}`).then((res) => {
                    this.fetchCampaignLists(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchLeaveOffer: function (payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/campaign-and-offer/offer/${payload.id}`).then((res) => {
                    this.fetchOfferLists(payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
