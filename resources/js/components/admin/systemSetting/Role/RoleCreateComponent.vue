<template>
    <LoadingComponent :props="loading" />
    <SmModalCreateComponent :props="addButton" />

    <div id="modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t('menu.role') }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500" @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-12">
                            <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                            <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text" id="name" class="db-field-control">
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>
                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab-fill-close-circle"></i>
                                    <span>{{ $t('button.close') }}</span>
                                </button>
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab-fill-save"></i>
                                    <span>{{ $t('button.save') }}</span>
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
import SmModalCreateComponent from "../../components/buttons/SmModalCreateComponent.vue";
import LoadingComponent from "../../../common/LoadingComponent.vue";
import { useModal } from "../../../../composables/modal.js";
import alertService from "../../../../services/alertService.js";
import {useRoleStore} from "../../../../stores/role.js";

export default {
    name: "RoleCreateComponent",
    components: { SmModalCreateComponent, LoadingComponent },
    props: ['props'],
    setup() {
        const roleStore = useRoleStore();
        return {roleStore}
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            addButton: {
                title: this.$t("button.add_role")
            },
            errors: {}
        }
    },
    methods: {
        reset: function () {
            useModal().closeModal('modal');
            this.roleStore.reset();
            this.errors = {};
            this.$props.props.form = {
                name: ""
            }
        },
        save: function () {
            try {
                const tempId = this.roleStore.temp.temp_id;
                this.loading.isActive = true;
                this.roleStore.save({form: this.props.form, search: this.props.search}).then((res) => {
                    useModal().closeModal('modal');
                    this.loading.isActive = false;
                    alertService.successFlip((tempId === null ? 0 : 1), this.$t('menu.role'));
                    this.props.form = {
                        name: ""
                    }
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err)
            }
        }
    }
}
</script>
