<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="flex flex-col items-start sm:flex-row sm:items-center gap-1.5 mb-6">
            <button type="button" class="tab-active tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10" @click="handleTab($event, 'information')">
                <i class="lab lab-line-info-circle lab-font-size-16"></i>
                {{ $t('label.information') }}
            </button>
            <button type="button" class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10" @click="handleTab($event, 'translations')">
                <i class="lab lab-line-language lab-font-size-16"></i>
                {{ $t('label.translations') }}
            </button>
        </div>
        <div class="tab-content db-card tab-active" id="information">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t('label.voucher') }}</h3>
            </div>
            <div class="db-card-body">
                <div class="row py-2">
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.name') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ voucher.name }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.code') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ voucher.code }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.type') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ enums.discountEnumArray[voucher.type]}}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.discount') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ voucher.flat_discount }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.discount_type') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2"> {{ enums.taxTypeEnumArray[voucher.discount_type] }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.start_date') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ voucher.convert_start_date }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.end_date') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ voucher.convert_end_date }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.minimum_order') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ voucher.minimum_order_flat_amount}}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.maximum_discount') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2">{{ voucher.maximum_flat_discount }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.limit_per_user') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2" v-if="voucher.limit_per_user == 0">{{$t('label.unlimited') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2" v-else>{{ voucher.limit_per_user }}</span>
                        </div>
                    </div>
                    <div class="col-12 sm:col-6 !py-1.5">
                        <div class="db-list-item p-0" v-if="voucher.description">
                            <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.description') }}</span>
                            <span class="db-list-item-text w-full sm:w-1/2" v-html="voucher.description"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="tab-content db-card" id="translations">
            <VoucherTranslationComponent />
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import taxTypeEnum from "../../../enums/modules/taxTypeEnum.js";
import discountEnum from "../../../enums/modules/discountEnum.js";
import {useVoucherStore} from "../../../stores/voucher.js";
import { useTab } from "../../../composables/tab.js";
import VoucherTranslationComponent from "./VoucherTranslationComponent.vue";

export default {
    name: "VoucherShowComponent",
    components: {
        LoadingComponent,
        VoucherTranslationComponent
    },
    setup() {
        const voucherStore = useVoucherStore();
        return {
            voucherStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                taxTypeEnum: taxTypeEnum,
                discountEnum: discountEnum,
                taxTypeEnumArray: {
                    [taxTypeEnum.FIXED]: this.$t("label.fixed"),
                    [taxTypeEnum.PERCENTAGE]: this.$t("label.percentage")
                },
                discountEnumArray: {
                    [discountEnum.DEFAULT]: this.$t("label.default"),
                    [discountEnum.FREE_DELIVERY]: this.$t("label.free_delivery")
                }
            },
            handleTab: useTab().handleTab,
        }
    },
    computed: {
        voucher: function () {
            return this.voucherStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.voucherStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    }
}
</script>
