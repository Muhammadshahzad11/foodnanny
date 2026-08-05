<template>
    <LoadingComponent :props="loading" />
    <div id="delivery_setup" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t('menu.delivery_setup') }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="delivery_setup_free_delivery_kilometer" class="db-field-title required">{{ $t("label.free_delivery_kilometer") }} </label>
                        <input @keypress="floatNumber($event)" v-model="form.delivery_setup_free_delivery_kilometer" v-bind:class="errors.delivery_setup_free_delivery_kilometer ? 'invalid' : ''" type="text" id="delivery_setup_free_delivery_kilometer" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.delivery_setup_free_delivery_kilometer">{{ errors.delivery_setup_free_delivery_kilometer[0] }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="delivery_setup_basic_delivery_fee" class="db-field-title required"> {{ $t("label.basic_delivery_fee") }} </label>
                        <input @keypress="floatNumber($event)" v-model="form.delivery_setup_basic_delivery_fee" v-bind:class="errors.delivery_setup_basic_delivery_fee ? 'invalid' : ''" type="text" id="delivery_setup_basic_delivery_fee" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.delivery_setup_basic_delivery_fee">{{ errors.delivery_setup_basic_delivery_fee[0] }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="delivery_setup_charge_per_kilo" class="db-field-title required"> {{ $t("label.charge_per_kilo") }} </label>
                        <input @keypress="floatNumber($event)" v-model="form.delivery_setup_charge_per_kilo" v-bind:class="errors.delivery_setup_charge_per_kilo ? 'invalid' : ''" type="text" id="delivery_setup_charge_per_kilo" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.delivery_setup_charge_per_kilo">{{ errors.delivery_setup_charge_per_kilo[0] }}</small>
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
import appService from "../../../../services/appService.js";
import {useDeliverySetupStore} from "../../../../stores/deliverySetup.js";

export default {
    name: "DeliverySetupComponent",
    components: { LoadingComponent },
    setup() {
        const deliverySetupStore = useDeliverySetupStore();
        return {
            deliverySetupStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                delivery_setup_free_delivery_kilometer: null,
                delivery_setup_basic_delivery_fee     : null,
                delivery_setup_charge_per_kilo        : null
            },
            errors: {}
        }
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.deliverySetupStore.fetch().then(res => {
                this.form = {
                    delivery_setup_free_delivery_kilometer: res.data.data.delivery_setup_free_delivery_kilometer,
                    delivery_setup_basic_delivery_fee     : res.data.data.delivery_setup_basic_delivery_fee,
                    delivery_setup_charge_per_kilo        : res.data.data.delivery_setup_charge_per_kilo
                }
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        } catch (err) {
            this.loading.isActive = false;
        }
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.deliverySetupStore.save(this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.delivery_setup"));
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
}
</script>
