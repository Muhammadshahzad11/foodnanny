<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset" />

    <div id="sidebar" @click="closeBackdrop"
        class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-xl h-dvh overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="drawer-title">{{ $t("menu.reviews") }}</h3>
                <button class="lab-line-close font-bold text-base" @click="reset"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div v-if="!editing" class="form-col-12 sm:form-col-6" >
                            <label for="order_id" class="db-field-title required">{{ $t("label.order_id") }}</label>
                            <input v-model="props.form.order_id" v-bind:class="errors.order_id ? 'invalid' : ''" type="text" id="order_id" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.order_id">{{ errors.order_id[0] }}</small>
                        </div>
                        <div v-if="!editing" class="form-col-12 sm:form-col-6">
                            <label for="type" class="db-field-title required">{{$t("label.type")}}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="type"
                                v-bind:class="errors.type ? 'invalid' : ''"
                                v-model="props.form.type" :options="[
                                { id: enums.modelTypeEnum.RESTAURANT, name: $t('label.restaurant') },
                                { id: enums.modelTypeEnum.DELIVERY_BOY, name: $t('label.delivery_boy') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.type">{{ errors.type[0] }}</small>
                        </div>
                        <div v-bind:class="editing ? 'form-col-12' : 'form-col-12 sm:form-col-6'">
                            <label for="star" class="db-field-title required">{{ $t("label.star") }}</label>
                            <input v-model="props.form.star" v-bind:class="errors.star ? 'invalid' : ''" type="text" id="star" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.star">{{ errors.star[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-12">
                            <label for="review" class="db-field-title required">{{ $t("label.review") }}</label>
                            <textarea id="review" v-model="props.form.review" v-bind:class="errors.review ? 'invalid' : ''" class="db-field-control block"></textarea>
                            <small class="db-field-alert" v-if="errors.review">{{ errors.review[0] }}</small>
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
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import { useCanvas } from "../../../composables/canvas.js";
import { useReviewStore } from "../../../stores/review.js";
import modelTypeEnum from "../../../enums/modules/modelTypeEnum.js";
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent.vue";
import alertService from "../../../services/alertService.js";

export default {
    name: "ReviewCreateComponent",
    components: {
        LoadingComponent,
        SmSidebarModalCreateComponent
    },
    props: ["props"],
    setup() {
        const reviewStore = useReviewStore();
        return {
            reviewStore
        }
    },
    data() {
        return {
            loading      : { isActive: false },
            addButton    : { title: this.$t("button.add_review") },
            enums        : { modelTypeEnum },
            errors       : {},
            closeBackdrop: useCanvas().closeBackdrop
        };
    },
    computed: {
        editing: function () {
            return this.reviewStore.temp.isEditing;
        }
    },
    methods: {
        addReset: function () {
            this.reviewStore.reset();
            this.errors = {};
            this.$props.props.form = {
                order_id: "",
                type    : null,
                star    : "",
                review  : ""
            };
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.reviewStore.reset();
            this.errors = {};
            this.$props.props.form = {
                order_id: "",
                type    : null,
                star    : "",
                review  : ""
            };
        },
        save: function () {
            try {
                const tempId = this.reviewStore.temp.temp_id;
                this.loading.isActive = true;
                this.reviewStore.save(this.props).then((res) => {
                    useCanvas().closeCanvas('sidebar');
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("menu.reviews"));
                    this.props.form = {
                        order_id: "",
                        type    : null,
                        star    : "",
                        review  : ""
                    };
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
