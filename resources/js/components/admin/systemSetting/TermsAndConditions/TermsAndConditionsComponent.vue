<template>
    <LoadingComponent :props="loading" />

    <div id="company" class="db-card db-tab-div active !overflow-visible">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("menu.terms_and_conditions") }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="terms_and_conditions_customer_page_id" class="db-field-title">{{ $t("label.customer_terms_and_conditions_page") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="terms_and_conditions_customer_page_id" v-bind:class="errors.terms_and_conditions_customer_page_id ? 'invalid' : ''" v-model="form.terms_and_conditions_customer_page_id" :options="pages" label-by="title" value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.terms_and_conditions_customer_page_id"> {{ errors.terms_and_conditions_customer_page_id[0] }} </small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="terms_and_conditions_restaurant_page_id" class="db-field-title"> {{ $t("label.restaurant_terms_and_conditions_page") }} </label>
                        <vue-select class="db-field-control f-b-custom-select" id="terms_and_conditions_restaurant_page_id" v-bind:class="errors.terms_and_conditions_restaurant_page_id ? 'invalid' : ''" v-model="form.terms_and_conditions_restaurant_page_id" :options="pages" label-by="title" value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.terms_and_conditions_restaurant_page_id"> {{ errors.terms_and_conditions_restaurant_page_id[0] }} </small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="terms_and_conditions_delivery_boy_page_id" class="db-field-title"> {{ $t("label.delivery_boy_terms_and_conditions_page") }}  </label>
                        <vue-select class="db-field-control f-b-custom-select" id="terms_and_conditions_delivery_boy_page_id" v-bind:class="errors.terms_and_conditions_delivery_boy_page_id ? 'invalid' : ''" v-model="form.terms_and_conditions_delivery_boy_page_id" :options="pages" label-by="title" value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.terms_and_conditions_delivery_boy_page_id"> {{ errors.terms_and_conditions_delivery_boy_page_id[0] }} </small>
                    </div>
                    <div class="form-col-12 mt-5">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-fill-save text-base"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import {useTermsAndConditionsStore} from "../../../../stores/termsAndConditions.js";
import {usePageStore} from "../../../../stores/page.js";

export default {
    name: "TermsAndConditionsComponent",
    components: { LoadingComponent },
    setup() {
        const pageStore               = usePageStore();
        const termsAndConditionsStore = useTermsAndConditionsStore();
        return {
            pageStore,
            termsAndConditionsStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                terms_and_conditions_customer_page_id    : null,
                terms_and_conditions_restaurant_page_id  : null,
                terms_and_conditions_delivery_boy_page_id: null
            },
            errors: {}
        };
    },
    computed: {
        pages: function () {
            return this.pageStore.lists;
        },
    },
    mounted() {
        this.loadProperty();
    },
    methods: {
        loadProperty: async function () {
            try {
                this.loading.isActive = true;
                await this.pageStore.fetch({ order_column: "id", order_type: "asc", status: statusEnum.ACTIVE });
                await this.termsAndConditionsStore.fetch("termsAndConditions/lists").then((res) => {
                    this.form = {
                        terms_and_conditions_customer_page_id    : res.data.data.terms_and_conditions_customer_page_id,
                        terms_and_conditions_restaurant_page_id  : res.data.data.terms_and_conditions_restaurant_page_id,
                        terms_and_conditions_delivery_boy_page_id: res.data.data.terms_and_conditions_delivery_boy_page_id
                    };
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.termsAndConditionsStore.save(this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.terms_and_conditions"));
                    this.errors = "";
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
