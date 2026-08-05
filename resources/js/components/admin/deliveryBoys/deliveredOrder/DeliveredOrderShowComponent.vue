<template>
    <LoadingComponent :props="loading" />
    <OrderDetailsComponent :order="order" :orderItems="orderItems" :orderUser="orderUser" :orderAddress="orderAddress" :orderRestaurant="orderRestaurant" :orderDeliveryBoy="orderDeliveryBoy" :fullStyle="true"/>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import OrderDetailsComponent from "../../components/OrderDetailsComponent.vue";
import { useDeliveryBoyOrderStore } from "../../../../stores/deliveryBoyOrder.js";

export default {
    name: "DeliveredOrderShowComponent",
    components: {
        LoadingComponent,
        OrderDetailsComponent
    },
    setup() {
        const deliveryBoyOrderStore = useDeliveryBoyOrderStore();
        return {
            deliveryBoyOrderStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            }
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.loading.isActive = true;
            this.deliveryBoyOrderStore.callDeliveredOrderDetails({
                id: this.$route.params.id,
                orderId: this.$route.params.orderId
            }).then((res) => {
                this.loading.isActive = false;
            }).catch((error) => {
                this.loading.isActive = false;
            });
        }
    },
    computed: {
        orderUser: function () {
            return this.deliveryBoyOrderStore.orderUser;
        },
        order: function () {
            return this.deliveryBoyOrderStore.deliveredOrderDetails;
        },
        orderItems: function () {
            return this.deliveryBoyOrderStore.orderItems;
        },
        orderAddress: function () {
            return this.deliveryBoyOrderStore.orderAddress;
        },
        orderRestaurant: function () {
            return this.deliveryBoyOrderStore.orderRestaurant;
        },
        orderDeliveryBoy: function () {
            return this.deliveryBoyOrderStore.orderDeliveryBoy;
        }
    }
}
</script>
