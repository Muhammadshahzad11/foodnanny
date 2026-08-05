import axios from "axios";
import {defineStore} from "pinia";

export const useDashboardStore = defineStore('dashboard', {
    state: () => ({
        adminOverview: [],
        adminSalesSummary: [],
        adminOrdersSummary: [],
        adminRevenue: [],
        adminTopCustomers: [],
        adminTopDeliveryBoys: [],
        adminMostPopularRestaurants: [],
        restaurantOwnerOverview: [],
        restaurantOwnerCustomerStats: [],
        restaurantOwnerMostPopularItems: [],
        deliveryBoyOverview: [],
        deliveryBoyPayoutBalance: [],
        deliveryBoyCollectionBalance: [],
        deliveryBoyActiveOrders: [],
        moderateOverview: [],
        otherOverview: []
    }),
    actions: {
        fetchAdminOverview: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.post("admin/dashboard/admin-overview", payload).then((res) => {
                    this.adminOverview = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchAdminSalesSummary: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.post("admin/dashboard/admin-sales-summary", payload).then((res) => {
                    this.adminSalesSummary = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchAdminOrdersSummary: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.post("admin/dashboard/admin-orders-summary", payload).then((res) => {
                    this.adminSalesSummary = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchAdminRevenue: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/dashboard/admin-revenue").then((res) => {
                    this.adminRevenue = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchAdminTopCustomers: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/dashboard/admin-top-customers").then((res) => {
                    this.adminTopCustomers = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchAdminTopDeliveryBoys: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/dashboard/admin-top-delivery-boys").then((res) => {
                    this.adminTopDeliveryBoys = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchAdminMostPopularRestaurants: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/dashboard/admin-most-popular-restaurants").then((res) => {
                    this.adminMostPopularRestaurants = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchRestaurantOwnerOverview: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.post("admin/dashboard/restaurant-owner-overview", payload).then((res) => {
                    this.restaurantOwnerOverview = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchRestaurantOwnerCustomerStats: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.post("admin/dashboard/restaurant-owner-customer-stats", payload).then((res) => {
                    this.restaurantOwnerCustomerStats = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchRestaurantOwnerMostPopularItems: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/dashboard/restaurant-owner-most-popular-items").then((res) => {
                    this.restaurantOwnerMostPopularItems = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchDeliveryBoyOverview: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.post("admin/dashboard/delivery-boy-overview", payload).then((res) => {
                    this.deliveryBoyOverview = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchDeliveryBoyPayoutBalance: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/dashboard/delivery-boy-payout-balance").then((res) => {
                    this.deliveryBoyPayoutBalance = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchDeliveryBoyCollectionBalance: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/dashboard/delivery-boy-collection-balance").then((res) => {
                    this.deliveryBoyCollectionBalance = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchDeliveryBoyActiveOrders: function () {
            return new Promise((resolve, reject) => {
                axios.get("admin/dashboard/delivery-boy-active-orders").then((res) => {
                    this.deliveryBoyActiveOrders = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        fetchOtherOverview: function (payload = {}) {
            return new Promise((resolve, reject) => {
                axios.post("admin/dashboard/other-overview", payload).then((res) => {
                    this.otherOverview = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    }
});
