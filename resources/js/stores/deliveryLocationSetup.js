import axios from "axios";
import { defineStore } from "pinia";

export const useDeliveryLocationSetupStore = defineStore('deliveryLocationSetup', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get('admin/delivery-boy-setting/delivery-location-setup').then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/delivery-boy-setting/delivery-location-setup`, payload).then(res => {
                    this.fetch(payload).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
});
