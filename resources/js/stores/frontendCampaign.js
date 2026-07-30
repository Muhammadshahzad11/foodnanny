import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";
import availabilityEnum from "../enums/modules/availabilityEnum.js";
import _ from "lodash";


export const useFrontendCampaignStore = defineStore('frontendCampaign', {
    state: () => ({
        lists: [],
        show: {},
        showRestaurants: []
    }),
    actions: {
        fetch: function (payload) {
            return new Promise((resolve, reject) => {
                let url = "frontend/campaign";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        view: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/campaign/show/${payload.slug}`;
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
                    })
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
