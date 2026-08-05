<template>
    <LoadingComponent :props="loading" />
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 sm:col-6 xl:col-3">
                    <button type="button" @click="handleTab($event, 'information')"
                        class="tab-active tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10">
                        <i class="lab lab-line-info-circle lab-font-size-16"></i>
                        <span
                            class="flex-auto ltr:text-left rtl:text-right text-sm capitalize whitespace-nowrap text-ellipsis overflow-hidden">
                            {{ $t('label.information') }}
                        </span>
                    </button>
                </div>
                <div class="col-12 sm:col-6 xl:col-3">
                    <button type="button" @click="handleTab($event, 'image')"
                        class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10">
                        <i class="lab lab-line-upload-image lab-font-size-16"></i>
                        <span class="flex-auto ltr:text-left rtl:text-right text-sm capitalize whitespace-nowrap text-ellipsis overflow-hidden">
                            {{ $t("label.image") }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="tab-content db-card tab-active" id="information">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t("label.basic_info") }}</h3>
                    </div>
                    <div class="db-card-body">
                        <form @submit.prevent="save">
                            <div class="form-row">
                                <div class="form-col-12 sm:form-col-6">
                                    <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                                    <input v-model="form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                                        id="name" class="db-field-control" />
                                    <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label class="db-field-title" for="latitude">{{ $t("label.latitude") }}/{{
                                        $t("label.longitude") }}</label>
                                    <div class="db-multiple-field">
                                        <input v-model="form.latitude" v-bind:class="errors.latitude ? 'invalid' : ''"
                                            type="text" id="latitude" />
                                        <input v-model="form.longitude" v-bind:class="errors.longitude ? 'invalid' : ''"
                                            type="text" id="longitude" />
                                        <button @click="add" v-on:click="isMap = true" type="button"
                                            class="lab-fill-map-locate !text-xl" data-modal="#myRestaurantMap"></button>
                                    </div>
                                    <small class="db-field-alert" v-if="errors.latitude">{{ errors.latitude[0]
                                        }}</small>
                                    <small class="db-field-alert" v-if="errors.longitude">{{ errors.longitude[0]
                                        }}</small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label for="email" class="db-field-title">{{ $t("label.email") }}</label>
                                    <input v-model="form.email" v-bind:class="errors.email ? 'invalid' : ''"
                                        type="email" id="email" class="db-field-control" />
                                    <small class="db-field-alert" v-if="errors.email">{{ errors.email[0] }}</small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label for="phone" class="db-field-title">{{ $t("label.phone") }}</label>
                                    <input v-model="form.phone" v-bind:class="errors.phone ? 'invalid' : ''" type="text"
                                        id="phone" class="db-field-control" />
                                    <small class="db-field-alert" v-if="errors.phone">{{ errors.phone[0] }}</small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label for="city" class="db-field-title required">{{ $t("label.city") }}</label>
                                    <input v-model="form.city" v-bind:class="errors.city ? 'invalid' : ''" type="text"
                                        id="city" class="db-field-control" />
                                    <small class="db-field-alert" v-if="errors.city">{{ errors.city[0] }}</small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label for="state" class="db-field-title required">{{ $t("label.state") }}</label>
                                    <input v-model="form.state" v-bind:class="errors.state ? 'invalid' : ''" type="text"
                                        id="state" class="db-field-control" />
                                    <small class="db-field-alert" v-if="errors.state">{{ errors.state[0] }}</small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label for="zip_code" class="db-field-title required">{{ $t("label.zip_code")
                                        }}</label>
                                    <input v-model="form.zip_code" v-bind:class="errors.zip_code ? 'invalid' : ''"
                                        type="text" id="zip_code" class="db-field-control" />
                                    <small class="db-field-alert" v-if="errors.zip_code">{{ errors.zip_code[0]
                                        }}</small>
                                </div>

                                <div class="form-col-12 sm:form-col-6">
                                    <label for="cuisine_id" class="db-field-title">{{ $t("label.cuisine") }}</label>
                                    <vue-select class="db-field-control f-b-custom-select" id="cuisine_id"
                                        v-bind:class="errors.cuisine_id ? 'invalid' : ''" v-model="form.cuisine_id"
                                        :options="cuisines" label-by="name" value-by="id" :closeOnSelect="true"
                                        :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--"
                                        :multiple="true" />
                                    <small class="db-field-alert" v-if="errors.cuisine_id">{{ errors.cuisine_id[0]
                                        }}</small>
                                </div>

                                <div class="form-col-12">
                                    <label for="address" class="db-field-title required">{{ $t("label.address")
                                        }}</label>
                                    <textarea v-model="form.address" v-bind:class="errors.address ? 'invalid' : ''"
                                        id="address" class="db-field-control"></textarea>
                                    <small class="db-field-alert" v-if="errors.address">{{ errors.address[0] }}</small>
                                </div>

                                <div class="form-col-12">
                                    <button type="submit" class="db-btn text-white bg-primary">
                                        <i class="lab lab-fill-save text-base"></i>
                                        <span>{{ $t("button.save") }}</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-content db-card" id="image">
                <div class="db-card">
                    <div class="db-card-body">
                        <div class="row">
                            <div class="form-col-12 2xl:form-col-6">
                                <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                                    <legend
                                        class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                                        {{ $t('label.logo') }}
                                    </legend>
                                    <div class="row py-2">
                                        <form @submit.prevent="saveLogo" class="w-auto">
                                            <p class="mt-2 px-3">{{ $t('label.size') }}: (1:1)</p>
                                            <div class="flex gap-3 md:gap-4 p-3">
                                                <label for="logo"
                                                    class="db-btn relative cursor-pointer h-[38px] shadow-[0px_6px_10px_rgb(var(--primary)/0.24)] bg-primary text-white">
                                                    <i class="lab lab-line-upload-image"></i>
                                                    <span class="hidden sm:inline-block">{{
                                                        $t("button.upload_new_logo")
                                                        }}</span>
                                                    <input v-if="uploadLogoButton" @change="changePreviewLogo"
                                                        accept="image/png, image/jpeg, image/jpg" ref="logoProperty"
                                                        type="file" id="logo"
                                                        class="absolute top-0 left-0 w-full h-full -z-10 opacity-0" />
                                                </label>
                                                <button v-if="saveLogoButton" type="submit"
                                                    class="db-btn h-[38px] shadow-[0px_6px_10px_rgba(26,_183,_89,_0.24)] text-white bg-[#1AB759]">
                                                    <i class="lab lab-line-circle-check"></i>
                                                    <span class="hidden sm:inline-block">{{ $t("button.save") }}</span>
                                                </button>
                                                <button v-if="resetLogoButton" @click="resetPreviewLogo" type="button"
                                                    class="db-btn-outline h-[38px] shadow-[0px_6px_10px_rgba(251,_78,_78,_0.24)] !text-[#FB4E4E] !bg-white !border-[#FB4E4E]">
                                                    <i class="lab lab-line-reset"></i>
                                                    <span class="hidden sm:inline-block">{{ $t("button.reset") }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 sm:col-5">
                                            <img class="w-[120px] h-[120px] object-fill db-image" alt="restaurant-logo"
                                                :src="previewLogo" />
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="form-col-12 2xl:form-col-6">
                                <fieldset class="p-4 mb-6 border border-[#DBDEE0]">
                                    <legend
                                        class="py-1.5 px-4 text-base font-semibold capitalize border border-[#DBDEE0] text-primary">
                                        {{ $t('label.cover') }}
                                    </legend>
                                    <div class="row py-2">
                                        <form @submit.prevent="saveCover" class="w-auto">
                                            <p class="mt-2 px-3">{{ $t('label.size') }}: (1126px,250px)</p>
                                            <div class="flex gap-3 md:gap-4 p-3">
                                                <label for="photo"
                                                    class="db-btn relative cursor-pointer h-[38px] shadow-[0px_6px_10px_rgb(var(--primary)/0.24)] bg-primary text-white">
                                                    <i class="lab lab-line-upload-image"></i>
                                                    <span class="hidden sm:inline-block">{{
                                                        $t("button.upload_new_cover")
                                                        }}</span>
                                                    <input v-if="uploadCoverButton" @change="changePreviewCover"
                                                        accept="image/png, image/jpeg, image/jpg" ref="imageProperty"
                                                        type="file" id="photo"
                                                        class="absolute top-0 left-0 w-full h-full -z-10 opacity-0" />
                                                </label>
                                                <button v-if="saveCoverButton" type="submit"
                                                    class="db-btn h-[38px] shadow-[0px_6px_10px_rgba(26,_183,_89,_0.24)] text-white bg-[#1AB759]">
                                                    <i class="lab lab-line-circle-check"></i>
                                                    <span class="hidden sm:inline-block">{{ $t("button.save") }}</span>
                                                </button>
                                                <button v-if="resetCoverButton" @click="resetPreviewCover" type="button"
                                                    class="db-btn-outline h-[38px] shadow-[0px_6px_10px_rgba(251,_78,_78,_0.24)] !text-[#FB4E4E] !bg-white !border-[#FB4E4E]">
                                                    <i class="lab lab-line-reset"></i>
                                                    <span class="hidden sm:inline-block">{{ $t("button.reset") }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 sm:col-10">
                                            <img class="db-image" alt="restaurant" :src="previewCover" />
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="myRestaurantMap" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.address") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500"
                    @click="mapReset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 map-height">
                            <MapComponent v-if="isMap" :location="{ lat: form.latitude, lng: form.longitude }"
                                :position="location" />
                        </div>
                        <div class="form-col-12">
                            <label for="apartment" class="db-field-title font-medium text-sm my-0">{{ address }}</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import alertService from "../../../../services/alertService.js";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import MapComponent from "../../../common/MapComponent.vue";
import { useModal } from "../../../../composables/modal.js";
import { useTab } from "../../../../composables/tab.js";
import _ from "lodash";
import { useCuisineStore } from "../../../../stores/cuisine.js";
import { useMyRestaurantStore } from "../../../../stores/myRestaurant.js";

export default {
    name: "MyRestaurantComponent",
    components: { LoadingComponent, MapComponent },
    setup() {
        const cuisineStore = useCuisineStore();
        const myRestaurantStore = useMyRestaurantStore();
        return { cuisineStore, myRestaurantStore }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                name: "",
                email: "",
                phone: "",
                cuisine_id: [],
                latitude: "",
                longitude: "",
                city: "",
                state: "",
                zip_code: "",
                address: ""
            },
            isMap: false,
            address: "",
            errors: {},

            defaultLogo: null,
            previewLogo: null,
            uploadLogoButton: true,
            resetLogoButton: false,
            saveLogoButton: false,

            defaultCover: null,
            previewCover: null,
            uploadCoverButton: true,
            resetCoverButton: false,
            saveCoverButton: false,

            handleTab: useTab().handleTab
        };
    },
    computed: {
        cuisines: function () {
            return this.cuisineStore.allCuisineLists;
        }
    },
    async mounted() {
        try {
            this.loading.isActive = true;
            await this.cuisineStore.fetchAllCuisine({ order_column: 'id', order_type: 'asc', status: statusEnum.ACTIVE }).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });

            await this.myRestaurantStore.fetch().then((res) => {
                this.defaultLogo = res.data.data.logo;
                this.previewLogo = res.data.data.logo;

                this.defaultCover = res.data.data.cover;
                this.previewCover = res.data.data.cover;

                this.form = {
                    name: res.data.data.name,
                    email: res.data.data.email,
                    phone: res.data.data.phone,
                    latitude: res.data.data.latitude,
                    longitude: res.data.data.longitude,
                    city: res.data.data.city,
                    state: res.data.data.state,
                    zip_code: res.data.data.zip_code,
                    address: res.data.data.address,
                    cuisine_id: this.cuisineUpdate(res.data.data.cuisine_id)
                };
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });

        } catch (err) {
            this.loading.isActive = false;
            alertService.error(err);
        }
    },
    methods: {
        add: function () {
            useModal().openModal('myRestaurantMap');
        },
        cuisineUpdate: function (objects) {
            let cuisines = [];
            _.forEach(objects, (object, key) => {
                cuisines.push(object.cuisine_id);
            });
            return cuisines;
        },
        location: function (e) {
            this.address = e.address;
            this.form.latitude = e.location.lat;
            this.form.longitude = e.location.lng;
            this.form.city = e.other.city;
            this.form.state = e.other.state;
            this.form.zip_code = e.other.zipCode;
            this.form.address = e.address;
        },
        mapReset: function () {
            useModal().closeModal('myRestaurantMap');
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.myRestaurantStore.save({ form: this.form }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(true, this.$t("menu.my_restaurant"));
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        changePreviewLogo: function (e) {
            if (e.target.files[0]) {
                this.previewLogo = URL.createObjectURL(e.target.files[0]);
                this.saveLogoButton = true;
                this.resetLogoButton = true;
            }
        },
        resetPreviewLogo: function () {
            this.$refs.logoProperty.value = null;
            this.previewLogo = this.defaultLogo;
            this.saveLogoButton = false;
            this.resetLogoButton = false;
        },
        saveLogo: function () {
            if (this.$refs.logoProperty.files[0]) {
                try {
                    this.loading.isActive = true;
                    const formData = new FormData();
                    formData.append("image", this.$refs.logoProperty.files[0]);
                    this.myRestaurantStore.changeLogo({ id: this.$route.params.id, form: formData }).then((res) => {
                        alertService.success(this.$t("message.logo_update"));
                        this.defaultLogo = res.data.data.logo;
                        this.previewLogo = res.data.data.logo;
                        this.$refs.logoProperty.value = null;
                        this.saveLogoButton = false;
                        this.resetLogoButton = false;
                        this.loading.isActive = false;
                    }).catch((err) => {
                        this.loading.isActive = false;
                        if (typeof err.response.data.status !== "undefined" && err.response.data.status === false) {
                            alertService.error(err.response.data.message);
                        } else if (err.response.data.errors && err.response.data.errors.image) {
                            err.response.data.errors.image.forEach((error) => {
                                alertService.error(error);
                            });
                        }
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }
        },
        changePreviewCover: function (e) {
            if (e.target.files[0]) {
                this.previewCover = URL.createObjectURL(e.target.files[0]);
                this.saveCoverButton = true;
                this.resetCoverButton = true;
            }
        },
        resetPreviewCover: function () {
            this.$refs.imageProperty.value = null;
            this.previewCover = this.defaultCover;
            this.saveCoverButton = false;
            this.resetCoverButton = false;
        },
        saveCover: function () {
            if (this.$refs.imageProperty.files[0]) {
                try {
                    this.loading.isActive = true;
                    const formData = new FormData();
                    formData.append("image", this.$refs.imageProperty.files[0]);
                    this.myRestaurantStore.changeImage({ id: this.$route.params.id, form: formData }).then((res) => {
                        alertService.success(this.$t("message.image_update"));
                        this.defaultCover = res.data.data.cover;
                        this.previewCover = res.data.data.cover;
                        this.$refs.imageProperty.value = null;
                        this.saveCoverButton = false;
                        this.resetCoverButton = false;
                        this.loading.isActive = false;
                    }).catch((err) => {
                        this.loading.isActive = false;
                        if (typeof err.response.data.status !== "undefined" && err.response.data.status === false) {
                            alertService.error(err.response.data.message);
                        } else if (err.response.data.errors && err.response.data.errors.image) {
                            err.response.data.errors.image.forEach((error) => {
                                alertService.error(error);
                            });
                        }
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }
        }
    }
};
</script>
