<template>
    <LoadingComponent :props="loading"/>

    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header !h-[68px] !leading-[68px]">
                <h3 class="db-card-title">{{ $t('label.order_tracker') }}</h3>
            </div>
            <div class="db-card-body">
                <form class="m-0 p-0" @submit.prevent="search">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-4">
                            <label for="order_id" class="db-field-title required">{{ $t("label.order_id") }}</label>
                            <input v-model="orderId" :class="errors.order_id ? 'invalid' : ''" v-on:keypress="floatNumber($event)" type="text" id="order_id"
                                   class="db-field-control" autocomplete="off"/>
                            <small class="db-field-alert" v-if="errors.order_id">{{ errors.order_id[0] }}</small>
                        </div>
                        <div class="form-col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-line-search lab-font-size-16"></i>
                                    <span>{{ $t('button.search') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <OrderDetailsComponent v-if="showDetails" :order="order" :orderItems="orderItems" :orderUser="orderUser" :orderRestaurant="orderRestaurant" :orderAddress="orderAddress" :orderDeliveryBoy="orderDeliveryBoy"/>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import alertService from "../../../services/alertService.js"
import OrderDetailsComponent from "../components/OrderDetailsComponent.vue"
import {useOrderTrackerStore} from "../../../stores/orderTracker";
import appService from "../../../services/appService.js";


export default {
    name: "OrderTrackerListComponent",
    components: {
        LoadingComponent,
        OrderDetailsComponent
    },
    setup() {
        const orderTrackerStore = useOrderTrackerStore();
        return {
            orderTrackerStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            orderId: "",
            showDetails: false,
            errors: {}
        };
    },
    computed: {
        order: function () {
            return this.orderTrackerStore.show;
        },
        orderItems: function () {
            return this.orderTrackerStore.orderItems;
        },
        orderUser: function () {
            return this.orderTrackerStore.orderUser;
        },
        orderAddress: function () {
            return this.orderTrackerStore.orderAddress;
        },
        orderRestaurant: function () {
            return this.orderTrackerStore.orderRestaurant;
        },
        orderDeliveryBoy: function () {
            return this.orderTrackerStore.orderDeliveryBoy;
        }
    },
    methods: {
        floatNumber: function (e) {
            return appService.floatNumber(e);
        },
        search: function () {
            this.loading.isActive = true;
            this.orderTrackerStore.fetchByOrderId({order_id: this.orderId}).then((res) => {
                this.loading.isActive = false;
                this.showDetails      = true;
                this.orderId          = "";
                this.errors           = {};
            }).catch((err) => {
                this.loading.isActive = false;
                this.showDetails      = false;
                if (err.response.data.errors) {
                    this.errors = err.response.data.errors;
                } else {
                    alertService.error(this.$t("message.order_not_found"));
                }
            })
        }
    }
}
</script>
