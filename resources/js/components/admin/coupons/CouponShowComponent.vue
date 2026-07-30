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
                <h3 class="db-card-title">{{ $t('label.coupon') }}</h3>
            </div>
            <div class="db-card-body">
                <div class="row">
                    <div class="col-12 sm:col-7 md:pl-8">
                        <ul class="db-list single">
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.name') }}</span>
                                <span class="db-list-item-text">{{ coupon.name }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.code') }}</span>
                                <span class="db-list-item-text">{{ coupon.code }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.discount') }}</span>
                                <span class="db-list-item-text">{{ coupon.flat_discount }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.discount_type') }}</span>
                                <span class="db-list-item-text">
                                    {{ enums.taxTypeEnumArray[coupon.discount_type] }}
                                </span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.start_date') }}</span>
                                <span class="db-list-item-text">{{ coupon.convert_start_date }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.end_date') }}</span>
                                <span class="db-list-item-text">{{ coupon.convert_end_date }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.minimum_order') }}</span>
                                <span class="db-list-item-text">{{ coupon.minimum_order_flat_amount }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.maximum_discount') }}</span>
                                <span class="db-list-item-text">{{ coupon.maximum_flat_discount }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.limit_per_user') }}</span>
                                <span class="db-list-item-text" v-if="coupon.limit_per_user == 0">{{
                                    $t('label.unlimited')
                                    }}</span>
                                <span class="db-list-item-text" v-else>{{ coupon.limit_per_user }}</span>
                            </li>
                            <li class="db-list-item !items-start">
                                <span class="db-list-item-title">{{ $t('label.description') }}</span>
                                <span v-html="coupon.description"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="tab-content db-card" id="translations">
            <CouponTranslationComponent />
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import taxTypeEnum from "../../../enums/modules/taxTypeEnum.js";
import { useCouponStore } from "../../../stores/coupon.js";
import { useTab } from "../../../composables/tab.js";
import CouponTranslationComponent from "./CouponTranslationComponent.vue";

export default {
    name: "CouponShowComponent",
    components: {
        LoadingComponent,
        CouponTranslationComponent
    },
    setup() {
        const couponStore = useCouponStore();
        return {
            couponStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                taxTypeEnum: taxTypeEnum,
                taxTypeEnumArray: {
                    [taxTypeEnum.FIXED]: this.$t("label.fixed"),
                    [taxTypeEnum.PERCENTAGE]: this.$t("label.percentage")
                }
            },
            handleTab: useTab().handleTab,
        }
    },
    computed: {
        coupon: function () {
            return this.couponStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.couponStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    }
}
</script>
