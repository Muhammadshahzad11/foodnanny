<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.currencies") }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                <CurrencyCreateComponent :props="props" />
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t("label.name") }}</th>
                        <th class="db-table-head-th">
                            {{ $t("label.symbol") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.code") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.is_cryptocurrency") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.exchange_rate") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="currencies.length > 0">
                    <tr class="db-table-body-tr" v-for="currency in currencies" :key="currency">
                        <td class="db-table-body-td" v-if="setting.site_default_currency === currency.id">
                            {{ currency.name }}({{ $t('label.default') }})
                        </td>
                        <td class="db-table-body-td" v-else>
                            {{ currency.name }}
                        </td>
                        <td class="db-table-body-td">
                            {{ currency.symbol }}
                        </td>
                        <td class="db-table-body-td">
                            {{ currency.code }}
                        </td>
                        <td class="db-table-body-td">
                            <span :class="askClass(currency.is_cryptocurrency)">
                                {{ enums.askEnumArray[currency.is_cryptocurrency] }}
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            {{ currency.exchange_rate }}
                        </td>
                        <td class="db-table-body-td">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmModalEditComponent @click="edit(currency)" />
                                <SmDeleteComponent @click="destroy(currency.id)"
                                    v-if="setting.site_default_currency !== currency.id" />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="6">
                            <div class="p-4">
                                <img class="m-auto not-found max-w-[300px]" :src="setting.data_not_found" alt="Not Found">
                                <span class="block mt-3 text-center text-lg">{{ $t('message.no_data_found') }}</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
            <PaginationSMBox :pagination="pagination" :method="list" />
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <PaginationTextComponent :props="{ page: paginationPage }" />
                <PaginationBox :pagination="pagination" :method="list" />
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import CurrencyCreateComponent from "./CurrencyCreateComponent.vue";
import alertService from "../../../../services/alertService.js";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import appService from "../../../../services/appService.js";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent.vue";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent.vue";
import { useModal } from "../../../../composables/modal.js";
import askEnum from "../../../../enums/modules/askEnum.js";
import {useCurrencyStore} from "../../../../stores/currency.js";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "CurrencyComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        CurrencyCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent,
        askEnum
    },
    setup() {
        const currencyStore = useCurrencyStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            currencyStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                askEnum: askEnum,
                askEnumArray: {
                    [askEnum.YES]: this.$t("label.yes"),
                    [askEnum.NO]: this.$t("label.no")
                }
            },
            props: {
                form: {
                    name: "",
                    symbol: "",
                    code: "",
                    is_cryptocurrency: askEnum.NO,
                    exchange_rate: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc"
                }
            },
            site_default_currency: null
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        currencies: function () {
            return this.currencyStore.lists;
        },
        pagination: function () {
            return this.currencyStore.pagination;
        },
        paginationPage: function () {
            return this.currencyStore.page;
        }
    },
    methods: {
        askClass: function (ask) {
            return appService.askClass(ask);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.currencyStore.fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (currency) {
            useModal().openModal('modal');
            this.loading.isActive = true;
            this.currencyStore.edit(currency.id);
            this.props.form = {
                name             : currency.name,
                symbol           : currency.symbol,
                code             : currency.code,
                is_cryptocurrency: currency.is_cryptocurrency,
                exchange_rate    : currency.exchange_rate
            };
            this.loading.isActive = false;
        },
        destroy: function (id) {
            return new VueSimpleAlert.confirm(
                this.$t("message.delete_record"),
                this.$t("message.are_you_sure"),
                "warning",
                {
                    confirmButtonText: this.$t("button.yes_delete"),
                    cancelButtonText: this.$t("button.no_cancel"),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then((res) => {
                try {
                    this.loading.isActive = true;
                    this.currencyStore.destroy({
                        id: id,
                        search: this.props.search
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.currencies"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    }
};
</script>
