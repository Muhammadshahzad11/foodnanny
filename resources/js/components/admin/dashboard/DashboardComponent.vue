<template>
    <LoadingComponent :props="loading"/>

    <div v-if="demo === 'true' || demo === 'TRUE' || demo === 'True' || demo === '1' || demo === 1"
         class="mb-4 bg-primary/20 p-2 pl-4 rounded-lg">
        <h2 class="mb-1">{{ $t('label.reminder') }}</h2>
        <p>{{ $t('message.data_reset') }}</p>
    </div>

    <div class="mb-8">
        <h3 class="font-semibold text-[26px] leading-10 capitalize text-primary">{{ visitorMessage() }}</h3>
        <h4 class="font-medium text-[22px] leading-[34px] capitalize">{{ authInfo.name }}</h4>
    </div>

    <AdminDashboardComponent v-if="authInfo.role_id === enums.roleEnum.ADMIN && defaultAccessStore.lists.restaurant_id === 0"/>
    <RestaurantOwnerDashboardComponent v-if="authInfo.role_id === enums.roleEnum.RESTAURANT_OWNER || (authInfo.role_id === enums.roleEnum.ADMIN && defaultAccessStore.lists.restaurant_id > 0)"/>
    <DeliveryBoyDashboardComponent v-if="authInfo.role_id === enums.roleEnum.DELIVERY_BOY"/>
    <OtherDashboardComponent v-if="authInfo.role_id > 0 && (authInfo.role_id !== enums.roleEnum.ADMIN && authInfo.role_id !== enums.roleEnum.RESTAURANT_OWNER && authInfo.role_id !== enums.roleEnum.DELIVERY_BOY && authInfo.role_id !== enums.roleEnum.CUSTOMER && authInfo.role_id !== enums.roleEnum.MODERATOR)"/>
</template>

<script>

import ENV from "../../../config/env";
import roleEnum from "../../../enums/modules/roleEnum";
import {useAuthStore} from "../../../stores/auth";
import LoadingComponent from "../../common/LoadingComponent.vue";
import AdminDashboardComponent from "./admin/AdminDashboardComponent.vue";
import RestaurantOwnerDashboardComponent from "./restaurantOwner/RestaurantOwnerDashboardComponent.vue";
import DeliveryBoyDashboardComponent from "./deliveryBoy/DeliveryBoyDashboardComponent.vue";
import OtherDashboardComponent from "./other/OtherDashboardComponent.vue";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";


export default {
    name: "DashboardComponent",
    components: {
        OtherDashboardComponent,
        DeliveryBoyDashboardComponent,
        LoadingComponent,
        AdminDashboardComponent,
        RestaurantOwnerDashboardComponent,
    },
    setup() {
        const authStore          = useAuthStore();
        const defaultAccessStore = useDefaultAccessStore();
        return {
            authStore,
            defaultAccessStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                roleEnum: roleEnum
            },
            demo: ENV.DEMO
        }
    },
    computed: {
        authInfo: function () {
            return this.authStore.info;
        }
    },
    methods: {
        visitorMessage: function () {
            let greet;
            let myDate = new Date();
            let hrs    = myDate.getHours();
            if (hrs < 12) {
                greet = this.$t('message.good_morning');
            } else if (hrs >= 12 && hrs <= 17) {
                greet = this.$t('message.good_afternoon');
            } else if (hrs >= 17 && hrs <= 24) {
                greet = this.$t('message.good_evening');
            }
            return greet;
        }
    }
}
</script>
