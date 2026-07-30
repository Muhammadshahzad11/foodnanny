import axios from "axios";
import { defineStore } from "pinia";

export const useMyOrderDetailsStore = defineStore('myOrderDetails', {
    state: () => ({
        orderDetails: {},
        orderItems: {},
        orderRestaurant: {},
        orderUser: {},
        orderAddress: {},
        orderDeliveryBoy: {},
    }),
    actions: {
        callOrderDetails: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/my-order/show/${payload.id}/${payload.orderId}`).then((res) => {
                    this.orderDetails = res.data.data;
                    this.orderItems = res.data.data.order_items;
                    this.orderRestaurant = res.data.data.restaurant;
                    this.orderUser = res.data.data.user;
                    this.orderAddress = res.data.data.order_address;
                    this.orderDeliveryBoy = res.data.data.delivery_boy;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
});
