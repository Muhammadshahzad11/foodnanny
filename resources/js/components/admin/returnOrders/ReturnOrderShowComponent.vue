<template>
    <LoadingComponent :props="loading"/>
    <OrderDetailsComponent :order="order" :orderItems="orderItems" :orderRestaurant="orderRestaurant" :orderDeliveryBoy="orderDeliveryBoy" :orderUser="orderUser" :orderAddress="orderAddress"/>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import OrderDetailsComponent from "../components/OrderDetailsComponent.vue";
import { useReturnOrderStore } from "../../../stores/returnOrder.js";

export default {
    name: "ReturnOrderShowComponent",
    components: {
        LoadingComponent, OrderDetailsComponent
    },
    setup() {
        const returnOrderStore = useReturnOrderStore();
        return {
            returnOrderStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            }
        }
    },
    computed: {
        order: function () {
            return this.returnOrderStore.show;
        },
        orderItems: function () {
            return this.returnOrderStore.orderItems;
        },
        orderRestaurant: function () {
            return this.returnOrderStore.orderRestaurant;
        },
        orderUser: function () {
            return this.returnOrderStore.orderUser;
        },
        orderAddress: function () {
            return this.returnOrderStore.orderAddress;
        },
        orderDeliveryBoy: function () {
            return this.returnOrderStore.orderDeliveryBoy;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.returnOrderStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    }
};
</script>
