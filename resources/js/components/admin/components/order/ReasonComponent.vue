<template>
    <LoadingComponent :props="loading" />
    <button type="button" @click="reasonModal" data-modal="#reasonModal" class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#FB4E4E]">
        <i class="lab lab-fill-close-circle"></i>
        <span class="text-sm capitalize text-white">{{ $t('button.reject') }}</span>
    </button>

    <div id="reasonModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.reason") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500" @click.prevent="resetModal"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="rejectOrder">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label for="reason" class="db-field-title required">
                                {{ $t("label.reason") }}
                            </label>
                            <textarea v-model="form.reason" v-bind:class="error ? 'invalid' : ''" id="reason"
                                class="db-field-control"></textarea>
                            <small class="db-field-alert" v-if="error">
                                {{ error }}
                            </small>
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
import LoadingComponent from "../../../common/LoadingComponent.vue";
import {useOnlineOrderStore} from "../../../../stores/onlineOrder.js";
import {useModal} from "../../../../composables/modal.js";
import orderStatusEnum from "../../../../enums/modules/orderStatusEnum.js";
import alertService from "../../../../services/alertService.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "ReasonComponent",
    components: {
        LoadingComponent
    },
    setup() {
        const onlineOrderStore = useOnlineOrderStore();

        return {
            onlineOrderStore,
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                reason: ""
            },
            error: ""
        }
    },
    methods: {
        reasonModal: function () {
            return new VueSimpleAlert.confirm(
                this.$t('message.cancel_order_confirm_detail'),
                this.$t('message.are_you_sure'),
                "warning",
                {
                    confirmButtonText: this.$t('button.yes_do') || 'Yes',
                    cancelButtonText: this.$t('button.no_cancel'),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(() => {
                useModal().openModal('reasonModal');
            }).catch(() => {});
        },
        resetModal: function () {
            useModal().closeModal('reasonModal');
            this.form.reason = '';
            this.error = "";
        },
        rejectOrder: function () {
            try {
                this.loading.isActive = true;
                this.onlineOrderStore.changeStatus({
                    id: this.$route.params.id,
                    status: orderStatusEnum.REJECTED,
                    reason: this.form.reason,
                }).then((res) => {
                    useModal().closeModal('reasonModal');
                    this.loading.isActive = false;
                    this.form = {
                        reason: "",
                    };
                    this.error = "";
                    alertService.successFlip(
                        1,
                        this.$t("label.status")
                    );
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.error = err.response.data.message;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        }
    }
}
</script>
