import {defineStore} from "pinia";
import _ from "lodash";
import orderTypeEnum from "../enums/modules/orderTypeEnum.js";

export const usePosCartStore = defineStore('posCart', {
    persist: true,
    state: () => ({
        lists: [],
        subtotal: 0,
        discount: 0,
        total: 0,
        tax: 0
    }),
    actions: {
        fetchCarts: function (payload) {
            return new Promise((resolve, reject) => {
                try {
                    this.discount = 0;
                    if (payload.length > 0) {
                        let isNew                    = false;
                        let newChecker               = [];
                        let variationAndExtraChecker = [];
                        _.forEach(payload, (pay) => {
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
                                            newChecker.push(true);
                                            this.lists[listKey].quantity += pay.quantity;
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
                    reject(err)
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

            if (this.discount > 0) {
                this.total -= this.discount;
            }

            if (this.tax > 0) {
                this.total += +this.tax;
            }
        },
        quantity: function (payload) {
            this.discount = 0;
            if (payload.status === "increment") {
                this.lists[payload.id].quantity++;
            } else if (payload.status === "decrement") {
                if (this.lists[payload.id].quantity === 1) {
                    this.lists.splice(payload.id, 1);
                } else {
                    this.lists[payload.id].quantity--;
                }
            } else {
                this.lists[payload.id].quantity = payload.status;
            }

            this.callSubtotal();
        },
        deleteCartItem: function (payload) {
            this.discount = 0;
            if (payload.status === "decrement") {
                this.lists.splice(payload.id, 1);
            }
            this.callSubtotal();
        },
        callDiscount: function (payload) {
            this.discount = payload;
            this.callSubtotal();
        },
        resetCart: function () {
            this.lists    = [];
            this.subtotal = 0;
            this.discount = 0;
            this.total    = 0;
            this.tax      = 0;
        }
    }
})
