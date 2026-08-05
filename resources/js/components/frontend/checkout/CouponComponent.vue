<template>
    <div v-if="Object.keys(cartCoupon).length !== 0"
         class="mb-4 w-full flex items-center gap-3.5 p-3 rounded-xl shadow-xs border border-green-300 bg-white">
        <i class="lab-fill-discount text-3xl text-green-500"></i>
        <dl class="flex-auto ltr:text-left rtl:text-right">
            <dt class="text-sm font-medium mb-1 text-green-500">
                {{ $t('label.offer_applied') }}
            </dt>
            <dd class="text-xs">
                {{
                    Object.keys(this.cartCoupon).length > 0 && this.cartCoupon.type === discountEnum.FREE_DELIVERY ? $t('message.you_got_free_delivery') : $t('message.you_saved', {amount: setting.site_default_currency_symbol + '' + discount.toFixed(2)})
                }}
            </dd>
        </dl>
        <button @click.prevent="destroyCoupon" class="lab-line-trash text-xl text-red-500"></button>
    </div>

    <button v-else @click.prevent="couponModalShow('coupon-modal')"
            class="mb-4 w-full flex items-center gap-3.5 p-3 rounded-xl shadow-xs transition-all duration-300 border border-gray-100 bg-white hover:border-cyan-600/20">
        <i class="lab-fill-discount text-3xl text-cyan-600"></i>
        <dl class="flex-auto ltr:text-left rtl:text-right">
            <dt class="text-sm font-medium mb-1 text-cyan-600">{{ $t('label.select_apply') }}</dt>
            <dd class="text-xs">{{ $t('message.get_discount') }}</dd>
        </dl>
        <i :class="displayMode === 'ltr' ? 'lab-line-chevron-right' : 'lab-line-chevron-left'"
           class="text-2xl text-cyan-600"></i>
    </button>

    <div id="coupon-modal"
         class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-sm w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="flex items-center justify-between gap-4 py-3.5 px-4 border-b border-slate-100">
                <h3 class="text-lg font-semibold capitalize">{{ $t('label.available_coupons') }}</h3>
                <button @click.prevent="couponModalHide('coupon-modal')"
                        class="lab-line-circle-cross text-lg text-danger"></button>
            </div>

            <div class="p-4">
                <div>
                    <form @submit.prevent="couponChecking" :class="error ? '' : 'mb-6'" class="flex w-full h-11">
                        <input :class="error ? 'invalid' : ''" v-model="couponProps.form.code" type="text"
                               :placeholder="$t('label.enter_coupon_code')"
                               class="w-full px-3 ltr:rounded-l-lg rtl:rounded-r-lg placeholder:text-sm placeholder:text-gray-400 border border-gray-200">
                        <button
                            class="px-5 w-fit h-full flex-shrink-0 ltr:rounded-r-lg rtl:rounded-l-lg font-medium capitalize text-white bg-primary">
                            {{ $t('button.apply') }}
                        </button>
                    </form>
                </div>

                <div v-if="error" :class="error ? 'mb-6' : ''" class="block">
                    <small class="db-field-alert" v-if="error">{{ error }}</small>
                </div>

                <div v-for="coupon in coupons" :key="coupon.id" class="relative rounded-lg shadow-xs mb-4 last:mb-0 border border-gray-100 bg-white">
                    <button @click.prevent="addCouponButton(coupon)"
                            class="absolute top-0 ltr:right-0 rtl:left-0 text-xs py-1 px-2 ltr:rounded-tr-lg ltr:rounded-bl-lg rtl:rounded-tl-lg rtl:rounded-br-lg capitalize text-white bg-primary">
                        {{ $t('button.apply') }}
                    </button>

                    <div class="p-2 border-b border-gray-100">
                        <h3 class="py-1 px-2 rounded-md font-medium text-xs w-fit mb-2 bg-yellow-300">
                            {{ $t('label.code') }}: <span class="uppercase">{{ coupon.code }} </span>
                        </h3>
                        <h4 class="text-xs" v-if="coupon.type !== discountEnum.FREE_DELIVERY">
                            {{
                                coupon.minimum_order > 0 ? $t('message.discount_off_above', {
                                    discount: coupon.discount_alt,
                                    min_order: coupon.minimum_order_currency_amount
                                }) : $t('message.discount_off', {
                                    discount: coupon.discount_alt
                                })
                            }}
                        </h4>
                        <h4 class="text-xs" v-else>
                            {{ $t('message.get_free_delivery') }}
                        </h4>
                    </div>

                    <div class="editor-show-design" v-if="coupon.description">
                        <button @click.prevent="termStatus(coupon.id)" class="text-xs px-2 py-1.5 text-primary">
                            {{ $t('label.terms_and_conditions') }}
                        </button>
                        <div :id="'terms-' + coupon.id"
                             class="h-0 overflow-hidden transition text-xs ltr:pl-2 rtl:pr-2 leading-5" v-html="coupon.description"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {useModal} from "../../../composables/modal.js";
