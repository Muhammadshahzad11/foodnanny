<template>
    <button @click.prevent="mapModal" type="button" class="w-[30px] h-[30px] flex items-center justify-center ml-auto flex-shrink-0 rounded bg-primary">
        <i class="lab-fill-map text-2xl text-white"></i>
    </button>

    <div id="mapModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.address") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500" @click="reset"></button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-col-12">
                        <MapComponent :key="mapKey" v-if="mapShow" :location="{ lat: orderAddress.latitude, lng: orderAddress.longitude }" :position="mapPosition" :setting="{ autocomplete: false, mouseEvent: false, currentLocation: false }" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {useModal} from "../../../../composables/modal.js";
import MapComponent from "../../../common/MapComponent.vue";

export default {
    name: "OrderMapComponent",
    props: {
        orderAddress: Object,
    },
    components: {
        MapComponent
    },
    data() {
        return {
            mapShow: false,
            mapKey: "branch",
        }
    },
    methods: {
        mapModal: function () {
            this.mapShow = true;
            useModal().openModal('mapModal');
        },
        reset: function () {
            useModal().closeModal('mapModal');
            this.mapShow = false;
        },
        mapPosition: function (e) {
        }
    }
}
</script>
