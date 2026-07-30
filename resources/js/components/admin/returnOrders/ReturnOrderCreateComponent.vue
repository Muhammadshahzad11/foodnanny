<template>
    <LoadingComponent :props="loading"/>
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset"/>
    <div id="sidebar" @click="closeBackdrop"
         class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-xl h-dvh overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="drawer-title">{{ $t("menu.return_order") }}</h3>
                <button class="lab-line-close font-bold text-base" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-12">
                            <label for="order_serial_no"
                                   class="db-field-title required">{{ $t("label.order_id") }}</label>
                            <input v-model="props.form.order_serial_no" v-on:keypress="phoneNumber($event)"
                                   v-bind:class="errors.order_serial_no ? 'invalid' : ''" type="text"
                                   id="order_serial_no" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.order_serial_no">{{
                                    errors.order_serial_no[0]
                                }}</small>
                        </div>
                        <div class="form-col-12">
                            <label for="reason" class="db-field-title required">{{ $t("label.reason") }}</label>
                            <textarea v-model="props.form.reason" v-bind:class="errors.reason ? 'invalid' : ''"
                                      id="reason" class="db-field-control"></textarea>
                            <small class="db-field-alert" v-if="errors.reason">{{ errors.reason[0] }}</small>
                        </div>
                        <div class="form-col-12">
                            <label for="images" class="db-field-title">{{ $t("label.images") }}</label>
                            <input @change="changeImages" :class="errors.images ? 'invalid' : ''" id="images"
                                   type="file" accept="image/png, image/jpeg, image/jpg" class="db-field-control"
                                   ref="imagesProperty" multiple/>
                            <small class="db-field-alert" v-for="(error, key) in imageErrors" :key="key">
                                {{ error }}<br>
                            </small>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <div v-for="(img, index) in imagePreviews" :key="index"
                                     class="relative w-20 h-20 border">
                                    <img alt="return-image" :src="img" class="object-cover w-full h-full rounded"/>
                                    <button @click.prevent="removeImage(index)"
                                            class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        &times;
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save text-base"></i>
                                    <span>{{ $t("label.save") }}</span>
                                </button>
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-fill-close-circle text-base"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import alertService from "../../../services/alertService.js";
import appService from "../../../services/appService.js";
import {useCanvas} from "../../../composables/canvas.js";
import {useReturnOrderStore} from "../../../stores/returnOrder.js";
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent.vue";

export default {
    name: "ReturnOrderCreateComponent",
    components: {SmSidebarModalCreateComponent, LoadingComponent},
    props: ["props"],
    setup() {
        const {closeCanvas, closeBackdrop} = useCanvas();
        const returnOrderStore             = useReturnOrderStore();
        return {
            closeCanvas,
            closeBackdrop,
            returnOrderStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            addButton: {
                title: this.$t("button.add_return_order")
            },
            errors: {},
            images: [],
            imagePreviews: []
        };
    },
    computed: {
        imageErrors() {
            const result = {}
            for (const key in this.errors) {
                if (key.startsWith('images.')) {
                    const index = key.split('.')[1]
                    result[key] = this.errors[key][0].replace(`images.${index}`, this.$t('label.image') + ' ' + (parseInt(index) + 1))
                }
            }
            return result
        }
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        addReset: function () {
            this.errors                     = {};
            this.$props.props.form          = {
                order_serial_no: "",
                reason: ""
            };
            this.images                     = [];
            this.imagePreviews              = [];
            this.$refs.imagesProperty.value = null;
        },
        reset: function () {
            this.closeCanvas('sidebar');
            this.errors                     = {};
            this.$props.props.form          = {
                order_serial_no: "",
                reason: ""
            };
            this.images                     = [];
            this.imagePreviews              = [];
            this.$refs.imagesProperty.value = null;
        },
        save: function () {
            try {
                const fd = new FormData();
                fd.append("order_serial_no", this.props.form.order_serial_no);
                fd.append("reason", this.props.form.reason);
                if (this.images.length > 0) {
                    this.images.forEach((file, index) => {
                        fd.append(`images[${index}]`, file);
                    });
                }

                this.loading.isActive = true;
                this.returnOrderStore.save({form: fd, search: this.props.search}).then((res) => {
                    this.closeCanvas('sidebar');
                    this.loading.isActive = false;
                    alertService.successFlip(0, this.$t("menu.return_orders"));
                    this.props.form = {
                        order_serial_no: "",
                        reason: ""
                    };
                    this.errors     = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors           = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        changeImages(e) {
            const selectedFiles = Array.from(e.target.files);
            if (this.images.length + selectedFiles.length > 4) {
                alertService.error(this.$t("message.max_images_exceeded"));
                return;
            }
            selectedFiles.forEach(file => {
                if (!file.type.startsWith("image/")) return;
                this.images.push(file);
                const reader  = new FileReader();
                reader.onload = (e) => {
                    this.imagePreviews.push(e.target.result);
                };
                reader.readAsDataURL(file);
            });
        },
        removeImage(index) {
            this.images.splice(index, 1);
            this.imagePreviews.splice(index, 1);
        }
    }
};
</script>
