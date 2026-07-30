<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="row">
            <div class="col-12 md:col-8">
                <div class="rounded-lg bg-white shadow-db-card">
                    <h3 class="text-base font-medium capitalize text-paragraph py-5 px-4 border-b border-[#e5e7eb]">
                        {{ $t('menu.cashouts') }}
                    </h3>
                    <form @submit.prevent="save">
                        <div class="form-row p-4">
                            <div class="form-col-12 sm:form-col-6">
                                <label for="user_id" class="db-field-title required">{{ $t("label.user") }}</label>
                                <vue-select v-bind:class="errors.user_id ? 'invalid' : ''" @update:modelValue="checkCashoutBalance($event)" class="db-field-control f-b-custom-select" id="user_id" v-model="props.form.user_id" :options="employees" label-by="name_email" value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--"/>
                                <small class="db-field-alert" v-if="errors.user_id">{{ errors.user_id[0] }}</small>
                            </div>
                            <div class="form-col-12 sm:form-col-6">
                                <label for="amount" class="db-field-title required">{{ $t("label.amount") }}</label>
                                <input v-model="props.form.amount" v-bind:class="errors.amount ? 'invalid' : ''"
                                       v-on:keyup="checkAvailableAmount($event)" v-on:keypress="floatNumber($event)"
                                       type="text" id="amount"
                                       class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.amount">{{ errors.amount[0] }}</small>
                            </div>
                            <div class="form-col-12 sm:form-col-6">
                                <label for="date" class="db-field-title required">{{ $t("label.date") }}</label>
                                <Datepicker :hideInputIcon="true" autoApply v-model="props.form.date"
                                            :enableTimePicker="true" :is24="false" :monthChangeOnScroll="false"
                                            utc="false" :input-class-name="errors.date ? 'invalid' : ''">
                                    <template #am-pm-button="{ toggle, value }">
                                        <button @click.prevent="toggle">{{ value }}</button>
                                    </template>
                                </Datepicker>
                                <small class="db-field-alert" v-if="errors.date">{{ errors.date[0] }}</small>
                            </div>
                            <div class="form-col-12 sm:form-col-6">
                                <label for="transaction_id" class="db-field-title">{{
                                        $t("label.transaction_id")
                                    }}</label>
                                <input v-model="props.form.transaction_id"
                                       v-bind:class="errors.transaction_id ? 'invalid' : ''" type="text"
                                       id="transaction_id" class="db-field-control"/>
                                <small class="db-field-alert" v-if="errors.transaction_id">{{
                                        errors.transaction_id[0]
                                    }}</small>
                            </div>
                            <div class="form-col-12 sm:form-col-6">
                                <label class="db-field-title"> {{ $t("label.attachment") }} <span class="text-primary">({{
                                        $t("label.image_or_pdf")
                                    }})</span></label>
                                <input @change="changeFile" v-bind:class="errors.file ? 'invalid' : ''" type="file"
                                       ref="fileProperty" accept="image/png , image/jpeg, image/jpg , application/pdf "
                                       class="db-field-control cursor-pointer" id="image"/>
                                <small class="db-field-alert" v-if="errors.file"> {{ errors.file[0] }} </small>
                            </div>
                            <div class="form-col-12 sm:form-col-12">
                                <label for="remarks" class="db-field-title">{{ $t("label.remarks") }}</label>
                                <textarea id="remarks" v-model="props.form.remarks"
                                          v-bind:class="errors.remarks ? 'invalid' : ''"
                                          class="db-field-control"></textarea>
                                <small class="db-field-alert" v-if="errors.remarks">{{ errors.remarks[0] }}</small>
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

            <div class="col-12 md:col-4" v-if="userInfo">
                <div class="rounded-lg overflow-hidden bg-white shadow-db-card">
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
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import alertService from "../../../services/alertService.js";
import Datepicker from "@vuepic/vue-datepicker";
import {useEmployeeStore} from "../../../stores/employee.js";
import {useCashoutStore} from "../../../stores/cashout.js";
import appService from "../../../services/appService.js";
import {format, fromZonedTime} from 'date-fns-tz';
import ENV from "../../../config/env.js";
import {useFrontendSettingStore} from "../../../stores/frontendSetting.js";
import statusEnum from "../../../enums/modules/statusEnum.js";

export default {
    name: "CashoutCreateComponent",
    components: {
        LoadingComponent,
        Datepicker
    },
    setup() {
        const employeeStore        = useEmployeeStore();
        const cashoutStore         = useCashoutStore();
        const frontendSettingStore = useFrontendSettingStore();
        return {
            cashoutStore,
            employeeStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            addButton: {
                title: this.$t("button.add_cashout")
            },
            props: {
                form: {
                    user_id: null,
                    amount: "",
                    date: "",
                    transaction_id: "",
                    remarks: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                }
            },
            userLists: [],
            date: '',
            errors: {},
            file: "",
            userInfo: null,
            timeZone: ENV.TIMEZONE
        };
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        employees: function () {
            return this.employeeStore.allEmployeeAndAdmins;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.employeeStore.fetchAllEmployeeAndAdmin({
            order_column: 'id',
            order_type: 'asc',
            status: statusEnum.ACTIVE
        }).then(res => {
            this.userLists = res.data.data;
            this.loading.isActive = false;
        }).catch(() => {
            this.loading.isActive = false;
        });
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        checkAvailableAmount: function (e) {
            if (this.userInfo) {
                if (this.userInfo?.convert_collection !== null && parseFloat(e.target.value) > parseFloat(this.userInfo?.convert_collection)) {
                    this.props.form.amount = this.userInfo?.convert_collection;
                }
            }
        },
        checkCashoutBalance: function (id) {
            if (id) {
                this.userInfo = null;
                this.employeeStore.viewEmployeeAndAdmin(id).then((res) => {
                    this.userInfo = res.data.data;
                }).catch();
            } else {
                this.userInfo = null;
            }
        },
        changeFile: function (e) {
            this.file = e.target.files[0];
        },
        save: function () {
            try {
                const fd = new FormData();
                if (this.props.form.date) {
                    const zoned = fromZonedTime(this.props.form.date, this.timeZone);
                    this.date   = format(zoned, 'yyyy-MM-dd HH:mm:ssXXX', {
                        timeZone: this.timeZone
                    });
                }
                fd.append('user_id', this.props.form.user_id === null ? '' : this.props.form.user_id);
                fd.append('amount', this.props.form.amount);
                fd.append('date', this.date);
                fd.append('transaction_id', this.props.form.transaction_id);
                fd.append('remarks', this.props.form.remarks);
                if (this.file) {
                    fd.append('file', this.file);
                }
                this.loading.isActive = true;
                this.cashoutStore.save({form: fd, search: this.props.search}).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(0, this.$t("menu.cashouts"));
                    this.$router.push({name: "admin.cashouts.list"});
                    this.props.form = {
                        user_id       : null,
                        amount        : "",
                        date          : "",
                        transaction_id: "",
                        remarks       : ""
                    };
                    this.date                     = "";
                    this.file                     = "";
                    this.errors                   = {};
                    this.$refs.fileProperty.value = null;
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        reset: function() {
            this.props.form = {
                user_id       : null,
                amount        : "",
                date          : "",
                transaction_id: "",
                remarks       : ""
            };
            this.date                     = "";
            this.file                     = "";
            this.errors                   = {};
            this.$refs.fileProperty.value = null;
        }
    }
}
</script>
