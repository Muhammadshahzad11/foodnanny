<template>
    <LoadingComponent :props="loading" />
    <SmModalCreateComponent :props="addButton" v-if="permissionChecker('tables_create')" />

    <div id="modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.tables") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500" @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6" v-if="showRestaurantField">
                            <label for="restaurant_id" class="db-field-title required">{{ $t("label.restaurant") }}</label>
                            <vue-select
                                class="db-field-control f-b-custom-select"
                                id="restaurant_id"
                                v-model="props.form.restaurant_id"
                                :options="restaurants"
                                label-by="name"
                                value-by="id"
                                :closeOnSelect="true"
                                :searchable="true"
                                :clearOnClose="true"
                                placeholder="--"
                                search-placeholder="--"
                            />
                            <small class="db-field-alert" v-if="errors.restaurant_id">{{ errors.restaurant_id[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="table_number" class="db-field-title required">{{ $t("label.table_number") }}</label>
                            <input v-model="props.form.table_number" :class="errors.table_number ? 'invalid' : ''" type="text" id="table_number" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.table_number">{{ errors.table_number[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="name" class="db-field-title required">{{ $t("label.table_name") }}</label>
                            <input v-model="props.form.name" :class="errors.name ? 'invalid' : ''" type="text" id="name" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="capacity" class="db-field-title required">{{ $t("label.capacity") }}</label>
                            <input v-model="props.form.capacity" v-on:keypress="onlyNumber($event)" :class="errors.capacity ? 'invalid' : ''" type="text" id="capacity" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.capacity">{{ errors.capacity[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="zone" class="db-field-title">{{ $t("label.zone") }}</label>
                            <input v-model="props.form.zone" :class="errors.zone ? 'invalid' : ''" type="text" id="zone" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.zone">{{ errors.zone[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="status" class="db-field-title required">{{ $t("label.status") }}</label>
                            <vue-select
                                class="db-field-control f-b-custom-select"
                                id="status"
                                v-model="props.form.status"
                                :options="statusOptions"
                                label-by="name"
                                value-by="id"
                                :closeOnSelect="true"
                                :searchable="true"
                                :clearOnClose="true"
                                placeholder="--"
                                search-placeholder="--"
                            />
                            <small class="db-field-alert" v-if="errors.status">{{ errors.status[0] }}</small>
                        </div>
                        <div class="form-col-12">
                            <label for="notes" class="db-field-title">{{ $t("label.notes") }}</label>
                            <textarea v-model="props.form.notes" :class="errors.notes ? 'invalid' : ''" id="notes" class="db-field-control" rows="3"></textarea>
                            <small class="db-field-alert" v-if="errors.notes">{{ errors.notes[0] }}</small>
                        </div>
                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-fill-close-circle text-base"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save text-base"></i>
                                    <span>{{ $t("button.save") }}</span>
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
import SmModalCreateComponent from "../components/buttons/SmModalCreateComponent.vue";
import LoadingComponent from "../../common/LoadingComponent.vue";
import tableStatusEnum from "../../../enums/modules/tableStatusEnum.js";
import roleEnum from "../../../enums/modules/roleEnum.js";
import alertService from "../../../services/alertService.js";
import {useModal} from "../../../composables/modal.js";
import appService from "../../../services/appService.js";
import {useRestaurantTableStore} from "../../../stores/restaurantTable.js";
import {useAuthStore} from "../../../stores/auth.js";

export default {
    name: "TableCreateComponent",
    components: {SmModalCreateComponent, LoadingComponent},
    props: ["props", "restaurants", "statusOptions"],
    setup() {
        const restaurantTableStore = useRestaurantTableStore();
        const authStore = useAuthStore();
        return {restaurantTableStore, authStore};
    },
    data() {
        return {
            loading: {isActive: false},
            addButton: {title: this.$t("button.add_table")},
            enums: {tableStatusEnum},
            errors: {}
        };
    },
    computed: {
        showRestaurantField: function () {
            return Number(this.authStore.info?.role_id) === roleEnum.ADMIN;
        }
    },
    methods: {
        permissionChecker: function (permission) {
            return appService.permissionChecker(permission);
        },
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        reset: function () {
            useModal().closeModal('modal');
            this.restaurantTableStore.reset();
            this.errors = {};
            this.$props.props.form = {
                restaurant_id: null,
                table_number: "",
                name: "",
                capacity: 2,
                zone: "",
                status: tableStatusEnum.AVAILABLE,
                notes: ""
            };
        },
        save: function () {
            try {
                const tempId = this.restaurantTableStore.temp.temp_id;
                this.loading.isActive = true;
                this.restaurantTableStore.save(this.props).then(() => {
                    useModal().closeModal('modal');
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("menu.tables"));
                    this.props.form = {
                        restaurant_id: null,
                        table_number: "",
                        name: "",
                        capacity: 2,
                        zone: "",
                        status: tableStatusEnum.AVAILABLE,
                        notes: ""
                    };
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response?.data?.errors || {};
                    if (err.response?.data?.message && !this.errors) {
                        alertService.error(err.response.data.message);
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
