<template>
    <LoadingComponent :props="loading"/>
    <button @click="add" v-on:click="this.props.isMap = true" type="button"
            class="flex items-center gap-1.5 px-2.5 h-8 rounded-3xl text-primary bg-primary/5">
        <i class="lab-fill-add-circle text-sm -mt-[1px]"></i>
        <span class="text-sm font-medium capitalize whitespace-nowrap">{{ addButton.title }}</span>
    </button>

    <div id="address" class="modal address ff-modal">
        <div class="modal-dialog">
            <div class="modal-header border-none pb-0">
                <h3 class="capitalize font-medium">{{ $t('label.your_address') }}</h3>
                <button class="lab-line-circle-cross text-lg text-danger" @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <MapComponent :key="mapKey" v-if="props.isMap" :location="{ lat: props.form.latitude, lng: props.form.longitude }" :position="location"/>
                    <div class="flex items-center gap-2 mb-3">
                        <i class="lab lab-fill-location text-xl text-primary"></i>
                        <span class="text-sm text-heading">{{ props.form.address }}</span>
                    </div>
                    <div class="mb-5">
                        <label for="apartment" class="text-xs mb-2 capitalize block">
                            {{ $t('label.apartment_and_flat') }}
                        </label>
                        <textarea id="apartment" v-model="props.form.apartment" class="h-12 w-full rounded-lg border py-1.5 px-2 placeholder:text-[10px] placeholder:text-[#6E7191] border-[#D9DBE9]"></textarea>
                    </div>
                    <div class="mb-5">
                        <h3 class="font-medium capitalize mb-2 block">{{ $t('label.add_label') }}</h3>
                        <nav class="flex flex-wrap gap-3 active-group">
                            <button @click="changeSwitchLabel(enums.labelEnum.HOME)"
                                    :class="props.switchLabel === enums.labelEnum.HOME ? 'bg-primary/5 border-primary/30' : 'border-gray-100 bg-gray-100'"
                                    v-on:click="this.props.status = false; this.props.form.label = $t('label.home')"
                                    :value="enums.labelEnum.HOME" type="button"
                                    class="flex items-center justify-center gap-2 px-4 h-10 rounded-lg border">
                                <i class="lab-fill-home -mt-0.5 text-paragraph"></i>
                                <span class="text-sm capitalize font-medium leading-none text-heading">
                                    {{ $t('label.home') }}
                                </span>
                            </button>
                            <button @click="changeSwitchLabel(enums.labelEnum.WORK)"
                                    :class="props.switchLabel === enums.labelEnum.WORK ? 'bg-primary/5 border-primary/30' : 'border-gray-100 bg-gray-100'"
                                    v-on:click="this.props.status = false; this.props.form.label = $t('label.work')"
                                    :value="enums.labelEnum.WORK" type="button"
                                    class="flex items-center justify-center gap-2 px-4 h-10 rounded-lg border">
                                <i class="lab-fill-briefcase -mt-0.5 text-paragraph"></i>
                                <span class="text-sm capitalize font-medium leading-none text-heading">
                                    {{ $t('label.work') }}
                                </span>
                            </button>
                            <button @click="changeSwitchLabel(enums.labelEnum.OTHER)"
                                    :class="props.switchLabel === enums.labelEnum.OTHER ? 'bg-primary/5 border-primary/30' : 'border-gray-100 bg-gray-100'"
                                    v-on:click="this.props.status = true; this.props.form.label = ''; this.errors.label = ''"
                                    :value="enums.labelEnum.OTHER" type="button"
                                    class="flex items-center justify-center gap-2 px-4 h-10 rounded-lg border">
                                <i class="lab-fill-box -mt-0.5 text-paragraph"></i>
                                <span class="text-sm capitalize font-medium leading-none text-heading">
                                    {{ $t('label.other') }}
                                </span>
                            </button>
                        </nav>
                        <small class="db-field-alert"
                               v-if="errors.label && props.switchLabel !== enums.labelEnum.OTHER">
                            {{ errors.label[0] }}
                        </small>
                        <div v-if="props.status" :class="!props.status ? 'h-0' : ''" class="overflow-hidden transition">
                            <input type="text" :placeholder="$t('label.type_label_name')" v-model="props.form.label"
                                   :class="errors.label ? 'invalid' : ''"
                                   class="h-10 w-full rounded-lg border mt-5 py-1.5 px-4 placeholder:text-xs border-[#D9DBE9]">
                            <small class="db-field-alert" v-if="errors.label">{{ errors.label[0] }}</small>
                        </div>
                    </div>
                    <button type="submit"
                            class="rounded-3xl text-base py-3 px-3 font-medium w-full text-white bg-primary">
                        {{ $t('button.confirm_location') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import {useModal} from "../../../../composables/modal.js";
import labelEnum from "../../../../enums/modules/labelEnum.js";
import MapComponent from "../../../common/MapComponent.vue";
import {useFrontendAddressStore} from "../../../../stores/frontendAddress.js";
import alertService from "../../../../services/alertService.js";

export default {
    name: "AddressComponent",
    components: {
        MapComponent,
        LoadingComponent
    },
    props: {
        props: Object,
        getLocation: Function
    },
    setup() {
        const {openModal, closeModal} = useModal();
        const frontendAddressStore    = useFrontendAddressStore();

        return {
            openModal,
            closeModal,
            frontendAddressStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            addButton: {
                title: this.$t("button.add_new"),
            },
            enums: {
                labelEnum: labelEnum,
            },
            mapKey: "create-update",
            errors: {}
        }
    },
    methods: {
        add: function () {
            this.openModal('address');
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
            this.closeModal('address');
            this.frontendAddressStore.reset();
            this.errors                   = {};
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
                const tempId          = this.frontendAddressStore.temp.temp_id;
                this.loading.isActive = true;
                this.frontendAddressStore.save(this.props).then((res) => {
                    this.getLocation(res.data.data);
                    this.closeModal('address');
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("label.address"));
                    this.props.form        = {
                        address: "",
                        apartment: "",
                        latitude: "",
                        longitude: "",
                        label: ""
                    };
                    this.props.isMap       = false;
                    this.props.status      = false;
                    this.props.switchLabel = "";
                    this.errors            = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
