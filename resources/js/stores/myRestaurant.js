import axios from "axios";
import {defineStore} from "pinia";

export const useMyRestaurantStore = defineStore('myRestaurant', {
    state: () => ({
        lists: {},
        defaultRestaurant: {}
    }),
    actions: {
        fetchDefaultRestaurant: function() {
            return new Promise((resolve, reject) => {
                axios.get('admin/restaurant-setting/default-restaurant').then((res) => {
                    if(typeof res.data === 'string' && res.data === "") {
                        this.defaultRestaurant = {};
                    } else {
                        this.defaultRestaurant = res.data.data;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetch: function () {
            return new Promise((resolve, reject) => {
                axios.get('admin/restaurant-setting/my-restaurant').then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`admin/restaurant-setting/my-restaurant`, payload.form).then(res => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeImage: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/restaurant-setting/my-restaurant/change-image`, payload.form, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                ).then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeLogo: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/restaurant-setting/my-restaurant/change-logo`, payload.form, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                ).then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        currentStatus: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post("/admin/restaurant-setting/my-restaurant/current-status", payload).then((res) => {
                    this.lists = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        resetDefaultRestaurant: function() {
            this.defaultRestaurant = {};
        }
    }
})

