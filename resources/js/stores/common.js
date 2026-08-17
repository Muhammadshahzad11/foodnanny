import {defineStore} from "pinia";
import displayModeEnum from "../enums/modules/displayModeEnum.js";

export const useCommonStore = defineStore('common', {
    persist: true,
    state: () => ({
        language_id: 0,
        language_code: null,
        top_sidebar: true,
        display_mode: displayModeEnum.LTR,
        order_type: 0,
        search_restaurant: null,
        location: null,
        latitude: null,
        longitude: null,
        city: null,
        district: null,
        state: null,
        cuisine_id: null,
        edit_address_id: 0,
        localization: false,
        manual_location_pick: false,
    }),
    actions: {
        init: function (payload) {
            return new Promise((resolve, reject) => {
                if (typeof payload === 'object') {
                    for (const key in payload) {
                        if (key === 'language_id' && this.language_id === 0) {
                            this.language_id = payload[key];
                        } else if (key === 'language_code' && this.language_code === null) {
                            this.language_code = payload[key];
                        } else if (key === 'top_sidebar' && this.top_sidebar === null) {
                            this.top_sidebar = payload[key];
                        } else if (key === 'display_mode' && this.display_mode === displayModeEnum.LTR) {
                            this.display_mode = payload[key];
                        } else if (key === 'order_type' && this.order_type === 0) {
                            this.order_type = payload[key];
                        } else if (key === 'search_restaurant' && this.search_restaurant === null) {
                            this.search_restaurant = payload[key];
                        } else if (key === 'location' && this.location === null) {
                            this.location = payload[key];
                        } else if (key === 'latitude' && this.latitude === null) {
                            this.latitude = payload[key];
                        } else if (key === 'longitude' && this.longitude === null) {
                            this.longitude = payload[key];
                        } else if (key === 'city' && this.city === null) {
                            this.city = payload[key];
                        } else if (key === 'district' && this.district === null) {
                            this.district = payload[key];
                        } else if (key === 'state' && this.state === null) {
                            this.state = payload[key];
                        } else if (key === 'cuisine_id' && this.cuisine_id === null) {
                            this.cuisine_id = payload[key];
                        } else if (key === 'edit_address_id' && this.edit_address_id === null) {
                            this.edit_address_id = payload[key];
                        } else if (key === 'localization' && this.localization === false) {
                            this.localization = payload[key];
                        }
                    }
                    resolve(payload);
                } else {
                    reject("object not found");
                }
            });
        },
        update: function (payload) {
            return new Promise((resolve, reject) => {
                if (typeof payload === 'object') {
                    for (const key in payload) {
                        if (Object.prototype.hasOwnProperty.call(this.$state, key)) {
                            this[key] = payload[key];
                        }
                    }
                    resolve(payload);
                } else {
                    reject("object not found");
                }
            });
        },
        clearLocation: function () {
            return this.update({
                location: null,
                latitude: null,
                longitude: null,
                city: null,
                district: null,
                state: null,
                search_restaurant: null,
                cuisine_id: null,
                // Allow auto GPS again on the next visit after clearing.
                manual_location_pick: false,
            });
        }
    }
})
