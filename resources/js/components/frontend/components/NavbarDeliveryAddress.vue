<template>
    <div id="delivery-address"
         class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-lg w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="flex items-center justify-between gap-4 py-4 px-6">
                <h3 class="text-lg font-semibold capitalize">{{ $t('label.delivery_address') }}</h3>
                <button @click.prevent="closeModal('delivery-address')"
                        class="lab-line-circle-cross text-lg text-danger"></button>
            </div>

            <div class="p-6">
                <div class="flex gap-2 mb-5">
                    <form @submit="searchLocation" class="w-full flex gap-2 mb-5">
                        <LoadingContentComponent :props="contentLoading"/>
                        <div class="flex-auto flex items-center gap-3 h-12 rounded-lg bg-gray-100 relative">
                            <button type="button"
                                    class="lab-line-search text-xl ltr:ml-3 rtl:mr-3 absolute ltr:left-3 rtl:right-3 z-[1]"></button>
                            <input type="text" v-model="modelLocation" id="nav-map-autocomplete-input"
                                   ref="locationName" :placeholder="$t('label.enter_your_location')"
                                   class="w-full h-full text-ellipsis ltr:pl-12 ltr:pr-12 ltr:sm:pr-12 rtl:pl-12 rtl:sm:pl-12 rtl:pr-12">
                            <button type="button" id="nav-map-current-location"
                                    class="lab-line-gps text-xl text-primary absolute ltr:right-3 rtl:left-3"></button>
                        </div>
                        <button type="submit"
                                :class="modelLocation === '' || modelLocation === null ? 'bg-primary/50' : ''"
                                :disabled="modelLocation === '' || modelLocation === null"
                                class="w-12 h-12 flex-shrink-0 flex items-center justify-center rounded-lg bg-primary">
                            <i :class="displayModeNumber  === enums.displayModeEnum.LTR ? 'lab-line-long-arrow-right' : 'lab-line-long-arrow-left'"
                               class="text-2xl text-white"></i>
                        </button>
                    </form>
                </div>
                <NavbarDeliverySaveAddress v-if="logged"/>
            </div>
        </div>
    </div>
</template>

<script>
import {useModal} from "../../../composables/modal.js";
import LoadingContentComponent from "../../common/LoadingContentComponent.vue";
import displayModeEnum from "../../../enums/modules/displayModeEnum.js";
import {useCommonStore} from "../../../stores/common.js";
import {useAuthStore} from "../../../stores/auth.js";
import NavbarDeliverySaveAddress from "./NavbarDeliverySaveAddress.vue";
import _ from "lodash";
import ENV from "../../../config/env.js";

