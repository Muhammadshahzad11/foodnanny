<template>
    <LoadingComponent :props="loading"/>
    <AddressCreateModalComponent @open="openManualByDefault" :props="addButton"/>

    <div id="new-address-modal"
         class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-lg w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="flex items-center justify-between gap-4 py-4 px-6 border-b border-slate-100">
                <h3 class="text-lg font-semibold capitalize">{{ $t('label.address') }}</h3>
                <button @click.prevent="reset" type="button" class="lab-line-circle-cross text-lg text-danger"></button>
            </div>

            <div class="p-6">
                <div class="mb-5 flex items-center justify-center p-1 rounded-full bg-mate w-fit mx-auto">
                    <button type="button"
                            @click.prevent="setEntryMode('manual')"
                            :class="entryMode === 'manual' ? 'text-white bg-secondary' : ''"
                            class="text-sm capitalize h-8 px-3 rounded-full">
                        {{ $t('label.enter_manually') }}
                    </button>
                    <button type="button"
                            @click.prevent="setEntryMode('map')"
                            :class="entryMode === 'map' ? 'text-white bg-secondary' : ''"
                            class="text-sm capitalize h-8 px-3 rounded-full">
                        {{ $t('label.use_map') }}
                    </button>
                </div>

                <p v-if="entryMode === 'manual'" class="mb-4 text-xs text-paragraph text-center">
                    {{ $t('message.manual_address_temporary_note') }}
                </p>

                <MapComponent
                    :key="mapKey"
                    v-if="entryMode === 'map' && props.isMap"
                    :location="{lat : props.form.latitude, lng : props.form.longitude}"
                    :position="location"/>

                <form @submit.prevent="save">
                    <div v-if="entryMode === 'manual'" class="mb-5">
                        <label class="text-xs mb-2 capitalize block required">{{ $t('label.address') }}</label>
                        <textarea
                            v-model="props.form.address"
                            rows="3"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200"
                            :placeholder="$t('label.enter_full_address')"></textarea>
                        <small class="db-field-alert" v-if="errors.address">{{ errors.address[0] }}</small>
                    </div>

                    <div v-else-if="props.form.address" class="flex items-center gap-2 mb-5">
                        <i class="lab-fill-location text-xl text-primary"></i>
                        <span class="text-sm text-heading">{{ props.form.address }}</span>
                    </div>

                    <div class="mb-5">
                        <label class="text-xs mb-2 capitalize block">{{ $t('label.apartment_and_flat') }}</label>
                        <input v-model="props.form.apartment" type="text" class="w-full h-12 px-4 rounded-lg border border-gray-200"/>
                        <small class="db-field-alert" v-if="errors.apartment">{{ errors.apartment[0] }}</small>
                    </div>
                    <div class="mb-5">
                        <label class="font-medium capitalize mb-2 block">{{ $t('label.add_label') }}</label>
                        <div class="flex flex-wrap items-start gap-3">
                            <button type="button" @click="changeSwitchLabel(labelEnum.HOME)" v-on:click="this.props.status = false; this.props.form.label = $t('label.home')" :value="labelEnum.HOME" :class="props.switchLabel === labelEnum.HOME ? 'bg-primary/5 border-primary/30' : 'border-gray-100 bg-gray-100'" class="flex items-center justify-center gap-2 px-4 h-10 rounded-lg border">
                                <i class="lab-fill-home -mt-0.5 text-paragraph"></i>
                                <span class="text-sm font-medium capitalize">{{ $t('label.home') }}</span>
                            </button>
                            <button type="button" @click="changeSwitchLabel(labelEnum.WORK)" v-on:click="this.props.status = false; this.props.form.label = $t('label.work')" :value="labelEnum.WORK" :class="props.switchLabel === labelEnum.WORK ? 'bg-primary/5 border-primary/30' : 'border-gray-100 bg-gray-100'" class="flex items-center justify-center gap-2 px-4 h-10 rounded-lg border">
                                <i class="lab-fill-briefcase -mt-0.5 text-paragraph"></i>
                                <span class="text-sm font-medium capitalize">{{ $t('label.work') }}</span>
                            </button>
                            <button type="button" @click="changeSwitchLabel(labelEnum.OTHER)" v-on:click="this.props.status = true; this.props.form.label = ''; this.errors.label = ''" :value="labelEnum.OTHER" :class="props.switchLabel === labelEnum.OTHER ? 'bg-primary/5 border-primary/30' : 'border-gray-100 bg-gray-100'" class="flex items-center justify-center gap-2 px-4 h-10 rounded-lg border">
                                <i class="lab-fill-box -mt-0.5 text-paragraph"></i>
                                <span class="text-sm font-medium capitalize">{{ $t('label.other') }}</span>
                            </button>
                        </div>

                        <small class="db-field-alert" v-if="errors.label && props.switchLabel !== labelEnum.OTHER">
                            {{ errors.label[0] }}
                        </small>

                        <div v-if="props.status" :class="!props.status ? 'h-0' : ''" class="overflow-hidden transition">
                            <input type="text" :placeholder="$t('label.type_label_name')" v-model="props.form.label" v-bind:class="errors.label ? 'invalid' : ''" class="w-full py-2 px-4 mt-4 rounded-lg placeholder:text-xs border border-gray-200">
                            <small class="db-field-alert" v-if="errors.label">{{ errors.label[0] }}</small>
                        </div>
                    </div>
                    <button type="submit" class="w-full h-12 leading-12 px-4 text-center rounded-3xl capitalize font-medium bg-primary text-white">
                        {{ entryMode === 'manual' ? $t('button.save_address') : $t('button.confirm_location') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import AddressCreateModalComponent from "../components/AddressCreateModalComponent.vue";
import MapComponent from "../../common/MapComponent.vue";
import {useModal} from "../../../composables/modal.js";
import labelEnum from "../../../enums/modules/labelEnum.js";
import {useFrontendAddressStore} from "../../../stores/frontendAddress.js";
import {useFrontendCartStore} from "../../../stores/frontendCart.js";
import alertService from "../../../services/alertService.js";

export default {
    name: "AddressComponent",
    components: {LoadingComponent, AddressCreateModalComponent, MapComponent},
    props: {
        props: Object,
        getLocation: Function
    },
    setup() {
        const {closeModal, openModal} = useModal();
        const frontendAddressStore = useFrontendAddressStore();
        const frontendCartStore = useFrontendCartStore();

        return {
            closeModal,
            openModal,
            frontendAddressStore,
            frontendCartStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            addButton: {
                title: this.$t("button.add"),
            },
            mapKey: "create-update",
            labelEnum: labelEnum,
            errors: {},
            entryMode: 'manual',
            // Temporary defaults for testing without Google Maps
            defaultLatitude: '24.8607',
            defaultLongitude: '67.0011',
        }
    },
    computed: {
        restaurant() {
            return this.frontendCartStore.restaurant || {};
        }
    },
    methods: {
        openManualByDefault() {
            this.setEntryMode('manual');
            this.openModal('new-address-modal');
        },
        setEntryMode(mode) {
            this.entryMode = mode;
            this.props.isMap = mode === 'map';
            if (mode === 'manual') {
                this.applyDefaultCoordinates();
            }
        },
        applyDefaultCoordinates() {
            const lat = this.restaurant.latitude || this.defaultLatitude;
            const lng = this.restaurant.longitude || this.defaultLongitude;
            if (!this.props.form.latitude) {
                this.props.form.latitude = String(lat);
            }
            if (!this.props.form.longitude) {
                this.props.form.longitude = String(lng);
            }
        },
        changeSwitchLabel: function (id) {
            this.props.switchLabel = id;
        },
        location: function (e) {
            this.props.form.latitude  = e.location.lat;
            this.props.form.longitude = e.location.lng;
            this.props.form.address   = e.address;
        },
        reset: function () {
            this.closeModal('new-address-modal');
            this.frontendAddressStore.reset();
            this.errors                   = {};
            this.entryMode                = 'manual';
            this.$props.props.form        = {
                address: "",
                apartment: "",
                latitude: "",
                longitude: "",
                label: "",
            };
            this.$props.props.status      = false;
            this.$props.props.switchLabel = "";
            this.$props.props.isMap       = false;
        },
        save: function () {
            try {
                if (this.entryMode === 'manual') {
                    this.applyDefaultCoordinates();
                    if (!this.props.form.address || !String(this.props.form.address).trim()) {
                        this.errors = {address: [this.$t('message.address_required')]};
                        return;
                    }
                    if (!this.props.form.label) {
                        this.errors = {label: [this.$t('message.label_required')]};
                        return;
                    }
                }

                const tempId          = this.frontendAddressStore.temp.temp_id;
                this.loading.isActive = true;
                this.frontendAddressStore.save(this.props).then((res) => {
                    this.getLocation(res.data.data);
                    this.closeModal('new-address-modal');
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("label.address"));
                    this.props.form        = {
                        address: "",
                        apartment: "",
                        latitude: "",
                        longitude: "",
                        label: "",
                    };
                    this.props.isMap       = false;
                    this.props.status      = false;
                    this.props.switchLabel = "";
                    this.entryMode         = 'manual';
                    this.errors            = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response?.data?.errors || {};
                    if (err.response?.data?.message && !this.errors.address) {
                        alertService.error(err.response.data.message);
                    }
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
