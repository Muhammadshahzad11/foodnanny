<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-4 pb-12">
        <div class="container max-w-xl">
            <router-link :to="{ name: 'frontend.home' }" class="mb-6 inline-flex items-center gap-2 text-primary">
                <i class="lab-line-undo text-xl font-semibold"></i>
                <span class="text-base font-medium">{{ $t('label.back_to_home') }}</span>
            </router-link>
            <form @submit.prevent="changePassword" class="w-full p-4 rounded-2xl bg-white shadow-xs">
                <h3 class="capitalize text-xl mb-6 font-semibold">{{ $t('label.change_password') }} </h3>
                <div v-if="profile.guest === enums.askEnum.NO" class="row !-m-2 !mb-2">
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="old_password" class="field-title">{{ $t('label.old_password') }}</label>
                        <input v-model="form.old_password" v-bind:class="errors.old_password ? 'invalid' : ''"
                               id="old_password" type="password" class="field-control">
                        <small class="db-field-alert" v-if="errors.old_password"> {{ errors.old_password[0] }} </small>
                    </div>
                </div>
                <div class="row !-m-2">
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="password" class="field-title">{{ $t('label.new_password') }}</label>
                        <input v-model="form.password" v-bind:class="errors.password ? 'invalid' : ''" id="password"
                               type="password" class="field-control">
                        <small class="db-field-alert" v-if="errors.password"> {{ errors.password[0] }} </small>
                    </div>
                    <div class="col-12 sm:col-6 !p-2">
                        <label for="confirm_password" class="field-title">{{
                                $t("label.confirm_new_password")
                            }} </label>
                        <input v-model="form.confirm_password" v-bind:class="errors.confirm_password ? 'invalid' : ''"
                               id="confirm_password" type="password" class="field-control">
                        <small class="db-field-alert" v-if="errors.confirm_password"> {{
                                errors.confirm_password[0]
                            }} </small>
                    </div>
                </div>
                <button type="submit" class="field-button mt-6">{{ $t('button.change_password') }}</button>
            </form>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import {useFrontendEditProfileStore} from "../../../../stores/frontendEditProfile.js";
import {useAuthStore} from "../../../../stores/auth.js";
import askEnum from "../../../../enums/modules/askEnum.js";

export default {
    name: "ChangePasswordComponent",
    components: {LoadingComponent},
    setup() {
        const authStore                = useAuthStore();
        const frontendEditProfileStore = useFrontendEditProfileStore();
        return {
            authStore,
            frontendEditProfileStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                old_password: "",
                password: "",
                confirm_password: ""
            },
            enums: {
                askEnum: askEnum
            },
            errors: {},
        };
    },
    computed: {
        profile: function () {
            return this.authStore.info;
        }
    },
    methods: {
        changePassword: function () {
            try {
                this.loading.isActive = true;
                this.frontendEditProfileStore.changePassword(this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.password"));
                    this.form   = {
                        old_password: "",
                        password: "",
                        confirm_password: ""
                    };
                    this.errors = {};
                    if (this.profile.guest === this.enums.askEnum.YES) {
                        this.authStore.profile().then().catch();
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
