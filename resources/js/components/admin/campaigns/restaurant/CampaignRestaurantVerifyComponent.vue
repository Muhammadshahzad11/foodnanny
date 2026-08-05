<template>
    <LoadingComponent :props="loading" />

    <div id="restaurantVerifyModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.verify") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500"
                    @click.prevent="resetModal"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="verify">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label for="approved" class="db-field-title required">{{ $t("label.verify") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input type="radio" v-model="props.form.status" id="approved"
                                            :value="enums.campaignStatusEnum.APPROVE" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="approved" class="db-field-label">{{ $t('label.approve') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input type="radio" class="custom-radio-field" v-model="props.form.status"
                                            id="reject" :value="enums.campaignStatusEnum.REJECT">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="reject" class="db-field-label">{{ $t('label.reject') }}</label>
                                </div>
                            </div>
                            <small class="db-field-alert mt-2 block" v-if="errors.status">{{ errors.status[0] }}</small>
                        </div>
                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click.prevent="resetModal">
                                    <i class="lab lab-fill-close-circle"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>

                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save"></i>
                                    <span>{{ $t("button.save") }}</span>
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
import alertService from "../../../../services/alertService.js";
import campaignStatusEnum from "../../../../enums/modules/campaignStatusEnum.js";
import LoadingComponent from "../../../common/LoadingComponent.vue";
import { useModal } from "../../../../composables/modal.js";
import { quillEditor } from 'vue3-quill';
import { useCampaignRestaurantStore } from "../../../../stores/campaignRestaurant.js";

export default {
    name: "CampaignRestaurantVerifyComponent",
    components: { LoadingComponent, quillEditor },
    props: ["props"],
    setup() {
        const campaignRestaurantStore = useCampaignRestaurantStore();
        return {
            campaignRestaurantStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                campaignStatusEnum: campaignStatusEnum
            },
            errors: {}
        }
    },
    methods: {
        resetModal: function () {
            useModal().closeModal('restaurantVerifyModal');
            this.campaignRestaurantStore.reset();
            this.$props.props.form.status = null;
            this.errors = {};
        },
        verify: function () {
            try {
                this.loading.isActive = true;
                const tempId = this.campaignRestaurantStore.temp.temp_id;
                this.campaignRestaurantStore.verifyRestaurant({
                    campaignId: this.$props.props.id,
                    id: tempId,
                    status: this.$props.props.form.status,
                    search: this.$props.props.search,
                }).then((res) => {
                    useModal().closeModal('restaurantVerifyModal');
                    this.loading.isActive = false;
                    this.$props.props.form = {
                        status: null,
                        restaurant_id: null,
                    };
                    this.errors = {};
                    alertService.successFlip(1, this.$t("menu.campaigns"));
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        }
    }
}
</script>
