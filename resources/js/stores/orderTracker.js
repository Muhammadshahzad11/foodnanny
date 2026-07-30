import axios from 'axios';
import {defineStore} from "pinia";


export const useOrderTrackerStore = defineStore("orderTracker", {
    state: () => ({
        show: {},
        orderItems: {},
        orderRestaurant: {},
        orderUser: {},
        orderAddress: {},
        orderDeliveryBoy: {},
        orderCoupon: {},
    }),
    actions: {
        fetchByOrderId: function (payload) {
             return new Promise((resolve, reject) => {
                axios.post(`admin/order-tracker`, payload).then((res) => {
                    this.show             = res.data.data;
                    this.orderItems       = res.data.data?.order_items;
                    this.orderRestaurant  = res.data.data?.restaurant;
                    this.orderUser        = res.data.data?.user;
                    this.orderAddress     = res.data.data?.order_address;
                    this.orderDeliveryBoy = res.data.data?.delivery_boy;
                    this.orderCoupon      = res.data.data?.coupon || {};
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        }
    }
})
