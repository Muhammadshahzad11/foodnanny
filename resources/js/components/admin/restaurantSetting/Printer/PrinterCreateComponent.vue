<template>
    <LoadingComponent :props="loading"/>
    <SmModalCreateComponent :props="addButton"/>

    <div id="modal" class="modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.printer_settings") }}</h3>
                <button class="modal-close lab-line-close font-bold text-base text-slate-400 hover:text-red-500" @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.name") }}</label>
                            <input v-model="props.form.name" :class="errors.name ? 'invalid' : ''" type="text" class="db-field-control"/>
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title">{{ $t("label.outlet") }}</label>
                            <input type="text" class="db-field-control" :value="outletName" disabled/>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.printing_choice") }}</label>
                            <select v-model.number="props.form.printing_choice" class="db-field-control" :class="errors.printing_choice ? 'invalid' : ''">
                                <option :value="enums.printingChoiceEnum.BROWSER_POPUP">{{ $t('label.browser_popup') }}</option>
                                <option :value="enums.printingChoiceEnum.DIRECT_PRINT">{{ $t('label.direct_print') }}</option>
                            </select>
                            <small class="db-field-alert" v-if="errors.printing_choice">{{ errors.printing_choice[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.print_format") }}</label>
                            <select v-model.number="props.form.print_format" class="db-field-control" :class="errors.print_format ? 'invalid' : ''">
                                <option :value="enums.printFormatEnum.INVOICE">{{ $t('label.invoice') }}</option>
                                <option :value="enums.printFormatEnum.KOT">{{ $t('label.kot') }}</option>
                                <option :value="enums.printFormatEnum.NO_AUTO_KOT">{{ $t('label.no_auto_kot') }}</option>
                            </select>
                            <small class="db-field-alert" v-if="errors.print_format">{{ errors.print_format[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.printer_type") }}</label>
                            <select v-model.number="props.form.printer_type" class="db-field-control" :class="errors.printer_type ? 'invalid' : ''">
                                <option :value="enums.printerTypeEnum.WINDOWS_SHARED">{{ $t('label.windows_shared_printer') }}</option>
                                <option :value="enums.printerTypeEnum.NETWORK">{{ $t('label.network_printer') }}</option>
                            </select>
                            <small class="db-field-alert" v-if="errors.printer_type">{{ errors.printer_type[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.characters_per_line") }}</label>
                            <input v-model.number="props.form.characters_per_line" type="number" min="24" max="80" class="db-field-control" :class="errors.characters_per_line ? 'invalid' : ''"/>
                            <small class="db-field-alert" v-if="errors.characters_per_line">{{ errors.characters_per_line[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.open_cash_drawer") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.askEnum.YES" v-model.number="props.form.open_cash_drawer" type="radio" id="drawer_yes" class="custom-radio-field"/>
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="drawer_yes" class="db-field-label">{{ $t('label.yes') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.askEnum.NO" v-model.number="props.form.open_cash_drawer" type="radio" id="drawer_no" class="custom-radio-field"/>
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="drawer_no" class="db-field-label">{{ $t('label.no') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t("label.invoice_qr_status") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.askEnum.YES" v-model.number="props.form.invoice_qr_status" type="radio" id="qr_yes" class="custom-radio-field"/>
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="qr_yes" class="db-field-label">{{ $t('label.yes') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.askEnum.NO" v-model.number="props.form.invoice_qr_status" type="radio" id="qr_no" class="custom-radio-field"/>
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="qr_no" class="db-field-label">{{ $t('label.no') }}</label>
                                </div>
                            </div>
                        </div>

                        <template v-if="props.form.printing_choice === enums.printingChoiceEnum.DIRECT_PRINT">
                            <div class="form-col-12">
                                <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900 leading-relaxed">
                                    {{ $t('message.direct_print_cloud_help') }}
                                    <a class="underline font-semibold ml-1" href="/local-print-agent/" target="_blank" rel="noopener">
                                        {{ $t('label.setup_local_print_agent') }}
                                    </a>
                                </div>
                            </div>
                            <div class="form-col-12 sm:form-col-4">
                                <label class="db-field-title required">{{ $t("label.computer_ipv4") }}</label>
                                <input v-model="props.form.computer_ipv4" type="text" class="db-field-control" :class="errors.computer_ipv4 ? 'invalid' : ''" placeholder="192.168.1.103"/>
                                <small class="text-[11px] text-[#6E7191]">{{ $t('message.computer_ip_hint') }}</small>
                                <small class="db-field-alert" v-if="errors.computer_ipv4">{{ errors.computer_ipv4[0] }}</small>
                            </div>
                            <div class="form-col-12 sm:form-col-4">
                                <label class="db-field-title required">{{ $t("label.printer_ip") }}</label>
                                <input v-model="props.form.printer_ip" type="text" class="db-field-control" :class="errors.printer_ip ? 'invalid' : ''" placeholder="192.168.1.50"/>
                                <small class="text-[11px] text-[#6E7191]">{{ $t('message.printer_ip_hint') }}</small>
                                <small class="db-field-alert" v-if="errors.printer_ip">{{ errors.printer_ip[0] }}</small>
                            </div>
                            <div class="form-col-12 sm:form-col-4">
                                <label class="db-field-title required">{{ $t("label.printer_port") }}</label>
                                <input v-model.number="props.form.printer_port" type="number" class="db-field-control" :class="errors.printer_port ? 'invalid' : ''"/>
                                <small class="db-field-alert" v-if="errors.printer_port">{{ errors.printer_port[0] }}</small>
                            </div>
                        </template>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{ $t('label.status') }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model.number="props.form.status" type="radio" id="printer_active" class="custom-radio-field"/>
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="printer_active" class="db-field-label">{{ $t('label.active') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model.number="props.form.status" type="radio" id="printer_inactive" class="custom-radio-field"/>
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="printer_inactive" class="db-field-label">{{ $t('label.inactive') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-fill-close-circle text-base"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>
                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save text-base"></i>
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
import SmModalCreateComponent from "../../components/buttons/SmModalCreateComponent.vue";
import LoadingComponent from "../../../common/LoadingComponent.vue";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import askEnum from "../../../../enums/modules/askEnum.js";
import printingChoiceEnum from "../../../../enums/modules/printingChoiceEnum.js";
import printFormatEnum from "../../../../enums/modules/printFormatEnum.js";
import printerTypeEnum from "../../../../enums/modules/printerTypeEnum.js";
import alertService from "../../../../services/alertService.js";
import {useModal} from "../../../../composables/modal.js";
import {usePrinterStore} from "../../../../stores/printer.js";
import {useAuthStore} from "../../../../stores/auth.js";

const defaultForm = () => ({
    name: "",
    printing_choice: printingChoiceEnum.BROWSER_POPUP,
    print_format: printFormatEnum.KOT,
    printer_type: printerTypeEnum.NETWORK,
    characters_per_line: 42,
    open_cash_drawer: askEnum.NO,
    invoice_qr_status: askEnum.NO,
    computer_ipv4: "",
    printer_ip: "",
    printer_port: 9100,
    status: statusEnum.ACTIVE,
});

export default {
    name: "PrinterCreateComponent",
    components: {SmModalCreateComponent, LoadingComponent},
    props: ["props"],
    setup() {
        const printerStore = usePrinterStore();
        const authStore = useAuthStore();
        return {printerStore, authStore};
    },
    data() {
        return {
            loading: {isActive: false},
            addButton: {title: this.$t("button.add")},
            enums: {
                statusEnum,
                askEnum,
                printingChoiceEnum,
                printFormatEnum,
                printerTypeEnum,
            },
            errors: {},
        };
    },
    computed: {
        outletName() {
            return this.authStore.info?.restaurant_name
                || this.authStore.info?.restaurant?.name
                || this.$t('label.outlet');
        },
    },
    methods: {
        reset() {
            useModal().closeModal('modal');
            this.printerStore.reset();
            this.errors = {};
            this.$props.props.form = defaultForm();
        },
        save() {
            const tempId = this.printerStore.temp.temp_id;
            this.loading.isActive = true;
            this.errors = {};
            this.printerStore.save(this.props).then(() => {
                useModal().closeModal('modal');
                this.loading.isActive = false;
                alertService.successFlip(tempId === null ? 0 : 1, this.$t("menu.printer_settings"));
                this.props.form = defaultForm();
                this.errors = {};
            }).catch((err) => {
                this.loading.isActive = false;
                this.errors = err.response?.data?.errors || {};
                const fieldErrors = Object.values(this.errors).flat().filter(Boolean);
                if (fieldErrors.length) {
                    alertService.error(fieldErrors[0]);
                } else if (err.response?.data?.message) {
                    alertService.error(err.response.data.message);
                } else {
                    alertService.error(this.$t('message.something_wrong'));
                }
            });
        },
    },
};
</script>
