<template>
    <LoadingComponent :props="loading"/>

    <button @click="showModal" type="button"
            class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-primary">
        <i class="lab lab-line-add-circle"></i>
        <span class="text-sm capitalize text-white">{{ $t('button.add_token') }}</span>
    </button>

    <div id="online-token-modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t('label.token') }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500"
                        @click="resetModal"></button>
            </div>
            <div class="modal-body"> 
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-12">
                            <label for="token-no" class="db-field-title required"> {{ $t("label.token_no") }} </label>
                            <input v-model="form.token" type="text" id="token-no" :class="errors.token ? 'invalid' : ''"
                                   class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.token">{{ errors.token[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="resetModal">
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
import {useModal} from "../../../composables/modal.js";
import LoadingComponent from "../../common/LoadingComponent.vue";
import {useOnlineOrderStore} from "../../../stores/onlineOrder.js";
import alertService from "../../../services/alertService.js";

export default {
    name: "OnlineOrderTokenComponent",
    components: {
        LoadingComponent,
    },
    setup() {
        const {openModal, closeModal} = useModal();
        const onlineOrderStore   = useOnlineOrderStore();

        return {
            openModal,
            closeModal,
            onlineOrderStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                token: ""
            },
            errors: {}
        }
    },
    methods: {
        showModal: function () {
            this.openModal('online-token-modal');
        },
        resetModal: function () {
            this.closeModal('online-token-modal');
            this.form.token = "";
            this.errors     = {};
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.onlineOrderStore.addToken({
                    id: this.$route.params.id,
                    token: this.form.token
                }).then((res) => {
                    this.loading.isActive = false;
                    this.closeModal('online-token-modal');
                    this.form.token = "";
                    this.errors = {};
                    alertService.successFlip(0, this.$t("label.token"));
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        }
    }
}
</script>
