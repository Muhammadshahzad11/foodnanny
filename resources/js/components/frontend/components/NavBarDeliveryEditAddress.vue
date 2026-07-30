<template>
    <div id="edit-delivery-address"
         class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-lg w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="flex items-center justify-between gap-4 py-4 px-6">
                <h3 class="text-lg font-semibold capitalize">{{ $t('label.edit_delivery_address') }}</h3>
                <button @click.prevent="closeModalEvent" class="lab-line-circle-cross text-lg text-danger"></button>
            </div>

            <div class="p-6">
                <MapComponent :key="mapKey" v-if="addressProps.isMap" :location="{ lat: addressProps.form.latitude, lng: addressProps.form.longitude }" :position="getLocation"/>
                <form @submit.prevent="save">
                    <div v-if="addressProps.form.address" class="flex items-center gap-2 mb-5">
                        <i class="lab-fill-location text-xl text-primary"></i>
                        <span class="text-sm text-heading">{{ addressProps.form.address }}</span>
                    </div>

                    <div class="mb-5">
                        <label class="text-xs mb-2 capitalize block">{{ $t('label.apartment_and_flat') }}</label>
                        <input v-model="addressProps.form.apartment" type="text"
                               class="w-full h-12 px-4 rounded-lg border border-gray-200"/>
                    </div>
                    <div class="mb-5">
                        <label class="font-medium capitalize mb-2 block">{{ $t('label.add_label') }}</label>
                        <div class="flex flex-wrap items-start gap-3">
                            <button type="button" @click="changeSwitchLabel(labelEnum.HOME)"
                                    v-on:click="this.addressProps.status = false; this.addressProps.form.label = $t('label.home')"
                                    :value="labelEnum.HOME"
                                    :class="addressProps.switchLabel === labelEnum.HOME ? 'bg-primary/5 border-primary/30' : 'border-gray-100 bg-gray-100'"
                                    class="flex items-center justify-center gap-2 px-4 h-10 rounded-lg border">
                                <i class="lab-fill-home -mt-0.5 text-paragraph"></i>
                                <span class="text-sm font-medium capitalize">{{ $t('label.home') }}</span>
                            </button>
                            <button type="button" @click="changeSwitchLabel(labelEnum.WORK)"
                                    v-on:click="this.addressProps.status = false; this.addressProps.form.label = $t('label.work')"
                                    :value="labelEnum.WORK"
                                    :class="addressProps.switchLabel === labelEnum.WORK ? 'bg-primary/5 border-primary/30' : 'border-gray-100 bg-gray-100'"
                                    class="flex items-center justify-center gap-2 px-4 h-10 rounded-lg border">
                                <i class="lab-fill-briefcase -mt-0.5 text-paragraph"></i>
                                <span class="text-sm font-medium capitalize">{{ $t('label.work') }}</span>
                            </button>
                            <button type="button" @click="changeSwitchLabel(labelEnum.OTHER)"
                                    v-on:click="this.addressProps.status = true; this.addressProps.form.label = ''; this.errors.label = ''"
                                    :value="labelEnum.OTHER"
                                    :class="addressProps.switchLabel === labelEnum.OTHER ? 'bg-primary/5 border-primary/30' : 'border-gray-100 bg-gray-100'"
                                    class="flex items-center justify-center gap-2 px-4 h-10 rounded-lg border">
                                <i class="lab-fill-box -mt-0.5 text-paragraph"></i>
                                <span class="text-sm font-medium capitalize">{{ $t('label.other') }}</span>
                            </button>
                        </div>

                        <small class="db-field-alert"
                               v-if="errors.label && addressProps.switchLabel !== labelEnum.OTHER">
                            {{ errors.label[0] }}
                        </small>

                        <div v-if="addressProps.status" :class="!addressProps.status ? 'h-0' : ''"
                             class="overflow-hidden transition">
                            <input type="text" :placeholder="$t('label.type_label_name')"
                                   v-model="addressProps.form.label" v-bind:class="errors.label ? 'invalid' : ''"
                                   class="w-full py-2 px-4 mt-4 rounded-lg placeholder:text-xs border border-gray-200">
                            <small class="db-field-alert" v-if="errors.label">{{ errors.label[0] }}</small>
                        </div>
                    </div>
                    <button type="submit"
                            class="w-full h-12 leading-12 px-4 text-center rounded-3xl capitalize font-medium bg-primary text-white">
                        {{ $t('button.confirm_location') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import {useCommonStore} from "../../../stores/common.js";
import {useModal} from "../../../composables/modal.js";
import labelEnum from "../../../enums/modules/labelEnum.js";
import {useFrontendAddressStore} from "../../../stores/frontendAddress.js";
import alertService from "../../../services/alertService.js";
import MapComponent from "../../common/MapComponent.vue";


export default {
    name: "NavbarDeliveryEditAddress",
    components: {MapComponent},
    setup() {
        const {closeModal}         = useModal();
        const commonStore          = useCommonStore();
        const frontendAddressStore = useFrontendAddressStore();

        return {
            closeModal,
            commonStore,
            frontendAddressStore
        }
    },
    data() {
        return {
            mapKey: "delivery-create-update",
            labelEnum: labelEnum,
            errors: {},
            loading: {
                isActive: true
            },
            addressProps: {
                form: {
                    address: "",
                    apartment: "",
                    latitude: "",
                    longitude: "",
                    label: "",
                },
                search: {
                    paginate: 0,
                    order_column: 'id',
                    order_type: 'asc'
                },
                status: false,
                switchLabel: "",
                isMap: false,
            }
        }
    },
    async mounted() {
        this.loading.isActive = true;
        let addressId         = this.commonStore.edit_address_id;
        if (addressId) {
            await this.frontendAddressStore.edit(addressId);
            this.frontendAddressStore.view(addressId).then(res => {
                this.loading.isActive            = false;
                this.addressProps.form.address   = res.data.data.address;
                this.addressProps.form.apartment = res.data.data.apartment;
                this.addressProps.form.latitude  = res.data.data.latitude;
                this.addressProps.form.longitude = res.data.data.longitude;
                this.addressProps.form.label     = res.data.data.label;

                if (this.addressProps.form.label !== labelEnum.HOME && this.addressProps.form.label !== labelEnum.WORK) {
                    this.addressProps.status      = true;
                    this.addressProps.switchLabel = labelEnum.OTHER;
                } else {
                    this.addressProps.switchLabel = res.data.data.label;
                }
                this.addressProps.isMap = true;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        }
    },
    methods: {
        changeSwitchLabel: function (id) {
            this.addressProps.switchLabel = id;
        },
        closeModalEvent: async function () {
            this.closeModal('edit-delivery-address');
            await this.commonStore.update({edit_address_id: null});
        },
        getLocation: function (e) {
            this.addressProps.form.address   = e.address;
            this.addressProps.form.latitude  = e.location.lat;
            this.addressProps.form.longitude = e.location.lng;
        },
        save: function () {
            try {
                const tempId          = this.frontendAddressStore.temp.temp_id;
                this.loading.isActive = true;
                this.frontendAddressStore.save(this.addressProps).then((res) => {
                    this.commonStore.update({
                        edit_address_id: null,
                        location: this.addressProps.form.address,
                        latitude: this.addressProps.form.latitude,
                        longitude: this.addressProps.form.longitude
                    });
                    this.closeModal('edit-delivery-address');
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("label.address"));
                    this.addressProps.form        = {
                        address: "",
                        apartment: "",
                        latitude: "",
                        longitude: "",
                        label: "",
                    };
                    this.addressProps.isMap       = false;
                    this.addressProps.status      = false;
                    this.addressProps.switchLabel = "";
                    this.errors                   = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
            }
        }
    }
}
</script>
