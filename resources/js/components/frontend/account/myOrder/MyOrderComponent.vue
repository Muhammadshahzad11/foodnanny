<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-8 pb-24 md:pb-16">
        <div class="container max-w-3xl">
            <router-link :to="{ name: 'frontend.home' }" class="mb-6 inline-flex items-center gap-2 text-primary">
                <i class="lab-line-undo text-xl font-semibold"></i>
                <span class="text-base font-medium">{{ $t('label.back_to_home') }} </span>
            </router-link>

            <div class="row">
                <div class="col-12 sm:col-6">
                    <h3 class="capitalize text-2xl font-medium mb-4">{{ $t('label.active_orders') }}</h3>
                    <div class="p-4 rounded-2xl shadow-xs bg-white" v-if="activeOrders.length > 0">
                        <div v-for="activeOrder in activeOrders" :key="activeOrder"
                             class="flex items-center gap-3 p-3 mb-4 last:mb-0 rounded-lg border border-gray-100">
                            <i class="lab-fill-reserve-2 flex-shrink-0 text-3xl text-paragraph/60"></i>
                            <div class="flex-auto">
                                <dl class="flex items-center gap-1 mb-1">
                                    <dt class="text-sm capitalize whitespace-nowrap text-paragraph">
                                        {{ $t("label.order_id") }}:
                                    </dt>
                                    <dd class="flex items-center gap-3">
                                        <span class="text-sm capitalize">#{{ activeOrder.order_serial_no }}</span>
                                        <span :class="orderStatusClass(activeOrder.status)">
                                            {{ enums.orderStatusEnumArray[activeOrder.status] }}
                                        </span>
                                    </dd>
                                </dl>
                                <p class="text-xs text-paragraph mb-1">{{ activeOrder.order_datetime }}</p>
                                <dl class="flex items-center gap-1 mb-1.5">
                                    <dt class="text-xs capitalize text-paragraph">{{ $t('label.from') }}:</dt>
                                    <dd class="text-xs capitalize">{{ activeOrder.restaurant_name }}</dd>
                                </dl>
                                <p class="text-sm capitalize text-[#00749B] mb-2.5">
                                    {{ enums.orderTypeEnumArray[activeOrder.order_type] }}</p>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <dl class="flex items-center gap-1">
                                        <dt class="text-sm capitalize">{{ $t("label.total") }}:</dt>
                                        <dd class="text-sm font-medium capitalize">
                                            {{ activeOrder.total_currency_price }}
                                        </dd>
                                    </dl>
                                    <router-link
                                        :to="{ name: 'frontend.myOrder.details', params: { id: activeOrder.id } }"
                                        class="inline-flex items-center gap-1 text-primary">
                                        <span class="text-xs font-medium capitalize">
                                            {{ $t("label.see_details") }}
                                        </span>
                                        <i class="lab-line-chevron-right text-sm font-semibold"></i>
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-4 rounded-2xl shadow-xs bg-white">
                        <div class="py-6">
                            <img alt="active-orders" class="w-[120px] mx-auto pb-6" :src="setting.image_active_orders">
                            <div class="text-center text-sm">{{ $t('message.no_active_orders') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 sm:col-6">
                    <h3 class="capitalize text-2xl font-medium mb-4">{{ $t('label.previous_orders') }}</h3>
                    <div v-if="previousOrders.length > 0" class="p-4 rounded-2xl shadow-xs bg-white">
                        <div v-for="previousOrder in previousOrders" :key="previousOrder"
                             class="flex items-center gap-3 p-3 mb-4 last:mb-0 rounded-lg border border-gray-100">
                            <i class="lab-fill-reserve-2 flex-shrink-0 text-3xl text-paragraph/60"></i>
                            <div class="flex-auto">
                                <dl class="flex items-center gap-1 mb-1">
                                    <dt class="text-sm capitalize whitespace-nowrap text-paragraph">
                                        {{ $t("label.order_id") }}:
                                    </dt>
                                    <dd class="flex items-center gap-3">
                                        <span class="text-sm capitalize">#{{ previousOrder.order_serial_no }}</span>
                                        <span :class="orderStatusClass(previousOrder.status)">
                                            {{ enums.orderStatusEnumArray[previousOrder.status] }}
                                        </span>
                                    </dd>
                                </dl>
                                <p class="text-xs text-paragraph mb-1">{{ previousOrder.order_datetime }}</p>
                                <dl class="flex items-center gap-1 mb-1.5">
                                    <dt class="text-xs capitalize text-paragraph">{{ $t('label.from') }}:</dt>
                                    <dd class="text-xs capitalize">{{ previousOrder.restaurant_name }}</dd>
                                </dl>
                                <p class="text-sm capitalize text-[#00749B] mb-2.5">
                                    {{ enums.orderTypeEnumArray[previousOrder.order_type] }}</p>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <dl class="flex items-center gap-1">
                                        <dt class="text-sm capitalize">{{ $t("label.total") }}:</dt>
                                        <dd class="text-sm font-medium capitalize">
                                            {{ previousOrder.total_currency_price }}
                                        </dd>
                                    </dl>
                                    <router-link
                                        :to="{ name: 'frontend.myOrder.details', params: { id: previousOrder.id } }"
                                        class="inline-flex items-center gap-1 text-primary">
                                        <span class="text-xs font-medium capitalize">
                                            {{ $t("label.see_details") }}
                                        </span>
                                        <i class="lab-line-chevron-right text-sm font-semibold"></i>
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="previousOrders.length > 0"
                         class="flex items-center justify-between border-gray-200 bg-white px-4 py-6">
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <PaginationBox :pagination="pagination" :method="previousOrderList"/>
                        </div>
                    </div>
                    <div v-else class="p-4 rounded-2xl shadow-xs bg-white">
                        <div class="py-6">
                            <img alt="active-orders" class="w-[120px] mx-auto pb-6" :src="setting.image_previous_orders">
                            <div class="text-center text-sm">{{ $t('message.not_past_orders') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div v-if="(Object.keys($route.query).length > 0 && $route.query.id > 0) && Object.keys(order).length > 0"
        id="confirm-order"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-sm w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="p-5 relative">
                <button @click.prevent="OrderModalClose('confirm-order')"
                        class="absolute top-4 ltr:right-4 rtl:left-4 leading-none">
                    <i class="lab-line-circle-cross text-danger"></i>
                </button>
                <h3 class="capitalize text-base font-medium text-center mt-2 mb-3">
                    {{ $t('message.order_thank_you') }}
                </h3>
                <img class="w-40 mx-auto mb-3" :src="setting.image_confirm" alt="gif">
                <h4 class="capitalize text-lg font-medium text-center mb-5 text-primary">
                    {{ $t('label.order_confirmed') }}
                </h4>
                <p v-if="order.table" class="mb-4 text-center text-sm text-paragraph">
                    {{ $t('label.dining_table') }}:
                    <span class="font-medium text-heading">
                        {{ order.table.name || order.table.table_number }}
                    </span>
                </p>
                <div
                    v-if="order.table"
                    class="mb-5 overflow-hidden rounded-2xl border border-primary/25 bg-gradient-to-br from-emerald-50 via-white to-amber-50 text-left"
                >
                    <div class="flex gap-3 p-3.5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary text-white">
                            <i class="lab-line-clock text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-primary">
                                {{ $t('label.cancel_policy_title') }}
                            </p>
                            <p class="mt-1 text-sm leading-5 text-heading">
                                {{ $t('message.scan_menu_cancel_window') }}
                            </p>
                        </div>
                    </div>
                </div>
                <router-link @click.prevent="OrderModalClose('confirm-order')"
                             class="w-full rounded-3xl text-center font-medium leading-6 py-3 bg-primary text-white"
                             :to="{ name: 'frontend.myOrder.details', params: { id: order.id } }">
                    {{ $t('button.go_to_order') }}
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import {useModal} from "../../../../composables/modal.js";
import orderStatusEnum from "../../../../enums/modules/orderStatusEnum.js";
import orderTypeEnum from "../../../../enums/modules/orderTypeEnum.js";
import {useFrontendOrderStore} from "../../../../stores/frontendOrder.js";
import appService from "../../../../services/appService.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import {useFrontendCartStore} from "../../../../stores/frontendCart.js";
import {useAuthStore} from "../../../../stores/auth.js";
import PaginationBox from "../../../admin/components/pagination/PaginationBox.vue";
import LoadingComponent from "../../../common/LoadingComponent.vue";

export default {
    name: "MyOrderComponent",
    components: {LoadingComponent, PaginationBox},
    setup() {
        const {openModal, closeModal} = useModal();
        const authStore               = useAuthStore();
        const frontendCartStore       = useFrontendCartStore();
        const frontendOrderStore      = useFrontendOrderStore();
        const frontendSettingStore    = useFrontendSettingStore();

        return {
            openModal,
            closeModal,
            authStore,
            frontendCartStore,
            frontendOrderStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.PREPARED]: this.$t("label.prepared"),
                    [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t("label.out_for_delivery"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                    [orderStatusEnum.RETURNED]: this.$t("label.returned"),
                },
                orderTypeEnum: orderTypeEnum,
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway"),
                    [orderTypeEnum.DINING_TABLE]: this.$t("label.dining_table")
                }
            },
            active: {
                excepts: orderStatusEnum.DELIVERED + "|" + orderStatusEnum.CANCELED + "|" + orderStatusEnum.REJECTED + "|" + orderStatusEnum.RETURNED
            },
            previous: {
                paginate: 1,
                page: 1,
                per_page: 5,
                excepts: orderStatusEnum.PENDING + "|" + orderStatusEnum.ACCEPT + "|" + orderStatusEnum.PREPARING + "|" + orderStatusEnum.PREPARED + "|" + orderStatusEnum.OUT_FOR_DELIVERY
            }
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        order: function () {
            return this.frontendOrderStore.show;
        },
        activeOrders: function () {
            return this.frontendOrderStore.activeOrder;
        },
        previousOrders: function () {
            return this.frontendOrderStore.previousOrder;
        },
        pagination: function () {
            return this.frontendOrderStore.pagination;
        },
        cart: function () {
            return this.frontendCartStore.lists;
        },
        paymentMethod: function () {
            return this.frontendCartStore.paymentMethod;
        }
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.previousOrderList();
            this.frontendOrderStore.fetchActiveOrder({
                excepts: this.active.excepts,
            }).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });

            if (Object.keys(this.$route.query).length > 0) {
                this.loading.isActive = true;
                this.frontendOrderStore.view(this.$route.query.id).then(async res => {
                    await this.$nextTick();
                    this.openModal('confirm-order');
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }
        } catch (err) {
            this.loading.isActive = false;
        }
    },
    methods: {
        OrderModalClose: function (id) {
            this.closeModal(id);
            if (this.cart.length > 0 && Object.keys(this.paymentMethod).length > 0 && this.paymentMethod.slug === 'credit') {
                this.authStore.profile().then().catch();
            }
            this.frontendCartStore.callResetCart();
        },
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        },
        previousOrderList: function (page = 1) {
            this.loading.isActive = true;
            this.previous.page    = page;
            this.frontendOrderStore.fetchPreviousOrder(this.previous).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            })
        }
    }
}
</script>
