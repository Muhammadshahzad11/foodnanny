<template>
    <div class="p-4 border-y border-gray-100">
        <h4 class="mb-3 text-sm text-center font-semibold capitalize text-heading">
            {{ $t('label.restaurant_current_status') }}
        </h4>
        <div class="flex items-center justify-center gap-3 w-fit mx-auto">
            <span class="text-sm font-semibold text-heading">{{ $t('label.off') }}</span>
            <label for="restaurant-status-switcher" class="custom-switcher">
                <input @click="statusChange($event)" type="checkbox" id="restaurant-status-switcher" :checked="myRestaurant.current_status === enums.statusEnum.ACTIVE">
            </label>
            <span class="text-sm font-semibold text-heading">{{ $t('label.on') }}</span>
        </div>
    </div>
</template>

<script>
import statusEnum from "../../../enums/modules/statusEnum.js";
import { useDefaultAccessStore } from "../../../stores/defaultAccess.js";
import { useMyRestaurantStore } from "../../../stores/myRestaurant.js";

export default {
    name: "RestaurantStatusComponent",
    setup() {
        const myRestaurantStore = useMyRestaurantStore();
        const defaultAccessStore = useDefaultAccessStore();
        return {
            myRestaurantStore, defaultAccessStore
        }
    },
    data() {
        return {
            enums: {
                statusEnum: statusEnum
            }
        }
    },
    computed: {
        myRestaurant: function () {
            return this.myRestaurantStore.lists;
        }
    },
    mounted() {
        let defaultAccess = this.defaultAccessStore.lists;
        if (defaultAccess.restaurant_id > 0) {
            this.myRestaurantStore.fetch().then().catch();
        }
    },
    methods: {
        statusChange: function (e) {
            this.myRestaurantStore.currentStatus({ current_status: e.target.checked === true ? this.enums.statusEnum.ACTIVE : this.enums.statusEnum.INACTIVE }).then().catch();
        }
    }
}
</script>
