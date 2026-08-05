<template>
    <button v-if="order.order_type === enums.orderTypeEnum.TAKEAWAY" @click.prevent="showModal"
            class="w-10 h-10 flex-shrink-0 rounded-lg flex items-center justify-center bg-primary">
        <i class="lab-fill-map text-2xl text-white"></i>
    </button>

    <div id="mapModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header border-none pb-0">
                <h3 class="capitalize font-medium">{{ $t('label.address') }}</h3>
                <button class="lab-line-circle-cross text-lg text-danger" @click="hideModal"></button>
            </div>
            <div class="modal-body">
                <MapComponent :key="mapKey" v-if="mapShow" :location="{ lat: order.restaurant.latitude, lng: order.restaurant.longitude }" :position="mapPosition" :setting="{ autocomplete: false, mouseEvent: false, currentLocation: false }"/>
            </div>
        </div>
    </div>
</template>

<script>
import orderTypeEnum from "../../../../enums/modules/orderTypeEnum";
import {useModal} from "../../../../composables/modal";
import MapComponent from "../../../common/MapComponent.vue";

export default {
    name: "OrderDetailsMapComponent",
    props: {
        order: Object
    },
    setup() {
        const {openModal, closeModal} = useModal();
        return {
            openModal,
            closeModal
        }
    },
    components: {
        MapComponent
    },
    data() {
        return {
            mapShow: false,
            mapKey: "restaurant",
            enums: {
                orderTypeEnum: orderTypeEnum
            }
        }
    },
    methods: {
        showModal: function () {
            this.mapShow = true;
            this.openModal('mapModal');
        },
        hideModal: function () {
            this.mapShow = false;
            this.closeModal('mapModal');
        },
        mapPosition: function (e) {
        }
    }
}
</script>
