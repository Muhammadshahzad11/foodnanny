import _ from "lodash";
import {defineStore} from "pinia";
import orderTypeEnum from "../enums/modules/orderTypeEnum.js";
import activityEnum from "../enums/modules/activityEnum.js";
import discountTypeEnum from "../enums/modules/discountTypeEnum.js";


export const useFrontendCartStore = defineStore('frontendCart', {
    persist: true,
    state: () => ({
        lists: [],
        serviceFee: 0,
        subtotal: 0,
        total: 0,
        coupon: {},
        discount: 0,
        tax: 0,
        orderType: null,
        restaurant: {},
        cutlery: false,
        paymentMethod: {},
        riderTip: {},
        address: {},
        timeSlot: {}
    }),
    actions: {
        fetchCarts: function (payload) {
            return new Promise((resolve, reject) => {
                try {
                    this.coupon = {};
                    this.discount = 0;

                    if (Object.keys(payload.restaurant).length > 0) {
                        if (Object.keys(this.restaurant).length === 0) {
                            this.restaurant = payload.restaurant;

                            if (payload.restaurant.order_setup.delivery === activityEnum.ENABLE && payload.restaurant.order_setup.takeaway === activityEnum.ENABLE) {
                                this.orderType = orderTypeEnum.DELIVERY;
                            } else if (payload.restaurant.order_setup.delivery === activityEnum.ENABLE) {
                                this.orderType = orderTypeEnum.DELIVERY;
                            } else if (payload.restaurant.order_setup.takeaway === activityEnum.ENABLE) {
                                this.orderType = orderTypeEnum.TAKEAWAY;
                            }
                        }
                    }

                    if (payload.items.length > 0) {
                        let isNew                    = false;
                        let newChecker               = [];
                        let variationAndExtraChecker = [];
                        _.forEach(payload.items, (pay) => {
                            if (this.lists.length === 0) {
                                isNew = true;
                            } else {
                                isNew = true;
                                _.forEach(this.lists, (list, listKey) => {
                                    if (list.item_id === pay.item_id) {

                                        if (this.lists[listKey].item_variations.variations !== "undefined") {
                                            if (Object.keys(this.lists[listKey].item_variations.variations).length !== 0) {
                                                _.forEach(this.lists[listKey].item_variations.variations, (variationId, variationKey) => {
                                                    if (pay.item_variations.variations[variationKey] !== "undefined" && pay.item_variations.variations[variationKey] === variationId) {
                                                        variationAndExtraChecker.push(true);
                                                    } else {
                                                        variationAndExtraChecker.push(false);
                                                    }
                                                });
                                            }
                                        }

                                        if (pay.item_extras.extras.length !== 0 && this.lists[listKey].item_extras.extras.length !== 0) {
                                            _.forEach(pay.item_extras.extras, (payExtra) => {
                                                if (this.lists[listKey].item_extras.extras.includes(payExtra) && this.lists[listKey].item_extras.extras.length === pay.item_extras.extras.length) {
                                                    variationAndExtraChecker.push(true);
                                                } else {
                                                    variationAndExtraChecker.push(false);
                                                }
                                            });
                                        } else {
                                            if (pay.item_extras.extras.length === this.lists[listKey].item_extras.extras.length) {
                                                variationAndExtraChecker.push(true);
                                            } else {
                                                variationAndExtraChecker.push(false);
                                            }
                                        }

                                        if (variationAndExtraChecker.includes(false)) {
                                            newChecker.push(false);
                                        } else {
                                            if((this.lists[listKey].quantity + pay.quantity) <= this.lists[listKey].maximum_purchase_quantity) {
                                                newChecker.push(true);
                                                this.lists[listKey].quantity += pay.quantity;
                                            } else {
                                                isNew = false;
                                                reject('max_quantity_error');
                                            }
                                        }
                                        variationAndExtraChecker = [];
                                    } else {
                                        newChecker.push(false);
                                    }
                                });

                                _.forEach(newChecker, (check) => {
                                    if (check) {
                                        isNew = false;
                                    }
                                });
                                newChecker = [];
                            }

                            if (isNew) {
                                this.lists.push({
                                    discount: pay.discount,
                                    maximum_purchase_quantity :pay.maximum_purchase_quantity,
                                    image: pay.image,
                                    instruction: pay.instruction,
                                    item_extra_total: pay.item_extra_total,
                                    item_extras: pay.item_extras,
                                    item_id: pay.item_id,
                                    item_variation_total: pay.item_variation_total,
                                    item_variations: pay.item_variations,
                                    name: pay.name,
                                    currency_price: pay.currency_price,
                                    convert_price: pay.convert_price,
                                    quantity: pay.quantity,
                                    tax_name: pay.tax !== null ? pay.tax.name : null,
                                    tax_rate: pay.tax != null ? pay.tax.tax_convert_rate : 0,
                                    tax_type: pay.tax != null ? pay.tax.type : null
                                });
                                isNew = false;
                            }
                        });
                    }

                    this.callSubtotal();
                    resolve(true);
                } catch (err) {
                    reject(err);
                }
            })
        },
        callSubtotal() {
            if (this.lists.length > 0) {
                let tax      = 0;
                let subtotal = 0;
                _.forEach(this.lists, (list, listKey) => {
                    this.lists[listKey].total      = ((list.convert_price + list.item_variation_total + list.item_extra_total) * list.quantity);
                    this.lists[listKey].tax_amount = list.tax_rate > 0 ? parseFloat((((list.convert_price + list.item_variation_total + list.item_extra_total) / 100) * list.tax_rate) * list.quantity) : 0;
                    subtotal += this.lists[listKey].total;
                    tax += this.lists[listKey].tax_amount
                });
                this.tax      = +tax.toFixed(2);
                this.subtotal = subtotal;
            } else {
                this.tax      = 0;
                this.subtotal = 0;
            }
            this.total = this.subtotal;

            if (this.tax > 0) {
                this.total += +this.tax;
            }

            if (Object.keys(this.coupon).length > 0) {
                this.total -= this.discount;
            }

            if (this.serviceFee > 0) {
                this.total += this.serviceFee;
            }

            if (Object.keys(this.riderTip).length > 0 && this.riderTip.amount > 0 && this.orderType === orderTypeEnum.DELIVERY) {
                this.total += this.riderTip.amount;
            }
        },
        setQuantity: function (payload) {
            return new Promise((resolve, reject) => {
                this.coupon   = {};
                this.discount = 0;
                if (payload.status === "increment") {
                    this.lists[payload.id].quantity++;
                    if (this.lists[payload.id].quantity > this.lists[payload.id].maximum_purchase_quantity) {
                        this.lists[payload.id].quantity = this.lists[payload.id].maximum_purchase_quantity;
                        reject('max_quantity_error');
                    }
                } else if (payload.status === "decrement") {
                    if (this.lists[payload.id].quantity <= 1) {
                        this.lists.splice(payload.id, 1);
                    } else {
                        this.lists[payload.id].quantity--;
                    }
                } else {
                    this.lists[payload.id].quantity = payload.status;
                    if (this.lists[payload.id].quantity > this.lists[payload.id].maximum_purchase_quantity) {
                        this.lists[payload.id].quantity = this.lists[payload.id].maximum_purchase_quantity;
                        reject('max_quantity_error');
                    }
                }

                if (this.lists.length === 0) {
                    this.restaurant = {};
                }
                this.callSubtotal();
                resolve(true);
            });
        },
        setCoupon: function (payload) {
            this.coupon = payload;
            if (Object.keys(payload).length > 0) {
                if (payload.discount_type === discountTypeEnum.PERCENTAGE) {
                    if(parseFloat(((this.subtotal / 100) * payload.convert_discount)) > payload.maximum_discount) {
                        this.discount = +parseFloat(payload.maximum_discount).toFixed(2);
                    } else {
                        this.discount = +parseFloat(((this.subtotal / 100) * payload.convert_discount)).toFixed(2);
                    }
                } else if (payload.discount_type === discountTypeEnum.FIXED) {
                    this.discount = payload.convert_discount;
                }
            } else {
                this.discount = 0;
            }
            this.callSubtotal();
        },
        callDestroyCoupon: function () {
            this.coupon   = {};
            this.discount = 0;
            this.callSubtotal();
        },
        callUpdateOrderType: function (payload) {
            this.coupon = {};
            this.discount = 0;
            if (orderTypeEnum.DELIVERY === payload || orderTypeEnum.TAKEAWAY === payload) {
                this.orderType = payload;
            } else {
                this.orderType = null;
            }
            this.callSubtotal();
        },
        setServiceFee: function (payload) {
            if (payload > 0) {
                this.serviceFee = payload;
            }
            this.callSubtotal();
        },
        setCutlery: function (payload) {
            this.cutlery = payload;
            this.callSubtotal();
        },
        setPaymentMethod: function (payload) {
            this.paymentMethod = payload;
            this.callSubtotal();
        },
        setRiderTip: function (payload) {
            this.riderTip = payload;
            this.callSubtotal();
        },
        setAddress: function (payload) {
            this.address = payload;
            this.callSubtotal();
        },
        setTimeSlot: function (payload) {
            this.timeSlot = payload;
            this.callSubtotal();
        },
        callResetCart: function () {
            this.lists         = [];
            this.subtotal      = 0;
            this.total         = 0;
            this.coupon        = {};
            this.discount      = 0;
            this.orderType     = null;
            this.cutlery       = false;
            this.restaurant    = {};
            this.paymentMethod = {};
            this.riderTip      = {};
            this.address       = {};
            this.timeSlot      = {};
        }
    }
})
