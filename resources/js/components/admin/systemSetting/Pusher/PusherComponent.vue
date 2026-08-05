<template>
    <LoadingComponent :props="loading"/>
    <div id="pusher" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("menu.pusher") }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="pusher_app_id" class="db-field-title required"> {{ $t("label.pusher_app_id") }} </label>
                        <input v-model="form.pusher_app_id" v-bind:class="errors.pusher_app_id ? 'invalid' : ''" type="text" id="pusher_app_id" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.pusher_app_id">{{ errors.pusher_app_id[0] }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="pusher_app_key" class="db-field-title required"> {{ $t("label.pusher_app_key") }} </label>
                        <input v-model="form.pusher_app_key" v-bind:class="errors.pusher_app_key ? 'invalid' : ''" type="text" id="pusher_app_key" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.pusher_app_key">{{ errors.pusher_app_key[0] }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="pusher_app_secret" class="db-field-title required"> {{ $t("label.pusher_app_secret") }} </label>
                        <input v-model="form.pusher_app_secret" v-bind:class="errors.pusher_app_secret ? 'invalid' : ''" type="text" id="pusher_app_secret" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.pusher_app_secret">{{ errors.pusher_app_secret[0] }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="pusher_app_cluster" class="db-field-title required"> {{ $t("label.pusher_app_cluster") }}  </label>
                        <input v-model="form.pusher_app_cluster" v-bind:class="errors.pusher_app_cluster ? 'invalid' : ''" type="text"  id="pusher_app_cluster" class="db-field-control"/>
                        <small class="db-field-alert" v-if="errors.pusher_app_cluster">{{  errors.pusher_app_cluster[0] }}</small>
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
import {usePusherStore} from "../../../../stores/pusher.js"

export default {
    name: "PusherComponent",
    components: {LoadingComponent, alertService},

    setup(){
        const pusherStore = usePusherStore();
        return {
            pusherStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                pusher_app_id     : "",
                pusher_app_key    : "",
                pusher_app_secret : "",
                pusher_app_cluster: ""
            },
            errors: {},
        };
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.pusherStore.fetch().then((res) => {
                this.form             = {
                    pusher_app_id     : res.data.data.pusher_app_id,
                    pusher_app_key    : res.data.data.pusher_app_key,
                    pusher_app_secret : res.data.data.pusher_app_secret,
                    pusher_app_cluster: res.data.data.pusher_app_cluster
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
    methods: {
        save: function () {
            try {
                this.loading.isActive = true;
                this.pusherStore.save(this.form).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.pusher"));
                        this.errors                     = {};
                    }).catch((err) => {
                        this.loading.isActive = false;
                        if (err.response.data.status !== "undefined" && err.response.data.status === false) {
                            alertService.error(err.response.data.message)
                        } else {
                            this.errors = err.response.data.errors;
                        }
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
};
</script>
