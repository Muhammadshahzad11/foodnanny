<template>
    <LoadingComponent :props="loading"/>
    <OrderDetailsComponent :order="order" :orderItems="orderItems" :orderUser="orderUser" :orderRestaurant="{}" :orderAddress="{}">
        <div class="flex flex-wrap gap-3" v-if="order.status !== enums.orderStatusEnum.REJECTED && order.status !== enums.orderStatusEnum.CANCELED">
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

            <button type="button" v-if="order.order_type === enums.orderTypeEnum.POS && order.status === enums.orderStatusEnum.PREPARED" @click="delivered" class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                <i class="lab lab-fill-delivered"></i>
                <span class="text-sm capitalize text-white">{{ $t('button.confirm_delivery') }}</span>
            </button>

            <button type="button" v-print="printObj"
                    class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-primary">
                <i class="lab lab-fill-printer lab-font-size-16 text-white"></i>
                <span class="text-sm capitalize text-white"> {{ $t('button.print_invoice') }}</span>
            </button>

            <PosReceiptComponent :order="order" />
        </div>

    </OrderDetailsComponent>
</template>
<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import {usePosOrderStore} from "../../../stores/posOrder.js";
import OrderDetailsComponent from "../components/OrderDetailsComponent.vue";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import VueSimpleAlert from "vue3-simple-alert";
import alertService from "../../../services/alertService.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import print from "vue3-print-nb";
import PosReceiptComponent from "../components/order/PosReceiptComponent.vue";


export default {
    name: "PosOrderShowComponent",
    components: {PosReceiptComponent, OrderDetailsComponent, LoadingComponent},
    directives: {
        print
    },
    setup() {
        const posOrderStore = usePosOrderStore();

        return {
            posOrderStore
        }
    },

    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                orderStatusEnum: orderStatusEnum,
                orderTypeEnum: orderTypeEnum
            },
            printObj: {
                id: "print",
                popTitle: this.$t("menu.order_receipt"),
            },
        }
    },
    computed: {
        order: function () {
            return this.posOrderStore.show;
        },
        orderItems: function () {
            return this.posOrderStore.orderItems;
        },
        orderUser: function () {
            return this.posOrderStore.orderUser;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.posOrderStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
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
                    this.posOrderStore.changeStatus({
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
                    this.posOrderStore.changeStatus({
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
                this.$t('message.collect_the_fee', {amount : this.order.total_currency_price}),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_collected'),
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(res => {
                try {
                    this.loading.isActive = true;
                    this.posOrderStore.changeStatus({
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