export default {
    name: "NavbarDeliveryAddress",
    components: {NavbarDeliverySaveAddress, LoadingContentComponent},
    setup() {
        const {closeModal} = useModal();
        const authStore    = useAuthStore();
        const commonStore  = useCommonStore();

        return {
            closeModal,
            authStore,
            commonStore
        }
    },
    data() {
        return {
            contentLoading: {
                isActive: false,
            },
            enums: {
                displayModeEnum: displayModeEnum
            },
            position: {
                name: null,
                address: null,
                other: {},
                location: {
                    lat: null,
                    lng: null
                }
            },
            modelLocation: null,
            currentLocation: {},
        }
    },
    computed: {
        logged: function () {
            return this.authStore.status;
        },
        displayModeNumber: function () {
            return this.commonStore.display_mode;
        },
        location: function () {
            return this.commonStore.location;
        }
    },
    async mounted() {
        this.modelLocation = this.commonStore.location;
        await this.commonStore.update({edit_address_id: null});
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.currentLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };
                    this.mainMap();
                }, () => {
                    alert('The Geolocation service failed.');
                }
            );
        } else {
            alert("Your browser doesn't support geolocation.");
        }
    },
    methods: {
        mainMap: async function () {
            if (ENV.GOOGLE_MAP_KEY) {
                const Places       = await google.maps.importLibrary("places")
                let input          = document.getElementById('nav-map-autocomplete-input');
                const autocomplete = new Places.Autocomplete(input);
                autocomplete.addListener('place_changed', () => {
                    const place          = autocomplete.getPlace();
                    this.modelLocation   = this.$refs.locationName.value;
                    this.currentLocation = {
                        lat: place.geometry.location.lat(),
                        lng: place.geometry.location.lng()
                    };
                    this.setPosition();
                });

                let currentLocationButton = document.getElementById('nav-map-current-location');
                currentLocationButton.addEventListener("click", () => {
                    this.contentLoading.isActive = true;
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.currentLocation = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude,
                                };

                                const latLngLiteral = new google.maps.LatLng(this.currentLocation.lat, this.currentLocation.lng);
                                const geocoder      = new google.maps.Geocoder();
                                geocoder.geocode({latLng: latLngLiteral}).then(res => {
                                    if (res.results.length > 0) {
                                        this.contentLoading.isActive = false;
                                        this.modelLocation           = res.results[0].formatted_address;
                                        this.setPosition();
                                    }
                                });
                            }, () => {
                                alert('The Geolocation service failed.');
                            }
                        );
                    } else {
                        alert("Your browser doesn't support geolocation.");
                    }
                });
            }
        },
        setPosition: function () {
            let other             = {
                "roadNo": null,
                "block": null,
                "area": null,
                "city": null,
                "zipCode": null,
                "state": null,
                "country": null,
            };
            let formatted_address = "";
            const latLngLiteral   = new google.maps.LatLng(this.currentLocation.lat, this.currentLocation.lng);
            const geocoder        = new google.maps.Geocoder();
            geocoder.geocode({latLng: latLngLiteral}).then(res => {
                for (let i = 0; i < res.results.length; i++) {
                    for (let j = 0; j < res.results[i].address_components.length; j++) {
                        if (res.results[i].address_components[j].types[0] === "route" && other.roadNo === null) {
                            other.roadNo = res.results[i].address_components[j].long_name;
                        }

                        if (res.results[i].address_components[j].types[0] === "neighborhood" && res.results[i].address_components[j].types[1] === "political" && other.block === null) {
                            other.block = res.results[i].address_components[j].long_name;
                        }

                        if (res.results[i].address_components[j].types[0] === "political" && res.results[i].address_components[j].types[1] === "sublocality" && res.results[i].address_components[j].types[2] === "sublocality_level_1" && other.area === null) {
                            other.area = res.results[i].address_components[j].long_name;
                        }

                        if (res.results[i].address_components[j].types[0] === "locality" && res.results[i].address_components[j].types[1] === "political" && other.city === null) {
                            other.city = res.results[i].address_components[j].long_name;
                        }

                        for (let k = 0; k < res.results[i].address_components[j].types.length; k++) {
                            if (res.results[i].address_components[j].types[k] === "postal_code" && other.zipCode === null) {
                                other.zipCode = res.results[i].address_components[j].long_name;
                            }
                        }

                        if (res.results[i].address_components[j].types[0] === "administrative_area_level_1" && res.results[i].address_components[j].types[1] === "political" && other.state === null) {
                            other.state = res.results[i].address_components[j].long_name;
                        }

                        if (res.results[i].address_components[j].types[0] === "country" && other.country === null) {
                            other.country = res.results[i].address_components[j].long_name;
                        }
                    }
                }

                _.forEach(other, (value, index) => {
                    if (value !== null && value !== "") {
                        formatted_address += value;
                        if (index !== "country") {
                            formatted_address += ", ";
                        }
                        formatted_address += "";
                    }
                });
                this.position = {
                    name: this.$refs.locationName.value,
                    address: formatted_address,
                    other: other,
                    location: this.currentLocation
                };
            }).catch((error) => {
                this.position = {
                    name: this.$refs.locationName.value,
                    address: formatted_address,
                    other: other,
                    location: this.currentLocation
                };
            });
        },
        searchLocation: async function () {
            if (this.position.location.lat !== null && this.position.location.lng !== null) {
                await this.commonStore.update({
                    location: this.position.name,
                    latitude: this.position.location.lat,
                    longitude: this.position.location.lng
                });
                this.closeModal('delivery-address');
            }
        },
    },
    watch: {
        location: function () {
            this.modelLocation = this.commonStore.location;
        }
    }
}
</script>
