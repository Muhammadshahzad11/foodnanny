<template>
    <LoadingComponent :props="loading" />
    <div id="order_setup" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t('menu.order_setup') }}</h3>
        </div>
        <div class="db-card-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="food_preparation_time" class="db-field-title required">
                            {{ $t("label.food_preparation_time") }}
                            <span class="text-primary">{{ $t("label.in_minute") }}</span>
                        </label>
                        <input v-on:keypress="onlyNumber($event)" v-model="form.food_preparation_time"
                            v-bind:class="errors.food_preparation_time ? 'invalid' : ''" type="text"
                            id="food_preparation_time" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.food_preparation_time">
                            {{ errors.food_preparation_time[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="schedule_order_slot_duration" class="db-field-title required">
                            {{ $t("label.schedule_order_slot_duration") }}
                            <span class="text-primary">{{ $t("label.in_minute") }}</span>
                        </label>
                        <input v-on:keypress="onlyNumber($event)" v-model="form.schedule_order_slot_duration"
                            v-bind:class="errors.schedule_order_slot_duration ? 'invalid' : ''" type="text"
                            id="schedule_order_slot_duration" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.schedule_order_slot_duration">
                            {{ errors.schedule_order_slot_duration[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="minimum_order_limit" class="db-field-title required">
                            {{ $t("label.minimum_order_limit") }}
                        </label>
                        <input v-on:keypress="floatNumber($event)" v-model="form.minimum_order_limit" v-bind:class="errors.minimum_order_limit ? 'invalid' : ''" type="text" id="minimum_order_limit"
                            class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.minimum_order_limit">
                            {{ errors.minimum_order_limit[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="enable">{{ $t("label.takeaway") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.takeaway" id="takeaway-enable" type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="takeaway-enable" class="db-field-label">{{ $t("label.enable") }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.takeaway" type="radio" id="takeaway-disable" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="takeaway-disable" class="db-field-label">{{ $t("label.disable") }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.takeaway">
                            {{ errors.takeaway[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="enable">{{ $t("label.delivery") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.ENABLE" v-model="form.delivery" id="deliver-enable" type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="deliver-enable" class="db-field-label">{{ $t("label.enable") }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input :value="enums.activityEnum.DISABLE" v-model="form.delivery" type="radio" id="deliver-disable" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="deliver-disable" class="db-field-label">{{ $t("label.disable") }}</label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.delivery">
                            {{ errors.delivery[0] }}
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
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import appService from "../../../../services/appService.js";
import activityEnum from "../../../../enums/modules/activityEnum.js";
import {useOrderSetupStore} from "../../../../stores/orderSetup.js";

export default {
    name: "OrderSetupComponent",
    components: { LoadingComponent },
    setup() {
        const orderSetupStore = useOrderSetupStore();
        return {orderSetupStore}
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                activityEnum: activityEnum
            },
            form: {
                food_preparation_time: null,
                schedule_order_slot_duration: null,
                takeaway: null,
                delivery: null,
                minimum_order_limit: null
            },
            errors: {}
        }
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.orderSetupStore.fetch().then(res => {
                this.form = {
                    restaurant_id: res.data.data.restaurant_id ? res.data.data.restaurant_id : null,
                    food_preparation_time: res.data.data.food_preparation_time,
                    schedule_order_slot_duration: res.data.data.schedule_order_slot_duration,
                    takeaway: res.data.data.takeaway,
                    delivery: res.data.data.delivery,
                    minimum_order_limit: res.data.data.minimum_order_limit
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
        onlyNumber(e) {
            return appService.onlyNumber(e);
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.orderSetupStore.save(this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(true, this.$t("menu.order_setup"));
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
