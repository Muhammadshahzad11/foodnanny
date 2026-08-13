<template>
    <LoadingComponent :props="loading"/>

    <div class="db-card db-tab-div active mb-4">
        <div class="db-card-header border-none">
            <div>
                <h3 class="db-card-title">{{ $t('label.zone_settings') }} : {{ zoneName }}</h3>
                <p class="text-sm text-slate-500 mt-1 mb-0">{{ $t('label.zone_settings_help') }}</p>
            </div>
            <router-link
                :to="{ name: 'admin.deliveryZones.list' }"
                class="db-btn py-2 px-3 text-sm text-slate-700 bg-slate-100"
            >
                {{ $t('button.back') || 'Back' }}
            </router-link>
        </div>

        <form class="p-4 sm:p-6" @submit.prevent="saveFees">
            <h4 class="text-base font-semibold text-slate-800 mb-4">{{ $t('label.delivery_charge') }}</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="db-field-title">{{ $t('label.base_delivery_fee') }}</label>
                    <input v-model="feeForm.base_delivery_fee" type="text" class="db-field-control" placeholder="30"/>
                </div>
                <div>
                    <label class="db-field-title">{{ $t('label.min_order_amount') }}</label>
                    <input v-model="feeForm.min_order_amount" type="text" class="db-field-control" placeholder="0"/>
                </div>
                <div>
                    <label class="db-field-title">{{ $t('label.free_delivery_above') }}</label>
                    <input v-model="feeForm.free_delivery_above" type="text" class="db-field-control" placeholder="299"/>
                </div>
                <div>
                    <label class="db-field-title">{{ $t('label.free_delivery_km') }}</label>
                    <input v-model="feeForm.free_delivery_km" type="text" class="db-field-control" placeholder="0"/>
                </div>
                <div>
                    <label class="db-field-title">{{ $t('label.extra_distance_charge') }}</label>
                    <input v-model="feeForm.extra_distance_charge" type="text" class="db-field-control" placeholder="10"/>
                </div>
                <div>
                    <label class="db-field-title">{{ $t('label.peak_charge') }}</label>
                    <input v-model="feeForm.peak_charge" type="text" class="db-field-control" placeholder="0"/>
                </div>
                <div>
                    <label class="db-field-title">{{ $t('label.peak_enabled') }}</label>
                    <select v-model="feeForm.peak_enabled" class="db-field-control">
                        <option :value="0">{{ $t('label.off') }}</option>
                        <option :value="1">{{ $t('label.on') }}</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button type="submit" class="db-btn py-2 px-4 text-white bg-primary">{{ $t('button.save') }}</button>
            </div>
        </form>
    </div>

    <div class="db-card mb-4">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('label.restaurants_in_zone') }}</h3>
        </div>
        <div class="p-4 sm:p-6">
            <p class="text-sm text-slate-500 mt-0 mb-3">{{ $t('label.auto_assigned_from_map') }}</p>
            <ul v-if="zoneRestaurants.length" class="m-0 p-0 list-none divide-y divide-slate-100 border border-slate-200 rounded">
                <li v-for="restaurant in zoneRestaurants" :key="restaurant.id" class="px-3 py-2 text-sm text-slate-800">
                    {{ restaurant.name }}
                </li>
            </ul>
            <p v-else class="text-sm text-slate-500 mb-0">{{ $t('label.no_restaurants_in_zone') }}</p>
        </div>
    </div>

    <div class="db-card mb-4">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('label.zone_admin') }}</h3>
        </div>
        <form class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4" @submit.prevent="saveAdmin">
            <div class="sm:col-span-2 text-sm text-slate-600" v-if="zone && zone.admins && zone.admins.length">
                {{ $t('label.current') || 'Current' }}:
                <strong>{{ zone.admins.map(a => a.name).join(', ') }}</strong>
            </div>
            <div>
                <label class="db-field-title required">{{ $t('label.name') }}</label>
                <input v-model="adminForm.name" type="text" class="db-field-control"/>
            </div>
            <div>
                <label class="db-field-title required">{{ $t('label.email') }}</label>
                <input v-model="adminForm.email" type="email" class="db-field-control"/>
            </div>
            <div>
                <label class="db-field-title">{{ $t('label.phone') }}</label>
                <input v-model="adminForm.phone" type="text" class="db-field-control"/>
            </div>
            <div>
                <label class="db-field-title required">{{ $t('label.password') }}</label>
                <input v-model="adminForm.password" type="password" class="db-field-control"/>
            </div>
            <div class="sm:col-span-2 flex justify-end">
                <button type="submit" class="db-btn py-2 px-4 text-white bg-primary">{{ $t('button.save') }}</button>
            </div>
        </form>
    </div>

    <div class="db-card">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('label.riders_in_zone') }}</h3>
        </div>
        <div class="p-4 sm:p-6">
            <p class="text-sm text-slate-500 mt-0 mb-3">{{ $t('label.auto_assigned_from_map') }}</p>
            <ul v-if="zoneRiders.length" class="m-0 p-0 list-none divide-y divide-slate-100 border border-slate-200 rounded">
                <li v-for="rider in zoneRiders" :key="rider.id" class="px-3 py-2 text-sm text-slate-800">
                    {{ rider.name }} <span class="text-slate-500">({{ rider.email }})</span>
                </li>
            </ul>
            <p v-else class="text-sm text-slate-500 mb-0">{{ $t('label.no_riders_in_zone') }}</p>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import {useRestaurantDeliveryZoneStore} from "../../../../stores/restaurantDeliveryZone.js";

