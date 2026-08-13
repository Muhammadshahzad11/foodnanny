<template>
    <LoadingComponent :props="loading"/>

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">
                {{ isEdit ? $t('label.update_delivery_zone') : $t('label.add_new_delivery_zone') }}
            </h3>
            <router-link
                :to="{ name: 'admin.deliveryZones.list' }"
                class="db-btn py-2 px-3 text-sm text-slate-700 bg-slate-100"
            >
                {{ $t('button.back') || 'Back' }}
            </router-link>
        </div>

        <form class="p-4 sm:p-6" @submit.prevent="save">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Instructions -->
                <div class="lg:col-span-4 space-y-4">
                    <p class="text-sm text-slate-600">
                        {{ $t('label.zone_create_instruction') }}
                    </p>
                    <div class="flex gap-3 items-start">
                        <span class="w-9 h-9 rounded bg-slate-100 flex items-center justify-center text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 11V6a2 2 0 0 0-4 0"/><path d="M14 10V4a2 2 0 0 0-4 0v2"/><path d="M10 10.5V6a2 2 0 0 0-4 0v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg>
                        </span>
                        <p class="text-sm text-slate-600 m-0">{{ $t('label.hand_tool_help') }}</p>
                    </div>
                    <div class="flex gap-3 items-start">
                        <span class="w-9 h-9 rounded bg-slate-100 flex items-center justify-center text-primary shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4.5v9L12 20l-8-4.5v-9L12 2z"/></svg>
                        </span>
                        <p class="text-sm text-slate-600 m-0">{{ $t('label.shape_tool_help') }}</p>
                    </div>
                </div>

                <!-- Form + map -->
                <div class="lg:col-span-8 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="db-field-title required">{{ $t('label.zone_name') }}</label>
                            <input v-model="form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>
                        <div>
                            <label class="db-field-title required">{{ $t('label.display_name') }}</label>
                            <input v-model="form.display_name" v-bind:class="errors.display_name ? 'invalid' : ''" type="text" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.display_name">{{ errors.display_name[0] }}</small>
                        </div>
                    </div>

                    <ZonePolygonMapComponent
                        v-if="mapReady"
                        v-model="form.polygon"
                        :restaurant-location="restaurantLocation"
                    />
                    <small class="db-field-alert" v-if="errors.polygon">{{ errors.polygon[0] || errors.polygon }}</small>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="db-field-title required">{{ $t('label.status') }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model="form.status" id="dz-form-active" type="radio" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="dz-form-active" class="db-field-label">{{ $t('label.active') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model="form.status" id="dz-form-inactive" type="radio" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="dz-form-inactive" class="db-field-label">{{ $t('label.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="db-btn py-2 px-4 text-slate-700 bg-white border border-gray-200" @click="resetForm">
                            {{ $t('button.reset') }}
                        </button>
                        <button type="submit" class="db-btn py-2 px-4 text-white bg-primary">
                            {{ isEdit ? $t('button.update') : $t('button.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import ZonePolygonMapComponent from "./ZonePolygonMapComponent.vue";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import alertService from "../../../../services/alertService.js";
import {useRestaurantDeliveryZoneStore} from "../../../../stores/restaurantDeliveryZone.js";

export default {
    name: "DeliveryZoneFormComponent",
    components: {LoadingComponent, ZonePolygonMapComponent},
    setup() {
        const zoneStore = useRestaurantDeliveryZoneStore();
        return {zoneStore};
    },
    data() {
        return {
            loading: {isActive: false},
            mapReady: false,
            errors: {},
            form: this.emptyForm(),
            enums: {statusEnum},
            initialSnapshot: null,
        };
    },
    computed: {
        isEdit() {
            return !!this.$route.params.id;
        },
        restaurantLocation() {
            return {lat: null, lng: null};
        },
    },
    async mounted() {
        this.loading.isActive = true;
        try {
            if (this.isEdit) {
                const res = await this.zoneStore.show(this.$route.params.id);
                const zone = res.data.data;
                this.form = {
                    name: zone.name,
                    display_name: zone.display_name,
                    polygon: zone.polygon || [],
                    status: zone.status,
                    base_delivery_fee: zone.base_delivery_fee ?? "0",
                    min_order_amount: zone.min_order_amount ?? "",
                    free_delivery_above: zone.free_delivery_above ?? "",
                    free_delivery_km: zone.free_delivery_km ?? "0",
                    extra_distance_charge: zone.extra_distance_charge ?? "0",
                    peak_enabled: zone.peak_enabled ?? 0,
                    peak_charge: zone.peak_charge ?? "0",
                };
                this.zoneStore.edit(zone.id);
            } else {
                this.zoneStore.reset();
            }
            this.initialSnapshot = JSON.stringify(this.form);
            this.mapReady = true;
        } catch (e) {
            alertService.error(e.response?.data?.message || e);
            this.$router.push({name: 'admin.deliveryZones.list'});
        } finally {
            this.loading.isActive = false;
        }
    },
    methods: {
        emptyForm() {
            return {
                name: "",
                display_name: "",
                polygon: [],
                status: statusEnum.ACTIVE,
                base_delivery_fee: "0",
                min_order_amount: "",
                free_delivery_above: "",
                free_delivery_km: "0",
                extra_distance_charge: "0",
                peak_enabled: 0,
                peak_charge: "0",
            };
        },
        resetForm() {
            if (this.initialSnapshot) {
                this.form = JSON.parse(this.initialSnapshot);
            } else {
                this.form = this.emptyForm();
            }
            this.errors = {};
            // Remount map so polygon redraws cleanly
            this.mapReady = false;
            this.$nextTick(() => {
                this.mapReady = true;
            });
        },
        save() {
            const pts = this.form.polygon || [];
            if (pts.length < 3) {
                this.errors = {polygon: [this.$t('message.zone_polygon_required')]};
                return;
            }
            this.loading.isActive = true;
            this.errors = {};
            const payload = {
                form: {
                    ...this.form,
                    min_order_amount: this.form.min_order_amount === '' ? null : this.form.min_order_amount,
                    free_delivery_above: this.form.free_delivery_above === '' ? null : this.form.free_delivery_above,
                },
                search: {paginate: 1, page: 1, per_page: 10},
            };

            this.zoneStore.save(payload).then((res) => {
                this.loading.isActive = false;
                // No blocking popup — go to settings for new zones, list for edits
                if (!this.isEdit && res?.data?.data?.id) {
                    this.$router.push({
                        name: 'admin.deliveryZones.settings',
                        params: {id: res.data.data.id},
                    });
                } else {
                    this.$router.push({name: 'admin.deliveryZones.list'});
                }
            }).catch((err) => {
                this.loading.isActive = false;
                this.errors = err.response?.data?.errors || {};
                if (err.response?.data?.message && !Object.keys(this.errors).length) {
                    alertService.error(err.response.data.message);
                }
            });
        },
    },
};
</script>
