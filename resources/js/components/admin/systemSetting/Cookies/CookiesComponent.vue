<template>
    <LoadingComponent :props="loading" />

    <div id="company" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("menu.cookies") }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-12">
                        <label for="cookies_details_page_id" class="db-field-title required">
                            {{ $t("label.cookies_details_page") }}
                        </label>
                        <vue-select class="db-field-control f-b-custom-select" id="cookies_details_page_id"
                            v-bind:class="errors.cookies_details_page_id ? 'invalid' : ''"
                            v-model="form.cookies_details_page_id" :options="pages" label-by="title" value-by="id"
                            :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.cookies_details_page_id">
                            {{ errors.cookies_details_page_id[0] }}
                        </small>
                    </div>
                    <div class="form-col-12">
                        <label for="cookies_summary" class="db-field-title required">
                            {{ $t("label.cookies_summary") }}
                        </label>
                        <textarea v-model="form.cookies_summary" v-bind:class="errors.cookies_summary ? 'invalid' : ''"
                            id="cookies_summary" class="db-field-control"></textarea>
                        <small class="db-field-alert" v-if="errors.cookies_summary">
                            {{ errors.cookies_summary[0] }}
                        </small>
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
import {useCookiesStore} from "../../../../stores/cookies.js";
import {usePageStore} from "../../../../stores/page.js";


export default {
    name: "CookiesComponent",
    components: { LoadingComponent },
    setup() {
        const cookiesStore = useCookiesStore();
        const pageStore    = usePageStore();
        return {
            cookiesStore,
            pageStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                cookies_details_page_id: null,
                cookies_summary: ""
            },
            errors: {}
        };
    },
    computed: {
        pages: function () {
            return this.pageStore.lists;
        }
    },
    mounted() {
        this.loadProperty();
    },
    methods: {
        loadProperty : async function() {
            try {
                this.loading.isActive = true;
                await this.pageStore.fetch({ order_column: "id", order_type: "asc", status: statusEnum.ACTIVE });
                await this.cookiesStore.fetch().then((res) => {
                    this.form = {
                        cookies_details_page_id: res.data.data.cookies_details_page_id,
                        cookies_summary: res.data.data.cookies_summary
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
                this.cookiesStore.save(this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.cookies"));
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
