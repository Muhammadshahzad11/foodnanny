<template>
    <LoadingComponent :props="loading"/>
    <OrderDetailsComponent :order="order" :orderItems="orderItems" :orderUser="orderUser" :orderRestaurant="orderRestaurant" :orderAddress="orderAddress" :orderDeliveryBoy="orderDeliveryBoy">
        <div class="flex flex-wrap gap-3" v-if="order.status === enums.orderStatusEnum.PENDING">
            <ReasonComponent/>
            <button type="button" @click="accept"
                    class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                <i class="lab lab-fill-save"></i>
                <span class="text-sm capitalize text-white">{{ $t('button.accept') }}</span>
            </button>
        </div>
        <div class="flex flex-wrap gap-3" v-else-if="order.status !== enums.orderStatusEnum.REJECTED && order.status !== enums.orderStatusEnum.CANCELED">

            <div v-if="!order.token && order.order_type === enums.orderTypeEnum.TAKEAWAY && ![enums.orderStatusEnum.DELIVERED, enums.orderStatusEnum.CANCELED, enums.orderStatusEnum.REJECTED, enums.orderStatusEnum.RETURNED].includes(order.status)">
                <OnlineOrderTokenComponent />
            </div>

            <button type="button" v-if="order.status === enums.orderStatusEnum.ACCEPT" @click="preparing"
                    class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                <i class="lab lab-fill-preparing"></i>
                <span class="text-sm capitalize text-white">{{ $t('label.preparing') }}</span>
            </button>

            <button type="button" v-if="order.status === enums.orderStatusEnum.PREPARING" @click="prepared"
                    class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                <i class="lab lab-fill-reserve"></i>
                <span class="text-sm capitalize text-white">{{ $t('label.prepared') }}</span>
            </button>

            <button type="button"
                    v-if="order.order_type === enums.orderTypeEnum.TAKEAWAY && order.status === enums.orderStatusEnum.PREPARED"
                    @click="delivered"
                    class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                <i class="lab lab-fill-delivered"></i>
                <span class="text-sm capitalize text-white">{{ $t('button.confirm_takeaway') }}</span>
            </button>
            <ReceiptComponent :order="order" :orderItems="orderItems" :orderUser="orderUser" :orderAddress="orderAddress" :orderRestaurant="orderRestaurant"/>
        </div>
    </OrderDetailsComponent>
</template>

<script>
import OrderDetailsComponent from "../components/OrderDetailsComponent.vue";
import ReceiptComponent from "../components/order/ReceiptComponent.vue";
import {useOnlineOrderStore} from "../../../stores/onlineOrder.js";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import ReasonComponent from "../components/order/ReasonComponent.vue";
import alertService from "../../../services/alertService.js";
import LoadingComponent from "../../common/LoadingComponent.vue";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import VueSimpleAlert from "vue3-simple-alert";
import paymentTypeEnum from "../../../enums/modules/paymentTypeEnum.js";
import OnlineOrderTokenComponent from "./OnlineOrderTokenComponent.vue";

export default {
    name: "OnlineOrderShowComponent",
    components: {
        LoadingComponent, OrderDetailsComponent, ReasonComponent, ReceiptComponent, OnlineOrderTokenComponent
    },
    setup() {
        const onlineOrderStore = useOnlineOrderStore();

        return {
            onlineOrderStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                orderStatusEnum: orderStatusEnum,
                orderTypeEnum: orderTypeEnum,
                paymentTypeEnum: paymentTypeEnum
            }
        }
    },
    computed: {
        order: function () {
            return this.onlineOrderStore.show;
        },
        orderItems: function () {
            return this.onlineOrderStore.orderItems;
        },
        orderUser: function () {
            return this.onlineOrderStore.orderUser;
        },
        orderAddress: function () {
            return this.onlineOrderStore.orderAddress;
        },
        orderRestaurant: function () {
            return this.onlineOrderStore.orderRestaurant;
        },
        orderDeliveryBoy: function () {
            return this.onlineOrderStore.orderDeliveryBoy;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.onlineOrderStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        accept: function () {
            return new VueSimpleAlert.confirm(
                this.$t('message.cancel_order'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_accept'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then((res) => {
                try {
                    this.loading.isActive = true;
                    this.onlineOrderStore.changeStatus({
                        id: this.$route.params.id,
                        status: this.enums.orderStatusEnum.ACCEPT
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("label.status"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        preparing: function () {
            return new VueSimpleAlert.confirm(
                this.$t('message.prepare_order'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_preparing'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(res => {
                try {
                    this.loading.isActive = true;
                    this.onlineOrderStore.changeStatus({
                        id: this.$route.params.id,
                        status: this.enums.orderStatusEnum.PREPARING
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("label.status"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                    });
                } catch (err) {
                    this.loading.isActive = false;
                }
            }).catch((err) => {
            })
        },
        prepared: function () {
            return new VueSimpleAlert.confirm(
                this.$t('message.prepared_order'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_prepared'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(res => {
                try {
                    this.loading.isActive = true;
                    this.onlineOrderStore.changeStatus({
                        id: this.$route.params.id,
                        status: this.enums.orderStatusEnum.PREPARED
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("label.status"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                    });
                } catch (err) {
                    this.loading.isActive = false;
                }
            }).catch((err) => {
            })
        },
        delivered: function () {
            return new VueSimpleAlert.confirm(
                this.order.payment_method === this.enums.paymentTypeEnum.CASH_ON_DELIVERY ? this.$t('message.collect_the_fee', {amount : this.order.total_currency_price}) : this.$t('message.complete_order'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.order.payment_method === this.enums.paymentTypeEnum.CASH_ON_DELIVERY ? this.$t('button.yes_collected') : this.$t('button.yes_delivered'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(res => {
                try {
                    this.loading.isActive = true;
                    this.onlineOrderStore.changeStatus({
                        id: this.$route.params.id,
                        status: this.enums.orderStatusEnum.DELIVERED
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("label.status"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                    });
                } catch (err) {
                    this.loading.isActive = false;
                }
            }).catch((err) => {
            })
        }
    }
}
</script>
