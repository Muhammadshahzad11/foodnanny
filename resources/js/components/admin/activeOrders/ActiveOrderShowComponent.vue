<template>
    <LoadingComponent :props="loading" />
    <OrderDetailsComponent :fullStyle="false" :order="order" :orderItems="orderItems" :orderUser="orderUser"  :orderRestaurant="orderRestaurant"  :orderAddress="orderAddress">
        <div class="db-card p-4 h-full overflow-hidden">
            <div class="relative h-full">
                <ul class="w-full flex items-center mb-4">
                    <li class="group w-full last:w-fit flex items-center">
                        <i :class="parseInt(enums.orderStatusEnum.PREPARING) <= parseInt(order.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'"
                           class="lab lab-fill-preparing flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                        <hr :class="parseInt(enums.orderStatusEnum.PREPARING) <= parseInt(order.status) ? 'bg-primary' : 'bg-[#FFEBDD]'"
                            class="flex-auto w-full h-1 border-0 group-last:hidden"/>
                    </li>
                    <li class="group w-full last:w-fit flex items-center">
                        <i :class="parseInt(enums.orderStatusEnum.PREPARED) <= parseInt(order.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'"
                           class="lab lab-fill-reserve flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                        <hr :class="parseInt(enums.orderStatusEnum.PREPARED) <= parseInt(order.status) ? 'bg-primary' : 'bg-[#FFEBDD]'"
                            class="flex-auto w-full h-1 border-0 group-last:hidden"/>
                    </li>
                    <li class="group w-full last:w-fit flex items-center">
                        <i :class="parseInt(enums.orderStatusEnum.OUT_FOR_DELIVERY) <= parseInt(order.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'"
                           class="lab lab-fill-on-the-way flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                        <hr :class="parseInt(enums.orderStatusEnum.OUT_FOR_DELIVERY) <= parseInt(order.status) ? 'bg-primary' : 'bg-[#FFEBDD]'"
                            class="flex-auto w-full h-1 border-0 group-last:hidden"/>
                    </li>
                    <li class="group w-full last:w-fit flex items-center">
                        <i :class="parseInt(enums.orderStatusEnum.DELIVERED) <= parseInt(order.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'"
                           class="lab lab-fill-delivered flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                        <hr :class="parseInt(enums.orderStatusEnum.DELIVERED) <= parseInt(order.status) ? 'bg-primary' : 'bg-[#FFEBDD]'"
                            class="flex-auto w-full h-1 border-0 group-last:hidden"/>
                    </li>
                </ul>
                <p class="text-sm text-start pb-[50px]">
                    {{ enums.message[order.status] }}
                </p>

                <div class="absolute ltr:right-0 rtl:left-0 bottom-0 flex flex-wrap gap-3 items-end" v-if="order.status === enums.orderStatusEnum.PREPARED && order.is_received === enums.askEnum.NO">
                    <button type="button" @click="receivedItem" class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                        <i class="lab lab-fill-item-hand"></i>
                        <span class="text-sm capitalize text-white">{{ $t('button.order_received') }}</span>
                    </button>
                </div>

                <div class="absolute ltr:right-0 rtl:left-0 bottom-0 flex flex-wrap gap-3" v-if="order.order_type === enums.orderTypeEnum.DELIVERY && order.status === enums.orderStatusEnum.OUT_FOR_DELIVERY">
                    <button type="button" @click="changeStatus" class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                        <i class="lab lab-fill-delivered"></i>
                        <span class="text-sm capitalize text-white">{{ $t('button.confirm_delivery') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </OrderDetailsComponent>
</template>

<script>


import OrderDetailsComponent from "../components/OrderDetailsComponent.vue";
import {useActiveOrderStore} from "../../../stores/activeOrder.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import VueSimpleAlert from "vue3-simple-alert";
import askEnum from "../../../enums/modules/askEnum.js";
import alertService from "../../../services/alertService.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum.js";
import paymentTypeEnum from "../../../enums/modules/paymentTypeEnum.js";
import LoadingComponent from "../../common/LoadingComponent.vue";

export default {
    name: "ActiveOrderShowComponent",
    components: {
        LoadingComponent,
        OrderDetailsComponent
    },
    setup() {
        const activeOrderStore = useActiveOrderStore();

        return {
            activeOrderStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                orderTypeEnum: orderTypeEnum,
                orderStatusEnum: orderStatusEnum,
                paymentStatusEnum: paymentStatusEnum,
                paymentTypeEnum: paymentTypeEnum,
                askEnum: askEnum,
                message: {
                    [orderStatusEnum.PREPARING]: this.$t("message.delivery_boy_preparing_order"),
                    [orderStatusEnum.PREPARED]: this.$t("message.delivery_boy_prepared_order"),
                    [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t("message.delivery_boy_out_for_delivery_order"),
                    [orderStatusEnum.DELIVERED]: this.$t("message.delivery_boy_delivered_order"),
                    [orderStatusEnum.RETURNED]: this.$t("message.order_has_been_returned"),
                }
            }
        }
    },
    computed: {
        order: function () {
            return this.activeOrderStore.show;
        },
        orderItems: function () {
            return this.activeOrderStore.orderItems;
        },
        orderUser: function () {
            return this.activeOrderStore.orderUser;
        },
        orderAddress: function () {
            return this.activeOrderStore.orderAddress;
        },
        orderRestaurant: function () {
            return this.activeOrderStore.orderRestaurant;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.activeOrderStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        receivedItem: function () {
            return new VueSimpleAlert.confirm(
                "Received the item from the restaurant!",
                "Are you sure?",
                "warning",
                {
                    confirmButtonText: "Yes, Received it!",
                    cancelButtonText: "No, Cancel!",
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C",
                }
            ).then(res => {
                try {
                    this.loading.isActive = true;
                    this.activeOrderStore.receivedStatus(this.$route.params.id).then((res) => {
                        this.loading.isActive = false;
                        alertService.success(this.$t("message.item_received_successfully"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                    });
                } catch (err) {
                    this.loading.isActive = false;
                }
            }).catch((err) => {
            });
        },
        changeStatus: function () {
            return new VueSimpleAlert.confirm(
                this.order.payment_method === this.enums.paymentTypeEnum.CASH_ON_DELIVERY ? "Collected "+ this.order.total_currency_price +" for this order fee!" : "This order is complete!",
                "Are you sure?",
                "warning",
                {
                    confirmButtonText: this.order.payment_method === this.enums.paymentTypeEnum.CASH_ON_DELIVERY ? "Yes, Collected!" : "Yes, Delivered it!",
                    cancelButtonText: "No, Cancel!",
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C",
                }
            ).then(res => {
                try {
                    this.loading.isActive = true;
                    this.activeOrderStore.changeStatus(this.$route.params.id).then((res) => {
                        this.loading.isActive = false;
                        alertService.success(this.$t("message.delivered_successfully"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch(err => {
            })
        }
    }
}
</script>