export default {
    name: "DeliveryZoneSettingsComponent",
    components: {LoadingComponent},
    setup() {
        const zoneStore = useRestaurantDeliveryZoneStore();
        return {zoneStore};
    },
    data() {
        return {
            loading: {isActive: false},
            zoneName: "",
            zone: null,
            zoneRestaurants: [],
            zoneRiders: [],
            feeForm: {
                base_delivery_fee: "0",
                min_order_amount: "",
                free_delivery_above: "",
                free_delivery_km: "0",
                extra_distance_charge: "0",
                peak_enabled: 0,
                peak_charge: "0",
            },
            adminForm: {
                name: "",
                email: "",
                phone: "",
                password: "",
                country_code: "91",
            },
        };
    },
    async mounted() {
        this.loading.isActive = true;
        try {
            await this.loadZone();
        } finally {
            this.loading.isActive = false;
        }
    },
    methods: {
        async loadZone() {
            const res = await this.zoneStore.show(this.$route.params.id);
            const zone = res.data.data;
            this.zone = zone;
            this.zoneName = zone.display_name || zone.name;
            this.feeForm = {
                base_delivery_fee: zone.base_delivery_fee ?? "0",
                min_order_amount: zone.min_order_amount ?? "",
                free_delivery_above: zone.free_delivery_above ?? "",
                free_delivery_km: zone.free_delivery_km ?? "0",
                extra_distance_charge: zone.extra_distance_charge ?? "0",
                peak_enabled: zone.peak_enabled ?? 0,
                peak_charge: zone.peak_charge ?? "0",
            };
            this.zoneRestaurants = zone.restaurants || [];
            this.zoneRiders = zone.delivery_boys || [];
        },
        saveFees() {
            if (!this.zone) return;
            this.loading.isActive = true;
            this.zoneStore.edit(this.zone.id);
            this.zoneStore.save({
                form: {
                    name: this.zone.name,
                    display_name: this.zone.display_name,
                    polygon: this.zone.polygon,
                    status: this.zone.status,
                    base_delivery_fee: this.feeForm.base_delivery_fee === '' ? 0 : this.feeForm.base_delivery_fee,
                    min_order_amount: this.feeForm.min_order_amount === '' ? null : this.feeForm.min_order_amount,
                    free_delivery_above: this.feeForm.free_delivery_above === '' ? null : this.feeForm.free_delivery_above,
                    free_delivery_km: this.feeForm.free_delivery_km === '' ? 0 : this.feeForm.free_delivery_km,
                    extra_distance_charge: this.feeForm.extra_distance_charge === '' ? 0 : this.feeForm.extra_distance_charge,
                    peak_enabled: this.feeForm.peak_enabled,
                    peak_charge: this.feeForm.peak_charge === '' ? 0 : this.feeForm.peak_charge,
                },
                search: {paginate: 1, page: 1, per_page: 10},
            }).then(() => {
                this.loading.isActive = false;
                this.loadZone();
                alertService.success(this.$t('message.update_success') || 'Saved');
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err);
            });
        },
        saveAdmin() {
            this.loading.isActive = true;
            this.zoneStore.assignAdmin(this.zone.id, this.adminForm).then(() => {
                this.loading.isActive = false;
                this.adminForm = {name: "", email: "", phone: "", password: "", country_code: "91"};
                this.loadZone();
                alertService.success(this.$t('message.update_success') || 'Saved');
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || err);
            });
        },
    },
};
</script>
