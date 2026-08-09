import {defineStore} from "pinia";
import _ from "lodash";

function normalizeExtras(extras) {
    if (!extras || typeof extras !== 'object') {
        return {extras: [], names: []};
    }
    return {
        extras: Array.isArray(extras.extras) ? extras.extras.slice() : [],
        names: Array.isArray(extras.names) ? extras.names.slice() : [],
    };
}

function normalizeVariations(variations) {
    if (!variations || typeof variations !== 'object') {
        return {variations: {}, names: {}};
    }
    return {
        variations: variations.variations && typeof variations.variations === 'object'
            ? {...variations.variations}
            : {},
        names: variations.names && typeof variations.names === 'object'
            ? {...variations.names}
            : {},
    };
}

function sameVariations(a, b) {
    const av = normalizeVariations(a).variations;
    const bv = normalizeVariations(b).variations;
    const ak = Object.keys(av);
    const bk = Object.keys(bv);
    if (ak.length !== bk.length) {
        return false;
    }
    return ak.every((key) => String(av[key]) === String(bv[key]));
}

function sameExtras(a, b) {
    const ae = normalizeExtras(a).extras.map(String).sort();
    const be = normalizeExtras(b).extras.map(String).sort();
    if (ae.length !== be.length) {
        return false;
    }
    return ae.every((v, i) => v === be[i]);
}

function normalizeCartItem(pay) {
    return {
        discount: Number(pay.discount) || 0,
        maximum_purchase_quantity: Number(pay.maximum_purchase_quantity) || 0,
        image: pay.image || '',
        instruction: pay.instruction || '',
        item_extra_total: Number(pay.item_extra_total) || 0,
        item_extras: normalizeExtras(pay.item_extras),
        item_id: pay.item_id,
        item_variation_total: Number(pay.item_variation_total) || 0,
        item_variations: normalizeVariations(pay.item_variations),
        name: pay.name || '',
        currency_price: pay.currency_price,
        convert_price: Number(pay.convert_price) || 0,
        quantity: Math.max(1, Number(pay.quantity) || 1),
        tax_name: pay.tax != null ? pay.tax.name : (pay.tax_name ?? null),
        tax_rate: pay.tax != null ? (Number(pay.tax.tax_convert_rate) || 0) : (Number(pay.tax_rate) || 0),
        tax_type: pay.tax != null ? pay.tax.type : (pay.tax_type ?? null),
    };
}

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
                    // Repair persisted/corrupt cart rows
                    this.lists = (this.lists || []).map((row) => normalizeCartItem(row)).filter((row) => row.item_id);

                    const items = Array.isArray(payload) ? payload : [];
                    items.forEach((raw) => {
                        const pay = normalizeCartItem(raw);
                        if (!pay.item_id) {
                            return;
                        }

                        const matchIndex = this.lists.findIndex((list) =>
                            list.item_id === pay.item_id
                            && sameVariations(list.item_variations, pay.item_variations)
                            && sameExtras(list.item_extras, pay.item_extras)
                            && String(list.instruction || '') === String(pay.instruction || '')
                        );

                        if (matchIndex >= 0) {
                            this.lists[matchIndex].quantity += pay.quantity;
                        } else {
                            this.lists.push(pay);
                        }
                    });

                    this.callSubtotal();
                    resolve(true);
                } catch (err) {
                    console.error('POS cart add failed', err);
                    reject(err);
                }
            });
        },
        callSubtotal() {
            if (this.lists.length > 0) {
                let tax = 0;
                let subtotal = 0;
                this.lists.forEach((list, listKey) => {
                    const unit = (Number(list.convert_price) || 0)
                        + (Number(list.item_variation_total) || 0)
                        + (Number(list.item_extra_total) || 0);
                    const qty = Math.max(0, Number(list.quantity) || 0);
                    this.lists[listKey].total = unit * qty;
                    this.lists[listKey].tax_amount = list.tax_rate > 0
                        ? parseFloat((((unit / 100) * Number(list.tax_rate)) * qty).toFixed(4))
                        : 0;
                    subtotal += this.lists[listKey].total;
                    tax += this.lists[listKey].tax_amount;
                });
                this.tax = +tax.toFixed(2);
                this.subtotal = +subtotal.toFixed(2);
            } else {
                this.tax = 0;
                this.subtotal = 0;
            }
            this.total = this.subtotal;

            if (this.discount > 0) {
                this.total -= this.discount;
            }

            if (this.tax > 0) {
                this.total += +this.tax;
            }
            this.total = +Number(this.total).toFixed(2);
        },
        quantity: function (payload) {
            this.discount = 0;
            if (!this.lists[payload.id]) {
                return;
            }
            if (payload.status === "increment") {
                this.lists[payload.id].quantity++;
            } else if (payload.status === "decrement") {
                if (this.lists[payload.id].quantity === 1) {
                    this.lists.splice(payload.id, 1);
                } else {
                    this.lists[payload.id].quantity--;
                }
            } else {
                const q = Number(payload.status);
                this.lists[payload.id].quantity = Number.isFinite(q) && q > 0 ? q : 1;
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
            this.discount = Number(payload) || 0;
            this.callSubtotal();
        },
        resetCart: function () {
            this.lists = [];
            this.subtotal = 0;
            this.discount = 0;
            this.total = 0;
            this.tax = 0;
        }
    }
});
