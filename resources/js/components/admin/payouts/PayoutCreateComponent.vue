<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="row">
            <div class="col-12 md:col-8">
                <div class="rounded-lg bg-white shadow-db-card">
                    <h3 class="text-base font-medium capitalize text-paragraph py-5 px-4 border-b border-[#e5e7eb]">
                        {{ $t('menu.payouts') }}
                    </h3>
                    <form @submit.prevent="save">
                        <div class="form-row p-4">
                            <div class="form-col-12 sm:form-col-6">
                                <label class="db-field-title required" for="restaurant">
                                    {{ $t("label.type") }}
                                </label>
                                <div class="db-field-radio-group">
                                    <div class="db-field-radio">
                                        <div class="custom-radio">
                                            <input @click="callRestaurant" :value="enums.modelTypeEnum.RESTAURANT"
                                                   v-model="props.form.type" id="restaurant" type="radio"
                                                   class="custom-radio-field"/>
                                            <span class="custom-radio-span"></span>
                                        </div>
                                        <label for="restaurant" class="db-field-label">
                                            {{ $t("label.restaurant") }}
                                        </label>
                                    </div>
                                    <div class="db-field-radio">
                                        <div class="custom-radio">
                                            <input @click="callDeliveryBoy" :value="enums.modelTypeEnum.DELIVERY_BOY"
                                                   v-model="props.form.type" type="radio" id="delivery_boy"
                                                   class="custom-radio-field"/>
                                            <span class="custom-radio-span"></span>
                                        </div>
                                        <label for="delivery_boy" class="db-field-label">
                                            {{ $t("label.delivery_boy") }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-col-12 sm:form-col-6">
                                <label for="searchRestaurantId" class="db-field-title required">
                                    {{
                                        props.form.type === enums.modelTypeEnum.RESTAURANT ? $t("label.restaurant") : $t("label.delivery_boy")
                                    }}
                                </label>
                                <vue-select @update:modelValue="checkModel($event)"
                                            v-bind:class="errors.model_id ? 'invalid' : ''"
                                            class="db-field-control f-b-custom-select" id="searchRestaurantId"
                                            v-model="props.form.model_id" :options="models" label-by="name_email"
                                            value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                            placeholder="--" search-placeholder="--"/>
                                <small class="db-field-alert" v-if="errors.model_id">{{ errors.model_id[0] }}</small>
                            </div>

                            <div class="form-col-12 sm:form-col-6">
                                <label for="date" class="db-field-title required">{{ $t("label.date") }}</label>
                                <Datepicker :hideInputIcon="true" autoApply v-model="selectedDate"
                                            :enableTimePicker="true" :is24="false" :monthChangeOnScroll="false"
                                            utc="false" :input-class-name="errors.date ? 'invalid' : ''">
                                    <template #am-pm-button="{ toggle, value }">
                                        <button @click.prevent="toggle">{{ value }}</button>
                                    </template>
                                </Datepicker>
                                <small class="db-field-alert" v-if="errors.date">{{ errors.date[0] }}</small>
                            </div>

                            <div class="form-col-12 sm:form-col-6">
                                <label for="amount" class="db-field-title required">
                                    {{ $t("label.amount") }}
                                </label>
                                <input v-on:keyup="checkAmount($event)" v-on:keypress="floatNumber($event)"
                                       v-model="props.form.amount" v-bind:class="errors.amount ? 'invalid' : ''"
                                       type="text" id="amount" class="db-field-control" autocomplete="off"/>
                                <small class="db-field-alert" v-if="errors.amount">{{ errors.amount[0] }}</small>
                            </div>
                            <div class="form-col-12">
                                <div class="flex flex-wrap gap-3 mt-4">
                                    <button type="submit" class="db-btn py-2 text-white bg-primary">
                                        <i class="lab lab-fill-save text-base"></i>
                                        <span>{{ $t("label.save") }}</span>
                                    </button>
                                    <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                        <i class="lab lab-fill-close-circle text-base"></i>
                                        <span>{{ $t("button.close") }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-12 md:col-4" v-if=" Object.keys(info).length > 0">
                <div class="rounded-lg overflow-hidden bg-white shadow-db-card">
                    <img :src="setting.profile_cover" alt="profile cover" class="w-full block">
                    <div class="px-6 pb-4 -mt-11">
                        <img :src="info.image" alt="profile avatar" class="w-20 h-20 object-cover rounded-full">
                        <h3 class="text-lg font-semibold capitalize text-heading mb-2">{{ info.name }}</h3>
                        <h4 class="text-sm font-medium text-heading mb-1" v-if="info.email">
                            {{ info.email }}
                        </h4>
                        <h5 class="text-sm font-medium text-heading" v-if="info.phone">
                            {{ info.country_code }}{{ info.phone }}
                        </h5>
                    </div>
                    <ul class="w-full flex flex-col gap-4 pt-4 px-6 pb-6 border-t border-gray-100">
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-sm ltr:text-left rtl: text-right capitalize text-paragraph">
                                {{ $t('label.balance') }}
                            </span>
                            <span class="text-sm ltr:text-right rtl:text-left font-medium capitalize text-heading">
                                {{ info.balance }}
                            </span>
                        </li>
                        <li v-if="props.form.type === enums.modelTypeEnum.DELIVERY_BOY"
                            class="flex items-center justify-between gap-2">
                            <span class="text-sm ltr:text-left rtl: text-right capitalize text-paragraph">
                                {{ $t('label.username') }}
                            </span>
                            <span class="text-sm ltr:text-right rtl:text-left font-medium text-heading">
                                {{ info.username }}
                            </span>
                        </li>
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-sm ltr:text-left rtl: text-right capitalize text-paragraph">
                                {{ $t('label.address') }}
                            </span>
                            <span v-if="info.address" class="text-sm ltr:text-right rtl:text-left font-medium text-heading">
                                {{ info.address }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import modelTypeEnum from "../../../enums/modules/modelTypeEnum.js";
import {useRestaurantStore} from "../../../stores/restaurant.js";
import {useDeliveryBoyStore} from "../../../stores/deliveryBoy.js";
import Datepicker from "@vuepic/vue-datepicker";
import LoadingComponent from "../../common/LoadingComponent.vue";
import {usePayoutStore} from "../../../stores/payout.js";
import appService from "../../../services/appService.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import alertService from "../../../services/alertService.js";
import {format, fromZonedTime} from 'date-fns-tz';
import ENV from "../../../config/env.js";

export default {
    name: "PayoutCreateComponent",
    components: {LoadingComponent, Datepicker},
    setup() {
        const payoutStore          = usePayoutStore();
        const restaurantStore      = useRestaurantStore();
        const deliveryBoyStore     = useDeliveryBoyStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            payoutStore,
            deliveryBoyStore,
            restaurantStore,
            frontendSettingStore,
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            models: [],
            info: {},
            errors: {},
            payoutAmount: 0,
            enums: {
                modelTypeEnum: modelTypeEnum
            },
            props: {
                form: {
                    model_id: null,
                    type: modelTypeEnum.RESTAURANT,
                    date: "",
                    amount: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: "desc",
                    amount: "",
                    from_date: "",
                    to_date: "",
                    type: null,
                    restaurant_id: null,
                    delivery_boy_id: null
                }
            },
            selectedDate: null,
            timeZone: ENV.TIMEZONE
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        }
    },
    mounted() {
        this.callRestaurant();
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        callRestaurant: function () {
            this.restaurantStore.fetchAllRestaurant().then(res => {
                this.info                = {};
                this.models              = res.data.data;
                this.payoutAmount        = 0;
                this.props.form.model_id = null;
            }).catch();
        },
        callDeliveryBoy: function () {
            this.deliveryBoyStore.fetchAllDeliveryBoy().then(res => {
                this.info                = {};
                this.models              = res.data.data;
                this.payoutAmount        = 0;
                this.props.form.model_id = null;
            });
        },
        checkAmount: function (e) {
            if (this.payoutAmount != null && parseFloat(e.target.value) > parseFloat(this.payoutAmount)) {
                this.props.form.amount = this.payoutAmount;
            }
        },
        checkModel: function (id) {
            if (id) {
                if (this.props.form.type === modelTypeEnum.RESTAURANT) {
                    this.restaurantStore.view(id).then(res => {
                        let data          = res.data.data;
                        this.payoutAmount = data.convert_balance;
                        this.info         = {
                            name: data.name,
                            email: data.email,
                            phone: data.phone,
                            image: data.logo,
                            balance: data.balance,
                            address: data.address
                        }
                    }).catch();
                } else if (this.props.form.type === modelTypeEnum.DELIVERY_BOY) {
                    this.deliveryBoyStore.view(id).then((res) => {
                        let data          = res.data.data;
                        this.payoutAmount = data.convert_balance;
                        this.info         = {
                            name: data.name,
                            email: data.email,
                            phone: data.phone,
                            image: data.image,
                            balance: data.balance,
                            username: data.username,
                            address: data.address[0]?.apartment + ' ' + data.address[0]?.address
                        }
                    }).catch((err) => {
                    });
                }
            }
        },
        reset: function () {
            this.callRestaurant();
            this.models       = [];
            this.info         = {};
            this.errors       = {};
            this.payoutAmount = 0;
            this.selectedDate = null;
            this.props.form   = {
                model_id: null,
                type: modelTypeEnum.RESTAURANT,
                date: "",
                amount: ""
            }
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.payoutStore.save(this.props).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(0, this.$t("menu.payouts"));
                    this.$router.push({name: "admin.payouts.list"});
                    this.props.form   = {
                        model_id: null,
                        type: modelTypeEnum.RESTAURANT,
                        date: "",
                        amount: ""
                    };
                    this.models       = [];
                    this.info         = {};
                    this.errors       = {};
                    this.payoutAmount = 0;
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    },
    watch: {
        selectedDate(newDate) {
            if (newDate) {
                const zoned          = fromZonedTime(newDate, this.timeZone);
                this.props.form.date = format(zoned, 'yyyy-MM-dd HH:mm:ssXXX', {
                    timeZone: this.timeZone,
                });
            }
        }
    }
};
</script>
