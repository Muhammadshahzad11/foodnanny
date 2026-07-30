<template>
    <LoadingComponent :props="loading" />
    <OrderDetailsComponent :order="order" :orderItems="orderItems" :orderUser="orderUser" :orderAddress="orderAddress" :orderRestaurant="orderRestaurant" :orderDeliveryBoy="orderDeliveryBoy" :fullStyle="true"/>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import appService from "../../../services/appService";
import OrderDetailsComponent from "../components/OrderDetailsComponent.vue";
import { useMyOrderDetailsStore } from "../../../stores/myOrderDetails"

export default {
    name: "RestaurantOwnerOrderDetailsComponent",
    components: { LoadingComponent, OrderDetailsComponent },
    setup() {
        const myOrderDetailsStore = useMyOrderDetailsStore();
        return {
            myOrderDetailsStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            }
        };
    },
    computed: {
        order: function () {
            return this.myOrderDetailsStore.orderDetails;
        },
        orderItems: function () {
            return this.myOrderDetailsStore.orderItems;
        },
        orderUser: function () {
            return this.myOrderDetailsStore.orderUser;
        },
        orderAddress: function () {
            return this.myOrderDetailsStore.orderAddress;
        },
        orderRestaurant: function () {
            return this.myOrderDetailsStore.orderRestaurant;
        },
        orderDeliveryBoy: function () {
            return this.myOrderDetailsStore.orderDeliveryBoy;
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.loading.isActive = true;
            this.myOrderDetailsStore.callOrderDetails({
                id: this.$route.params.id,
                orderId: this.$route.params.orderId
            }).then((res) => {
                this.loading.isActive = false;
            }).catch((error) => {
                this.loading.isActive = false;

            });
        }
    },
    methods: {
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        }
    }
}
</script>
