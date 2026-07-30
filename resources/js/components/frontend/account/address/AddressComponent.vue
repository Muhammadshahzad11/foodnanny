<template>
    <LoadingComponent :props="loading"/>
    <section class="pt-8 pb-24 md:pb-16">
        <div class="container max-w-3xl">
            <div class="flex items-center justify-between gap-3 mb-5">
                <h3 class="capitalize text-2xl font-semibold">{{ $t('label.address') }}</h3>
                <AddressCreateComponent :getLocation="updateAddress" :props="address"/>
            </div>
            <div class="row">
                <div class="col-12 sm:col-6" v-if="addresses.length > 0" v-for="address in addresses" :key="address">
                    <div class="p-3 rounded-lg bg-gray-100">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="flex items-center gap-2 text-[#008BBA]">
                                <i class="lab-fill-home" v-if="address.label === 'Home'"></i>
                                <i class="lab-fill-briefcase" v-else-if="address.label === 'Work'"></i>
                                <i class="lab-fill-more-square" v-else></i>
                                <span class="font-medium leading-6 capitalize">{{ address.label }}</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <button data-modal="#address" type="button" @click="edit(address)"
                                        class="w-6 h-6 rounded-full bg-primary flex items-center justify-center">
                                    <i class="lab-fill-edit text-sm text-white"></i>
                                </button>
                                <button type="button" @click="destroy(address.id)"
                                        class="w-6 h-6 rounded-full bg-[#FB4E4E] flex items-center justify-center">
                                    <i class="lab-fill-delete text-xs text-white"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="lab-fill-location text-sm text-paragraph"></i>
                            <span class="text-sm">
                                {{ address.apartment ? address.apartment + ', ' : '' }}
                                {{ address.address }}
                            </span>
                        </div>
                    </div>
                </div>
                <div v-else class="w-full py-10 flex flex-col items-center justify-center text-center">
                    <img class="w-40" :src="setting.image_address" alt="empty">
                    <h3 class="text-lg text-gray-300">{{ $t('message.no_address_found') }}</h3>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import {useFrontendAddressStore} from "../../../../stores/frontendAddress.js";
import AddressCreateComponent from "./AddressCreateComponent.vue";
import VueSimpleAlert from "vue3-simple-alert";
import alertService from "../../../../services/alertService.js";
import labelEnum from "../../../../enums/modules/labelEnum.js";
import {useModal} from "../../../../composables/modal.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";

export default {
    name: "AddressComponent",
    components: {
        LoadingComponent,
        AddressCreateComponent,
    },
    setup() {
        const frontendAddressStore = useFrontendAddressStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            frontendAddressStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            localAddress: {},
            address: {
                form: {
                    address: "",
                    apartment: "",
                    latitude: "",
                    longitude: "",
                    label: "",
                },
                search: {
                    paginate: 0,
                    order_column: "id",
                    order_type: "asc",

                },
                status: false,
                switchLabel: "",
                isMap: false
            }
        }
    },
    mounted() {
        this.list();
    },
    computed: {
        addresses: function () {
            return this.frontendAddressStore.lists;
        },
        setting: function () {
            return this.frontendSettingStore.lists;
        }
    },
    methods: {
        list: function () {
            this.loading.isActive = true;
            this.frontendAddressStore.fetch({
                search: this.address.search
            }).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        updateAddress: function (address) {
            this.localAddress = address;
        },
        edit: function (address) {
            useModal().openModal('address');
            this.loading.isActive = true;
            this.frontendAddressStore.edit(address.id);
            this.loading.isActive = false;
            this.address.isMap    = true;
            this.address.form     = {
                address: address.address,
                apartment: address.apartment,
                latitude: address.latitude,
                longitude: address.longitude,
                label: address.label
            };

            if (this.address.form.label === this.$t("label.home")) {
                this.address.status      = false;
                this.address.switchLabel = labelEnum.HOME;
            } else if (this.address.form.label === this.$t("label.work")) {
                this.address.status      = false;
                this.address.switchLabel = labelEnum.WORK;
            } else {
                this.address.status      = true;
                this.address.switchLabel = labelEnum.OTHER;
            }
        },
        destroy: function (addressId) {
            return new VueSimpleAlert.confirm(
                this.$t("message.delete_record"),
                this.$t("message.are_you_sure"),
                "warning",
                {
                    confirmButtonText: this.$t("button.yes_delete"),
                    cancelButtonText: this.$t("button.no_cancel"),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then((res) => {
                try {
                    this.loading.isActive = true;
                    this.frontendAddressStore.destroy({
                        id: addressId,
                        search: this.address.search,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("label.address"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    }
}
</script>
