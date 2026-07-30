<template>
    <h3 class="font-medium capitalize">{{ $t('label.saved_address') }}</h3>
    <ul class="w-full flex flex-col gap-6 py-4">
        <li v-for="address in addresses"
            class="w-full flex items-start gap-4 cursor-pointer group rounded-lg p-2 hover:bg-primary/10">
            <input @click.prevent="selectAddress(address)" type="radio" :checked="selectedAddress!== null && selectedAddress.id === address.id" :value="address.id" v-model="selectedAddress" class="cs-custom-radio flex-shrink-0 mt-1">
            <dl @click="selectAddress(address)" class="flex-auto">
                <dt :class="{'text-primary': selectedAddress!== null && selectedAddress.id === address.id}" class="text-sm font-medium capitalize mb-2">
                    {{ address.label }}
                </dt>
                <dd :class="{'text-primary': selectedAddress!== null && selectedAddress.id === address.id}" class="text-sm max-w-[220px]"><span v-if="address.apartment">{{ address.apartment }},</span>
                    {{ address.address }}
                </dd>
            </dl>
            <button type="button" @click.prevent="selectEditAddress(address)" class="invisible opacity-0 group-hover:visible group-hover:opacity-100 self-center w-10 h-10 rounded-full flex items-center justify-center shadow-xs transition-all duration-300 text-primary bg-white">
                <i class="lab-fill-edit text-xl"></i>
            </button>
        </li>
    </ul>
</template>

<script>
import {useModal} from "../../../composables/modal.js";
import {useCommonStore} from "../../../stores/common.js";
import {useFrontendAddressStore} from "../../../stores/frontendAddress.js";

export default {
    name: "NavbarDeliverySaveAddress",
    setup() {
        const {openModal, closeModal} = useModal();
        const commonStore             = useCommonStore();
        const frontendAddressStore    = useFrontendAddressStore();

        return {
            openModal,
            closeModal,
            commonStore,
            frontendAddressStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            selectedAddress: null,
            selectedEditAddress: null,
            propsAddress: {
                search: {
                    paginate: 0,
                    order_column: 'id',
                    order_type: 'asc'
                }
            }
        }
    },
    computed: {
        addresses: function () {
            return this.frontendAddressStore.lists;
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.frontendAddressStore.fetch(this.propsAddress).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        selectEditAddress: async function (address) {
            this.selectedAddress     = null;
            this.selectedEditAddress = address;
            this.closeModal('delivery-address');
            await this.commonStore.update({edit_address_id: this.selectedEditAddress.id});
            this.openModal('edit-delivery-address');
        },
        selectAddress: async function (address) {
            this.selectedAddress = address;
            if (this.selectedAddress.latitude !== null && this.selectedAddress.longitude !== null) {
                await this.commonStore.update({
                    location: this.selectedAddress.address,
                    latitude: this.selectedAddress.latitude,
                    longitude: this.selectedAddress.longitude
                });
                this.closeModal('delivery-address');
            }
        }
    }
}
</script>
