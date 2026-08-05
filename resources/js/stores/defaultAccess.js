import axios from "axios";
import {defineStore} from "pinia";

export const useDefaultAccessStore =  defineStore('defaultAccess', {
    persist : true,
    state: () => ({
        lists: [],
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/default-access").then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
