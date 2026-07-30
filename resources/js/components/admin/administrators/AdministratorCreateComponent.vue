<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset" />

    <div id="sidebar" @click="closeBackdrop"
        class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div class="w-full max-w-xl h-dvh overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="drawer-title">{{ $t("menu.administrators") }}</h3>
                <button class="lab-line-close text-base font-bold" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                            <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                                id="name" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="email" class="db-field-title required">{{ $t("label.email") }}</label>
                            <input v-model="props.form.email" v-bind:class="errors.email ? 'invalid' : ''" type="text"
                                id="email" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.email">{{ errors.email[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="phone" class="db-field-title">{{ $t('label.phone') }}</label>
                            <div class="relative group">
                                <div :class="[isOpen ? 'rounded-t-md' : 'rounded-md', errors.phone ? 'invalid' : '']" class="flex w-full h-10 border border-[#e5e7eb] group-hover:border-primary/20 transition-all duration-300 group-focus-within:border-primary/20">
                                    <button @click="isOpen = !isOpen" type="button" :class="errors.phone ? 'invalid' : ''" class="flex items-center gap-1 h-full pl-3 pr-2 border-r border-[#d9dbe9] group-hover:border-primary/20 group-focus-within:border-primary/20 transition-all duration-300">
                                        <span class="text-sm flex-shrink-0">{{ props.form.flag }}</span>
                                        <span class="text-sm">{{ props.form.country_code }}</span>
                                        <i class="lab-fill-arrow-down"></i>
                                    </button>
                                    <input v-model="props.form.phone" v-on:keypress="phoneNumber($event)" type="text" class="pl-2 pr-3 w-full h-full text-sm overflow-hidden placeholder:text-sm">
                                </div>
                                <ul :class="isOpen ? 'scale-y-100' : 'scale-y-0'" class="absolute top-10 left-0 z-10 w-full max-h-60 thin-scrolling rounded-b-lg shadow-paper origin-top border-x border-b group-hover:border-primary/20 group-focus-within:border-primary/20 border-[#d9dbe9] bg-white transition-all duration-300">
                                    <li v-for="countryCode in countryCodes" @click="handleCountry(countryCode)"
                                        class="flex items-center gap-2 px-3 py-1.5 cursor-pointer hover:bg-gray-100 transition-all duration-300">
                                        <div class="text-sm flex-shrink-0">{{ countryCode.flag }}</div>
                                        <span class="text-sm capitalize flex-auto">{{ countryCode.country_name }}</span>
                                        <span class="text-sm">{{ countryCode.calling_code }}</span>
                                    </li>
                                </ul>
                            </div>
                            <small class="db-field-alert" v-if="errors.phone"> {{ errors.phone[0] }} </small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="active">{{ $t("label.status") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model="props.form.status" id="active"
                                            type="radio" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="active" class="db-field-label">{{ $t("label.active") }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model="props.form.status"
                                            type="radio" id="inactive" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inactive" class="db-field-label">{{ $t("label.inactive") }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="password" class="db-field-title required">{{ $t("label.password") }}</label>
                            <input v-model="props.form.password" v-bind:class="errors.password ? 'invalid' : ''"
                                type="password" id="password" class="db-field-control" autocomplete="off" />
                            <small class="db-field-alert" v-if="errors.password">{{ errors.password[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="password_confirmation" class="db-field-title required">{{ $t("label.password_confirmation") }}</label>
                            <input v-model="props.form.password_confirmation"
                                v-bind:class="errors.password_confirmation ? 'invalid' : ''" type="password"
                                id="password_confirmation" class="db-field-control" autocomplete="off" />
                            <small class="db-field-alert" v-if="errors.password_confirmation">{{ errors.password_confirmation[0] }}</small>
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
    </div>
</template>

<script>
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum.js";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import { useCanvas } from "../../../composables/canvas.js";
import { useAdministratorStore } from "../../../stores/administrator.js";
import { useCountryCodeStore } from "../../../stores/countryCode.js";
import { useCompanyStore } from "../../../stores/company.js";

export default {
    name: "AdministratorCreateComponent",
    components: {
        SmSidebarModalCreateComponent,
        LoadingComponent
    },
    props: ["props"],
    setup() {
        const administratorStore = useAdministratorStore();
        const countryCodeStore = useCountryCodeStore();
        const companyStore = useCompanyStore();
        return {
            administratorStore,
            countryCodeStore,
            companyStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum
            },
            errors       : {},
            flag         : "",
            countryCode  : "",
            isOpen       : false,
            closeBackdrop: useCanvas().closeBackdrop
        };
    },
    computed: {
        addButton: function () {
            return { title: this.$t('button.add_administrator') };
        },
        countryCodes: function () {
            return this.countryCodeStore.lists;
        }
    },
    async mounted() {
        this.loading.isActive = true;
        await this.countryCodeStore.fetch().then(res => {
            this.loading.isActive = false;
        }).catch(err => {
            this.loading.isActive = false;
        });
        await this.companyStore.fetch().then(async companyRes => {
            await this.countryCodeStore.view(companyRes.data.data.company_country_code).then(res => {
                if (this.props.form.country_code === "") {
                    this.props.form.country_code = res.data.data.calling_code;
                    this.props.form.flag         = res.data.data.flag_emoji;
                    this.countryCode             = res.data.data.calling_code;
                    this.flag                    = res.data.data.flag_emoji;
                }
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        handleCountry: async function (val) {
            await this.countryCodeStore.fetchFind({
                country_code: val.calling_code
            }).then(res => {
                this.props.form.country_code = res.data.data.calling_code;
                this.props.form.flag         = res.data.data.flag_emoji;
            }).catch(err => {
                this.loading.isActive = false;
            });
            this.isOpen = false
        },
        addReset: function () {
            this.administratorStore.reset();
            this.errors = {};
            this.$props.props.form = {
                name                 : "",
                email                : "",
                phone                : "",
                password             : "",
                password_confirmation: "",
                status               : statusEnum.ACTIVE,
                country_code         : this.countryCode,
                flag                 : this.flag,
            };
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.administratorStore.reset();
            this.errors = {};
            this.$props.props.form = {
                name                 : "",
                email                : "",
                phone                : "",
                password             : "",
                password_confirmation: "",
                status               : statusEnum.ACTIVE,
                country_code         : this.countryCode,
                flag                 : this.flag
            };
        },
        save: function () {
            try {
                const tempId = this.administratorStore.temp.temp_id;
                this.loading.isActive = true;
                this.administratorStore.save(this.props).then((res) => {
                    useCanvas().closeCanvas('sidebar');
                    this.loading.isActive = false;
                    alertService.successFlip(
                        tempId === null ? 0 : 1,
                        this.$t("menu.administrators")
                    );
                    this.props.form = {
                        name                 : "",
                        email                : "",
                        phone                : "",
                        password             : "",
                        password_confirmation: "",
                        status               : statusEnum.ACTIVE,
                        country_code         : this.countryCode,
                        flag                 : this.flag
                    };
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
};
</script>
