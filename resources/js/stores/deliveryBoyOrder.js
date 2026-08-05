import axios from "axios";
import {defineStore} from "pinia";
import appService from "../services/appService.js";

export const useDeliveryBoyOrderStore = defineStore('deliveryBoyOrder', {
    state: () => ({
        show: {},
        temp: {
            temp_id: null,
            isEditing: false,
        },
        deliveredOrders: [],
        deliveredOrderPage: {},
        deliveredOrderPagination: [],
        deliveredOrderDetails: {},
        orderRestaurant: {},
        orderDeliveryBoy: {},
        orderItems: {},
        orderUser: {},
        orderAddress: {},
    }),
    actions: {
        callDeliveredOrders: function (payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/delivery-boy/delivered-order/${payload.id}`;
                if (payload.search) {
                    url = url + appService.requestHandler(payload.search);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        this.deliveredOrders          = res.data.data;
                        this.deliveredOrderPagination = res.data;
                        if (typeof res.data.meta !== "undefined" && res.data.meta !== null) {
                            this.deliveredOrderPage = {
                                from: res.data.meta.from,
                                to: res.data.meta.to,
                                total: res.data.meta.total
                            }
                        }
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        callDeliveredOrderDetails: function (payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/delivery-boy/delivered-order/show/${payload.id}/${payload.orderId}`).then((res) => {
                    this.deliveredOrderDetails = res.data.data;
                    this.orderItems            = res.data.data.order_items;
                    this.orderRestaurant       = res.data.data.restaurant;
                    this.orderUser             = res.data.data.user;
                    this.orderAddress          = res.data.data.order_address;
                    this.orderDeliveryBoy      = res.data.data.delivery_boy;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
});
