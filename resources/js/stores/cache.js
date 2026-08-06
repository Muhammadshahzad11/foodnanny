import axios from 'axios'
import { defineStore } from 'pinia'

export const useCacheStore = defineStore('cache', {
    state: () => ({
        lastCleared: [],
    }),
    actions: {
        flush: function () {
            return new Promise((resolve, reject) => {
                axios.post('admin/system-setting/cache/flush').then((res) => {
                    this.lastCleared = res.data?.data?.cleared || [];
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
})
