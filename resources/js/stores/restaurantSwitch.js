import axios from "axios";
import {defineStore} from "pinia";
import {useDefaultAccessStore} from "./defaultAccess.js";


export const useRestaurantSwitchStore = defineStore('restaurantSwitch', {
    state: () => ({
        lists: [],
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/restaurant-switch").then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        switch: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post('/admin/restaurant-switch/switch', payload).then(res => {
                    const defaultAccessStore = useDefaultAccessStore();
                    defaultAccessStore.fetch().then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
