<template>
    <div class="col-12">
        <BreadcrumbComponent/>
    </div>

    <LoadingComponent :props="loading"/>
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t("label.change_password") }}</h3>
            </div>
            <div class="db-card-body">
                <form @submit.prevent="changePassword">
                    <div class="form-row">
                        <div v-if="profile.guest === enums.askEnum.NO" class="form-col-12 sm:form-col-6">
                            <label for="old_password" class="db-field-title required"> {{
                                    $t('label.old_password')
                                }} </label>
                            <input v-model="form.old_password" v-bind:class="errors.old_password ? 'invalid' : ''"
                                   id="old_password" type="password" class="db-field-control">
                            <small class="db-field-alert" v-if="errors.old_password">{{
                                    errors.old_password[0]
                                }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="password" class="db-field-title required">{{
                                    $t("label.new_password")
                                }} </label>
                            <input v-model="form.password" v-bind:class="errors.password ? 'invalid' : ''"
                                   type="password" id="password" class="db-field-control" autocomplete="off"/>
                            <small class="db-field-alert" v-if="errors.password"> {{ errors.password[0] }} </small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="confirm_password"
                                   class="db-field-title required">{{ $t("label.confirm_new_password") }} </label>
                            <input v-model="form.confirm_password"
                                   v-bind:class="errors.confirm_password ? 'invalid' : ''" type="password"
                                   id="confirm_password" class="db-field-control" autocomplete="off"/>
                            <small class="db-field-alert" v-if="errors.confirm_password">
                                {{ errors.confirm_password[0] }}
                            </small>
                        </div>
                        <div class="form-col-12">
                            <button type="submit" class="db-btn text-white bg-primary">
                                <i class="lab lab-fill-save text-base"></i>
                                <span>{{ $t("button.save") }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import BreadcrumbComponent from "../components/BreadcrumbComponent.vue";
import LoadingComponent from "../../common/LoadingComponent.vue";
import alertService from "../../../services/alertService.js";
import {useFrontendEditProfileStore} from "../../../stores/frontendEditProfile.js";
import {useAuthStore} from "../../../stores/auth.js";
import askEnum from "../../../enums/modules/askEnum.js";

export default {
    name: "ProfileChangePasswordComponent",
    components: {
        BreadcrumbComponent,
        LoadingComponent
    },
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
            errors: {}
        }
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
                    if (this.profile.guest === this.enums.askEnum.YES) {
                        this.authStore.profile().then().catch();
                    }
                    this.errors = {};
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
