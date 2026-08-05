<template>
    <LoadingComponent :props="loading" />

    <div id="delivery_location_setup" class="db-card db-tab-div active">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("menu.delivery_location_setup") }}</h3>
        </div>
        <div class="db-card-body">
            <div v-if="fetchDate" class="mb-2 text-red-600">
                {{ $t("label.warning") }}: {{ $t("message.location_warning") }}
            </div>

            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 map-height">
                        <MapComponent v-if="props.isMap" :location="{ lat: props.form.latitude, lng: props.form.longitude }" :position="location" />
                    </div>

                    <div class="form-col-12">
                        <label for="apartment" class="db-field-title font-medium text-sm my-0">
                            {{ props.form.address }}
                        </label>
                    </div>
                    <div class="form-col-12 mt-5" v-if="props.isMap">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-fill-save text-base"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import MapComponent from "../../../common/MapComponent.vue";
import {useDeliveryLocationSetupStore} from "../../../../stores/deliveryLocationSetup.js";


export default {
    name: "DeliveryLocationSetupComponent",
    components: { LoadingComponent, MapComponent },
    setup() {
        const deliveryLocationSetupStore = useDeliveryLocationSetupStore();
        return {
            deliveryLocationSetupStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            props: {
                form: {
                    address: "",
                    latitude: "",
                    longitude: ""
                },
                isMap: false
            },
            fetchDate : false,
            errors: {}
        };
    },
    mounted() {
        try {
            this.deliveryLocationSetupStore.fetch().then((res) => {
                this.props.isMap = true;
                if(typeof res.data === 'object') {
                    this.props.form  = {
                        address: res.data.data.address,
                        latitude: res.data.data.latitude,
                        longitude: res.data.data.longitude
                    }
                } else {
                    this.fetchDate = true;
                }
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        } catch(err) {
            this.loading.isActive = false;
            alertService.error(err);
        }
    },
    methods: {
        location: function (e) {
            this.props.form.address = e.address;
            this.props.form.latitude = e.location.lat;
            this.props.form.longitude = e.location.lng;
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.deliveryLocationSetupStore.save(this.props.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(!this.fetchDate ?? 0, this.$t("menu.delivery_location_setup"));
                    this.fetchDate = false;
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
};
</script>
