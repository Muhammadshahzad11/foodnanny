import axios from 'axios'
import {defineStore} from "pinia";
import appService from "../services/appService.js";


export const useFrontendItemCategoryStore = defineStore('frontendItemCategory', {
    state: () => ({
        categoryWiseItems: [],
        categoryWiseItemsPage: {},
        categoryWiseItemsPagination: [],
    }),
    actions: {
        fetchCategoryWiseItems: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/item-category/items/${payload.slug}`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if(typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.categoryWiseItems = res.data.data;
                        this.categoryWiseItemsPagination = res.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.categoryWiseItemsPage = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total,
                            }
                        }
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
