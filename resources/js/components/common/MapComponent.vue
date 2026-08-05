<template>
    <div v-if="setting.autocomplete || setting.currentLocation"
         class="w-full h-12 mb-5 shadow-xs rounded-lg flex items-center gap-3 px-3 border border-white bg-white relative">
        <i class="lab-line-search flex-shrink-0 text-xl text-primary ltr:ml-3 rtl:mr-3 absolute ltr:left-3 rtl:right-3 z-[1]"></i>
        <input v-if="setting.autocomplete" id="map-autocomplete-input" type="text" placeholder="Enter a location"
               class="w-full text-ellipsis h-full ltr:pl-12 ltr:pr-12 ltr:sm:pr-12 rtl:pl-12 rtl:sm:pl-12 rtl:pr-12">
        <button v-if="setting.currentLocation" id="map-current-location" type="button"
                class="lab-line-gps flex-shrink-0 w-7 h-7 leading-7 rounded-md text-center bg-primary text-white  absolute ltr:right-3 rtl:left-3"></button>
    </div>

    <div class="mb-5">
        <LoadingContentComponent :props="loading"/>
        <div ref="theGoogleMap" id="the-google-map" class="w-full h-48 rounded-lg"></div>
    </div>
</template>

<script>
import LoadingContentComponent from "./LoadingContentComponent.vue";
import ENV from "../../config/env.js";
import _ from "lodash";


export default {
    name: "MapComponent",
    components: {LoadingContentComponent},
    props: {
        location: Object,
        position: {
            type: Function,
            required: false
        },
        setting: {
            type: Object,
            default: {
                autocomplete: {
                    type: Boolean,
                    default: true,
                    required: false
                },
                mouseEvent: {
                    type: Boolean,
                    default: true,
                    required: false
                },
                currentLocation: {
                    type: Boolean,
                    default: true,
                    required: false
                }
            }
        }
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            currentLocation: {
                lat: null,
                lng: null,
            },
            address: null,
        }
    },
    mounted: async function () {
        this.loading.isActive = true;
        if ((this.location.lat === null || this.location.lat === "") && (this.location.lng === null || this.location.lng === "")) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.currentLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        this.mainMap(this.currentLocation);
                    }, () => {
                        alert('The Geolocation service failed.');
                    }
                );
            } else {
                alert("Your browser doesn't support geolocation.");
            }
        } else {
            this.currentLocation.lat = parseFloat(this.location.lat);
            this.currentLocation.lng = parseFloat(this.location.lng);
            await this.mainMap(this.currentLocation);
        }
    },
    methods: {
        mainMap: async function (location) {
            if (ENV.GOOGLE_MAP_KEY) {
                const {AdvancedMarkerElement, PinElement} = await google.maps.importLibrary("marker");

                const map = new google.maps.Map(this.$refs.theGoogleMap, {
                    zoom: 15,
                    center: location,
                    mapId: ENV.GOOGLE_MAP_KEY
                });

                const pin = new PinElement({
                    scale: 1,
                });

                const marker = new AdvancedMarkerElement({
                    map,
                    position: location,
                    content: pin.element,
                });

                if (this.setting.currentLocation) {
                    let currentLocationButton = document.getElementById('map-current-location');
                    currentLocationButton.addEventListener("click", () => {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    this.currentLocation = {
                                        lat: position.coords.latitude,
                                        lng: position.coords.longitude,
                                    };
                                    marker.position      = this.currentLocation;
                                    map.setCenter(marker.position);
                                    this.setPosition();
                                }, () => {
                                    alert('The Geolocation service failed.');
                                }
                            );
                        } else {
                            alert("Your browser doesn't support geolocation.");
                        }
                    });
                }

                if (this.setting.mouseEvent) {
                    map.addListener("click", (mapsMouseEvent) => {
                        const latLng             = mapsMouseEvent.latLng.toJSON();
                        this.currentLocation.lat = latLng.lat;
                        this.currentLocation.lng = latLng.lng;
                        marker.position          = latLng;
                        this.setPosition();
                    });
                }

                if (this.setting.autocomplete) {
                    const Places       = await google.maps.importLibrary("places")
                    let input          = document.getElementById('map-autocomplete-input');
                    const autocomplete = new Places.Autocomplete(input);
                    autocomplete.addListener('place_changed', () => {
                        const place          = autocomplete.getPlace();
                        this.currentLocation = {
                            lat: place.geometry.location.lat(),
                            lng: place.geometry.location.lng()
                        };
                        marker.position      = this.currentLocation;
                        map.setCenter(marker.position);
                        this.setPosition();
                    });
                }

                this.setPosition();
                this.loading.isActive = false;
            }
        },
        setPosition: function () {
            let other           = {
                "roadNo": null,
                "block": null,
                "area": null,
                "city": null,
                "zipCode": null,
                "state": null,
                "country": null,
            };
            const latLngLiteral = new google.maps.LatLng(this.currentLocation.lat, this.currentLocation.lng);
            const geocoder      = new google.maps.Geocoder();
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

                let formatted_address = "";
                _.forEach(other, (value, index) => {
                    if (value !== null && value !== "") {
                        formatted_address += value;

                        if (index !== "country") {
                            formatted_address += ",";
                        }
                        formatted_address += " ";
                    }
                });

                this.address = formatted_address;
                if (this.$props.position !== undefined) {
                    this.position({address: this.address, other: other, location: this.currentLocation});
                }
            }).catch((error) => {
                if (this.$props.position !== undefined) {
                    this.position({address: this.address, other: other, location: this.currentLocation});
                }
            })
        }
    }
}
</script>
