import axios from "axios";
import {defineStore} from "pinia";


export const useRestaurantSettingMenuStore = defineStore('restaurantSettingMenu', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                let url = "admin/restaurant-setting/menu";
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
