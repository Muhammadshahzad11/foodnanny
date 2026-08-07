<template>
    <LoadingComponent :props="loading"/>
    <SmModalCreateComponent :props="addButton"/>

    <div id="modal" class="modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.kitchen_settings") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500" @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.kitchen_name") }}</label>
                            <input v-model="props.form.name" type="text" class="db-field-control" :class="errors.name ? 'invalid' : ''"/>
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title">{{ $t("label.outlet") }}</label>
                            <input type="text" class="db-field-control" :value="outletName" disabled/>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.assigned_printer") }}</label>
                            <select v-model.number="props.form.printer_id" class="db-field-control" :class="errors.printer_id ? 'invalid' : ''">
                                <option :value="null">--</option>
                                <option v-for="printer in printers" :key="printer.id" :value="printer.id">{{ printer.name }}</option>
                            </select>
                            <small class="db-field-alert" v-if="errors.printer_id">{{ errors.printer_id[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t('label.status') }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model.number="props.form.status" type="radio" id="ks_active" class="custom-radio-field"/>
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="ks_active" class="db-field-label">{{ $t('label.active') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model.number="props.form.status" type="radio" id="ks_inactive" class="custom-radio-field"/>
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="ks_inactive" class="db-field-label">{{ $t('label.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-col-12">
                            <label class="db-field-title">{{ $t("label.categories") }}</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto border border-gray-100 rounded-lg p-3">
                                <label v-for="category in categories" :key="category.id" class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" :value="category.id" v-model="props.form.category_ids"/>
                                    <span>{{ category.name }}</span>
                                </label>
                            </div>
                            <small class="db-field-alert" v-if="errors.category_ids">{{ errors.category_ids[0] }}</small>
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
import SmModalCreateComponent from "../../components/buttons/SmModalCreateComponent.vue";
import LoadingComponent from "../../../common/LoadingComponent.vue";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import alertService from "../../../../services/alertService.js";
import {useModal} from "../../../../composables/modal.js";
import {useKitchenStationStore} from "../../../../stores/kitchenStation.js";
import {useAuthStore} from "../../../../stores/auth.js";

const defaultForm = () => ({
    name: "",
    printer_id: null,
    category_ids: [],
    sort_order: 0,
    status: statusEnum.ACTIVE,
});

export default {
    name: "KitchenStationCreateComponent",
    components: {SmModalCreateComponent, LoadingComponent},
    props: {
        props: {type: Object, required: true},
        printers: {type: Array, default: () => []},
        categories: {type: Array, default: () => []},
    },
    setup() {
        const kitchenStationStore = useKitchenStationStore();
        const authStore = useAuthStore();
        return {kitchenStationStore, authStore};
    },
    data() {
        return {
            loading: {isActive: false},
            addButton: {title: this.$t("button.add")},
            enums: {statusEnum},
            errors: {},
        };
    },
    computed: {
        outletName() {
            return this.authStore.info?.restaurant_name
                || this.authStore.info?.restaurant?.name
                || this.$t('label.outlet');
        },
    },
    methods: {
        reset() {
            useModal().closeModal('modal');
            this.kitchenStationStore.reset();
            this.errors = {};
            this.$props.props.form = defaultForm();
        },
        save() {
            const tempId = this.kitchenStationStore.temp.temp_id;
            this.loading.isActive = true;
            this.kitchenStationStore.save(this.props).then(() => {
                useModal().closeModal('modal');
                this.loading.isActive = false;
                alertService.successFlip(tempId === null ? 0 : 1, this.$t("menu.kitchen_settings"));
                this.props.form = defaultForm();
                this.errors = {};
            }).catch((err) => {
                this.loading.isActive = false;
                this.errors = err.response?.data?.errors || {};
                if (err.response?.data?.message) {
                    alertService.error(err.response.data.message);
                }
            });
        },
    },
};
</script>
