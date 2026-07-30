import axios from 'axios';
import {defineStore} from "pinia";


export const useFrontendReviewStore = defineStore("frontendReview", {
    state: () => ({
        restaurantReview : {},
        deliveryBoyReview: {}
    }),
    actions: {
        fetchRestaurantReview: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/restaurant-review/${payload}`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchDeliveryBoyReview: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/delivery-boy-review/${payload}`).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        saveRestaurantReview: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/frontend/restaurant-review/${payload.orderId}`, payload.form).then((res) => {
                    this.restaurantReview = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        saveDeliveryBoyReview: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/frontend/delivery-boy-review/${payload.orderId}`, payload.form).then((res) => {
                    this.deliveryBoyReview = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
