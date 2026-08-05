<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" />

    <div id="sidebar" @click="closeBackdrop"
        class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-xl h-dvh overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="drawer-title">{{ $t('menu.push_notifications') }}</h3>
                <button @click="reset" class="lab-line-close font-bold text-base"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="row">
                        <div class="col-12 sm:col-6">
                            <label for="role_id" class="db-field-title">{{ $t("label.role") }}</label>
                            <vue-select class="db-field-control f-b-custom-select" @search:change="selectUser($event)"
                                id="role_id" v-bind:class="errors.role_id ? 'invalid' : ''" v-model="form.role_id"
                                :options="roles" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.role_id">{{ errors.role_id[0] }}</small>
                        </div>

                        <div class="col-12 sm:col-6">
                            <label for="user_id" class="db-field-title">{{ $t("label.user") }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="user_id"
                                v-bind:class="errors.user_id ? 'invalid' : ''" v-model="form.user_id" :options="users"
                                label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.user_id">{{ errors.user_id[0] }}</small>
                        </div>

                        <div class="col-12">
                            <label for="title" class="db-field-title required">{{ $t("label.title") }}</label>
                            <input v-model="form.title" v-bind:class="errors.title ? 'invalid' : ''" type="text"
                                id="title" class="db-field-control">
                            <small class="db-field-alert" v-if="errors.title">{{ errors.title[0] }}</small>
                        </div>

                        <div class="col-12">
                            <label class="db-field-title">{{ $t("label.image") }}</label>
                            <input @change="changeImage" v-bind:class="errors.image ? 'invalid' : ''" id="image"
                                type="file" class="db-field-control" ref="imageProperty" accept="image/png, image/jpeg, image/jpg">
                            <small class="db-field-alert" v-if="errors.image">{{ errors.image[0] }}</small>
                        </div>

                        <div class="col-12">
                            <label for="description" class="db-field-title required">
                                {{ $t("label.description") }}
                            </label>
                            <div :class="errors.description ? 'invalid textarea-error-box-style' : ''" class="custom-quill-editor">
                                <quill-editor id="description" v-model:value="form.description" class="!h-40 textarea-border-radius" />
                            </div>
                            <small class="db-field-alert" v-if="errors.description">{{ errors.description[0] }}</small>
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
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent.vue";
import LoadingComponent from "../../common/LoadingComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum.js";
import alertService from "../../../services/alertService.js";
import { useCanvas } from "../../../composables/canvas.js";
import { quillEditor } from 'vue3-quill';
import { useRoleStore } from "../../../stores/role.js";
import { useUserStore } from "../../../stores/user.js";
import { usePushNotificationStore } from "../../../stores/pushNotification.js";

export default {
    name: "PushNotificationCreateComponent",
    components: {
        SmSidebarModalCreateComponent,
        LoadingComponent,
        quillEditor
    },
    props: ['props'],
    setup() {
        const roleStore            = useRoleStore();
        const userStore            = useUserStore();
        const notificationStore    = usePushNotificationStore();
        return {
            roleStore,
            userStore,
            notificationStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            addButton: {
                title: this.$t("button.add_push_notification")
            },
            form: {
                title: "",
                description: "",
                role_id: null,
                user_id: null
            },
            enums: {
                statusEnum: statusEnum
            },
            image: "",
            errors: {},
            closeBackdrop: useCanvas().closeBackdrop
        }
    },
    computed: {
        roles: function () {
            return this.roleStore.lists;
        },
        users: function () {
            return this.userStore.lists;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.roleStore.fetch({
            order_column: 'id',
            order_type: 'asc'
        });
        this.userStore.fetch({
            order_column: 'id',
            order_type: 'asc',
            status: statusEnum.ACTIVE
        });
        this.loading.isActive = false;
    },
    methods: {
        changeImage: function (e) {
            this.image = e.target.files[0];
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.pushNotificationStore.reset().then().catch();
            this.errors = {};
            this.$props.props.form = {
                title: "",
                description: "",
                role_id: null,
                user_id: null
            };
            if (this.image) {
                this.image = "";
                this.$refs.imageProperty.value = null;
            }
        },
        selectUser: function (e) {
            this.userStore.fetch({
                order_column: 'id',
                order_type: 'asc',
                status: statusEnum.ACTIVE,
                role_id: this.form.role_id
            });
        },
        save: function () {
            try {
                const fd = new FormData();
                fd.append('title', this.form.title);
                fd.append('role_id', this.form.role_id == null ? 0 : this.form.role_id);
                fd.append('user_id', this.form.user_id == null ? 0 : this.form.user_id);
                fd.append('description', this.form.description);
                if (this.image) {
                    fd.append('image', this.image);
                }
                const tempId = this.notificationStore.temp.temp_id;
                this.loading.isActive = true;
                this.notificationStore.save({form: fd, search: this.props.search}).then((res) => {
                    useCanvas().closeCanvas('sidebar');
                    this.loading.isActive = false;
                    alertService.successFlip((tempId === null ? 0 : 1), this.$t('label.push_notification'));
                    this.form = {
                        title: "",
                        description: "",
                        role_id: null,
                        user_id: null,
                        status: statusEnum.ACTIVE
                    }
                    this.image = "";
                    this.errors = {};
                    this.$refs.imageProperty.value = null;
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
