import axios from 'axios'
import {defineStore} from "pinia";

export const useMenuSectionStore = defineStore('menuSection', {
    state: () => ({
        lists: [],
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                let url = "admin/system-setting/menu-section";
                axios.get(url).then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
