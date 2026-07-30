<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" />

    <div id="sidebar" @click="closeBackdrop" class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div class="w-full max-w-xl h-dvh overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h3 class="drawer-title">{{ $t('menu.subscribers') }}</h3>
                <button @click="reset" class="lab-line-close font-bold text-base"></button>
            </div>
            <div class="drawer-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-12">
                            <label for="subject" class="db-field-title required">{{ $t("label.subject") }}</label>
                            <input v-model="props.form.subject" v-bind:class="errors?.subject ? 'invalid' : ''"
                                type="text" id="title" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors?.subject">{{ errors.subject[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-12">
                            <label for="message" class="db-field-title required">{{$t("label.message")}}</label>
                            <textarea v-model="props.form.message" v-bind:class="errors?.message ? 'invalid' : ''"
                                id="message" class="db-field-control"></textarea>
                            <small class="db-field-alert" v-if="errors?.message">{{ errors.message[0] }}</small>
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
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import alertService from "../../../services/alertService.js";
import { useCanvas } from "../../../composables/canvas.js";
import { useSubscriberStore } from "../../../stores/subscriber.js";
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent.vue";

export default {
    name: "SubscriberMailComponent",
    components: { SmSidebarModalCreateComponent, LoadingComponent, Datepicker },
    props: ["props"],
    setup() {
        const subscriberStore = useSubscriberStore();
        return {
            subscriberStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            errors: {},
            closeBackdrop: useCanvas().closeBackdrop
        };
    },
    computed: {
        addButton: function () {
            return { title: this.$t("button.send_mail") }
        }
    },
    methods: {
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.subscriberStore.reset().then().catch();
            this.errors = {};
            this.$props.props.form = {
                subject: "",
                message: "",
            };
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.subscriberStore.sendEmail(this.props).then((res) => {
                        useCanvas().closeCanvas('sidebar');
                        this.loading.isActive = false;
                        alertService.successInfo(0, this.$t("message.email_send"));
                        this.props.form = {
                            subject: "",
                            message: "",
                        };
                        this.errors = {};
                    }).catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.errors;
                        if (err?.response?.data?.status === false) {
                            alertService.error(this.$t('message.email_send_failed'));
                        }
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
};
</script>