import discountEnum from "../../../enums/modules/discountEnum.js";
import taxTypeEnum from "../../../enums/modules/taxTypeEnum.js";
import {useFrontendCouponStore} from "../../../stores/frontendCoupon.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import {useCommonStore} from "../../../stores/common.js";
import displayModeEnum from "../../../enums/modules/displayModeEnum.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import alertService from "../../../services/alertService.js";

export default {
    name: "CouponComponent",
    props: {
        props: Object,
        coupon: Function,
    },
    setup() {
        const {openModal, closeModal} = useModal();
        const commonStore             = useCommonStore();
        const frontendCartStore       = useFrontendCartStore();
        const frontendCouponStore     = useFrontendCouponStore();
        const frontendSettingStore    = useFrontendSettingStore();

        return {
            openModal,
            closeModal,
            commonStore,
            frontendCartStore,
            frontendCouponStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            discountEnum: discountEnum,
            taxTypeEnum: taxTypeEnum,
            couponProps: {
                form: {
                    code: null
                }
            },
            error: ""
        }
    },
    computed: {
        displayMode: function () {
            return this.commonStore.display_mode === displayModeEnum.LTR ? 'ltr' : 'rtl';
        },
        restaurant: function () {
            return this.frontendCartStore.restaurant;
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        coupons: function () {
            return this.frontendCouponStore.lists;
        },
        cartCoupon: function () {
            return this.frontendCartStore.coupon;
        },
        discount: function () {
            return this.frontendCartStore.discount;
        }
    },
    mounted() {
        this.frontendCouponStore.fetch(this.restaurant.id);
    },
    methods: {
        couponModalShow: function (id) {
            this.error                 = "";
            this.couponProps.form.code = null;
            this.openModal(id);
        },
        couponModalHide: function (id) {
            this.closeModal(id);
        },
        termStatus: function (id) {
            const element = document.getElementById('terms-' + id);
            if (element.classList.contains('h-0')) {
                element.classList.remove('h-0');
            } else {
                element.classList.add('h-0');
            }
        },
        addCouponButton(coupon) {
            this.couponProps.form.code = coupon.code;
        },
        couponChecking() {
            this.frontendCouponStore.checking({
                total: this.props.total,
                code: this.couponProps.form.code,
                restaurant_id: this.restaurant.id
            }).then(async res => {
                this.error = "";
                this.coupon(res.data.data);
                await this.frontendCartStore.setCoupon(res.data.data);
                this.couponModalHide('coupon-modal');
                alertService.success(this.$t('message.coupon_add'));
            }).catch((err) => {
                this.error = err.response.data.message;
            });
        },
        destroyCoupon() {
            this.frontendCartStore.callDestroyCoupon();
            this.coupon({});
            alertService.success(this.$t('message.coupon_delete'));
        }
    }
}
</script>
