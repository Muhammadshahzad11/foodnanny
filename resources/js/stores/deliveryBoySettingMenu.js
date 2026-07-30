import axios from "axios";
import { defineStore } from "pinia";


export const useDeliveryBoySettingMenuStore = defineStore('deliveryBoySettingMenu', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                let url = "admin/delivery-boy-setting/menu";
                axios.get(url).then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
