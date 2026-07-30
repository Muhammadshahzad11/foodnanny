import axios from 'axios'
import {defineStore} from "pinia";

export const useTimezoneStore = defineStore('timezone', {
    state: () => ({
        lists: []
    }),
    actions: {
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get('admin/timezone').then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
