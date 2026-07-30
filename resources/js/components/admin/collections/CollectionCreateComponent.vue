<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="row">
            <div class="col-12 md:col-8">
                <div class="rounded-lg bg-white shadow-db-card">
                    <h3 class="text-base font-medium capitalize text-paragraph py-5 px-4 border-b border-[#e5e7eb]">
                        {{ $t('label.collections') }}
                    </h3>
                    <form @submit.prevent="save">
                        <div class="form-row p-4">
                            <div class="form-col-12 sm:form-col-6">
                                <label class="db-field-title required" for="delivery_boy">
                                    {{ $t("label.type") }}
                                </label>
                                <div class="db-field-radio-group">
                                    <div class="db-field-radio">
                                        <div class="custom-radio">
                                            <input @click="callDeliveryBoy"
                                                   :value="enums.collectionTypeEnum.DELIVERY_BOY" v-model="props.type"
                                                   id="delivery_boy" type="radio" class="custom-radio-field"/>
                                            <span class="custom-radio-span"></span>
                                        </div>
                                        <label for="delivery_boy" class="db-field-label">
                                            {{ $t("label.delivery_boy") }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row p-4">
                            <div class="form-col-12 sm:form-col-6">
                                <label for="user_id" class="db-field-title required">{{ $t("label.user") }}</label>
                                <vue-select @update:modelValue="changeUser($event)"
                                            class="db-field-control f-b-custom-select" id="user_id"
                                            :class="errors.source_user_id ? 'invalid' : ''"
                                            v-model="props.form.source_user_id" :options="userLists"
                                            label-by="name_email" value-by="id" :closeOnSelect="true"
                                            :searchable="true" :clearOnClose="true" placeholder="--"
                                            search-placeholder="--"/>
                                <small class="db-field-alert" v-if="errors.source_user_id">
                                    {{ errors.source_user_id[0] }}
                                </small>
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
                            <div class="form-col-12 sm:form-col-6">
                                <label for="date" class="db-field-title required">{{ $t("label.date") }}</label>
                                <Datepicker hideInputIcon autoApply v-model="props.form.date" :enableTimePicker="true"
                                            :is24="false" :monthChangeOnScroll="false" utc="false"
                                            :input-class-name="errors.date ? 'invalid' : ''">
                                    <template #am-pm-button="{ toggle, value }">
                                        <button @click="toggle">{{ value }}</button>
                                    </template>
                                </Datepicker>
                                <small class="db-field-alert" v-if="errors.date">{{ errors.date[0] }}</small>
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

            <div class="col-12 md:col-4">
                <div class="rounded-lg overflow-hidden bg-white shadow-db-card" v-if="userInfo">
                    <img :src="setting.profile_cover" alt="profile cover" class="w-full block">
                    <div class="px-6 pb-4 -mt-11">
                        <img :src="userInfo.image" alt="profile avatar" class="w-20 h-20 object-cover rounded-full">
                        <h3 class="text-lg font-semibold capitalize text-heading mb-2">{{ userInfo.name }}</h3>
                        <h4 class="text-sm font-medium text-heading mb-1" v-if="userInfo.email">
                            {{ userInfo.email }}
                        </h4>
                        <h5 class="text-sm font-medium text-heading" v-if="userInfo.phone">
                            {{ userInfo.country_code }}{{ userInfo.phone }}
                        </h5>
                    </div>
                    <ul class="w-full flex flex-col gap-4 pt-4 px-6 pb-6 border-t border-gray-100">
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-sm ltr:text-left rtl: text-right capitalize text-paragraph">
                                {{ $t('label.role') }}
                            </span>
                            <span
                                class="db-table-badge text-[#E89806] bg-[#FFF5DE]">
                                {{ userInfo.role }}
                            </span>
                        </li>
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-sm ltr:text-left rtl: text-right capitalize text-paragraph">
                                {{ $t('label.collection_balance') }}
                            </span>
                            <span class="text-sm ltr:text-right rtl:text-left font-medium capitalize text-heading">
                                {{ userInfo.collection }}
                            </span>
                        </li>
                        <li class="flex items-center justify-between gap-2">
                            <span class="text-sm ltr:text-left rtl: text-right capitalize text-paragraph">
                                {{ $t('label.username') }}
                            </span>
                            <span class="text-sm ltr:text-right rtl:text-left font-medium text-heading">
                                {{ userInfo.username }}
                            </span>
                        </li>
                        <li class="flex items-center justify-between gap-2" v-if="userInfo.address.length > 0">
                            <span class="text-sm ltr:text-left rtl: text-right capitalize text-paragraph">
                                {{ $t('label.address') }}
                            </span>
                            <span class="text-sm ltr:text-right rtl:text-left font-medium capitalize text-heading">
                                {{ userInfo.address[0].apartment }} {{ userInfo.address[0].address }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import collectionTypeEnum from "../../../enums/modules/collectionTypeEnum";
import {useDeliveryBoyStore} from "../../../stores/deliveryBoy.js";
import LoadingComponent from "../../common/LoadingComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum.js";
import {useEmployeeStore} from "../../../stores/employee.js";
import appService from "../../../services/appService.js";
import Datepicker from "@vuepic/vue-datepicker";
import {useCollectionStore} from "../../../stores/collection.js";
import alertService from "../../../services/alertService.js";
import {useDefaultAccessStore} from "../../../stores/defaultAccess.js";
import {useAuthStore} from "../../../stores/auth.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";

export default {
    name: "CollectionCreateComponent",
    components: {LoadingComponent, Datepicker},
    setup() {
        const authStore            = useAuthStore();
        const employeeStore        = useEmployeeStore();
        const collectionStore      = useCollectionStore();
        const deliveryBoyStore     = useDeliveryBoyStore();
        const defaultAccessStore   = useDefaultAccessStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            authStore,
            employeeStore,
            collectionStore,
            deliveryBoyStore,
            defaultAccessStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                collectionTypeEnum: collectionTypeEnum,
            },
            props: {
                form: {
                    source_user_id: null,
                    date: "",
                    amount: "",
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    type: null,
                    user_id: null,
                    amount: "",
                    from_date: "",
                    to_date: ""
                },
                type: collectionTypeEnum.DELIVERY_BOY,
            },
            userLists: [],
            userInfo: null,
            errors: {}
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        }
    },
    async mounted() {
        await this.callDeliveryBoy();
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        callDeliveryBoy: async function () {
            this.userInfo          = null;
            this.props.form.amount = null;
            await this.deliveryBoyStore.fetchAllDeliveryBoy().then(res => {
                this.userLists = res.data.data;
            }).catch(err => {
            })
        },
        checkAmount: function (e) {
            if (this.userInfo) {
                if (this.userInfo?.convert_collection !== null && parseFloat(e.target.value) > parseFloat(this.userInfo?.convert_collection)) {
                    this.props.form.amount = this.userInfo?.convert_collection;
                }
            }
        },
        changeUser: function (id) {
            if (id) {
                this.userInfo          = null;
                this.props.form.amount = null;
                if (this.props.type === this.enums.collectionTypeEnum.DELIVERY_BOY) {
                    this.deliveryBoyStore.view(id).then(res => {
                        this.userInfo = res.data.data;
                    }).catch()
                } else if (this.props.type === this.enums.collectionTypeEnum.Employee) {
                    this.employeeStore.viewEmployeeAndAdmin(id).then(res => {
                        this.userInfo = res.data.data;
                    }).catch()
                }
            } else {
                this.userInfo = null;
            }
        },
        save: async function () {
            try {
                this.loading.isActive = true;
                this.collectionStore.save(this.props).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(0, this.$t("menu.collections"));
                    this.$router.push({name: "admin.collections.list"});
                    this.props.form = {
                        source_user_id: null,
                        date: "",
                        amount: ""
                    };
                    this.userLists  = [];
                    this.userInfo   = null;
                    this.errors     = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        reset: function () {
            this.callDeliveryBoy();
            this.props.form = {
                source_user_id: null,
                date: "",
                amount: ""
            }
            this.userLists  = [];
            this.userInfo   = null;
            this.errors     = {};
        }
    }
}
</script>
