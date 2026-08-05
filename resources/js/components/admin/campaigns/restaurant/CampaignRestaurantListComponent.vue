<template>
    <div class="db-card">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('label.restaurants') }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent @click.prevent="handlePaper" :method="list" :search="campaignProps.search"
                    :page="paginationPage" />
                <CampaignRestaurantCreateComponent :props="campaignProps" v-if="permissionChecker('campaigns_create')" />
            </div>
        </div>
    </div>
    <div class="db-card">
        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t("label.name") }}</th>
                        <th class="db-table-head-th">{{ $t("label.date") }}</th>
                        <th class="db-table-head-th">{{ $t("label.status") }}</th>
                        <th v-if="permissionChecker('campaigns_delete')" class="db-table-head-th">{{ $t("label.action")}}</th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="campaignRestaurants.length > 0">
                    <tr class="db-table-body-tr" v-for="campaignRestaurant in campaignRestaurants" :key="campaignRestaurant">
                        <td class="db-table-body-td">
                            {{ campaignRestaurant.campaign_restaurant_name }}
                        </td>
                        <td class="db-table-body-td">
                            {{ campaignRestaurant.date }}
                        </td>
                        <td class="db-table-body-td">
                            <span :class="campaignStatusClass(campaignRestaurant.status)">
                                {{ enums.campaignStatusEnumArray[campaignRestaurant.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td" v-if="permissionChecker('campaigns_delete')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconDeleteComponent @click="destroy(campaignRestaurant.id)" />
                                <SmIconModalVerifyComponent @click="restaurantVerifyModal(campaignRestaurant)"
                                    v-if="permissionChecker('campaigns_create')" />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td" colspan="4">
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
    <CampaignRestaurantVerifyComponent :props="campaignProps" />
</template>

<script>
import alertService from "../../../../services/alertService.js";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import campaignStatusEnum from "../../../../enums/modules/campaignStatusEnum.js";
import appService from "../../../../services/appService.js";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent.vue";
import CampaignRestaurantCreateComponent from "./CampaignRestaurantCreateComponent.vue";
import SmIconModalVerifyComponent from "../../components/buttons/SmIconModalVerifyComponent.vue";
import CampaignRestaurantVerifyComponent from "./CampaignRestaurantVerifyComponent.vue";
import { useModal } from "../../../../composables/modal.js";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import { usePaper } from "../../../../composables/paper.js";
import { useCampaignRestaurantStore } from "../../../../stores/campaignRestaurant.js";
import { useFrontendSettingStore } from "../../../../stores/frontendSetting.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "CampaignRestaurantListComponent",
    components: {
        CampaignRestaurantCreateComponent,
        SmIconDeleteComponent,
        SmIconModalVerifyComponent,
        CampaignRestaurantVerifyComponent,
        PaginationTextComponent,
        PaginationBox,
        PaginationSMBox,
        TableLimitComponent
    },
    props: {
        campaign: { type: Number }
    },
    setup() {
        const campaignRestaurantStore = useCampaignRestaurantStore();
        const frontendSettingStore = useFrontendSettingStore();
        return {
            frontendSettingStore,
            campaignRestaurantStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                campaignStatusEnum: campaignStatusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                },
                campaignStatusEnumArray: {
                    [campaignStatusEnum.PENDING]: this.$t("label.pending"),
                    [campaignStatusEnum.APPROVE]: this.$t("label.approved"),
                    [campaignStatusEnum.REJECT]: this.$t("label.rejected")
                }
            },
            campaignProps: {
                id: this.campaign,
                form: {
                    restaurant_id: null,
                    status: null
                },
                search: {
                    id: this.campaign,
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc'
                }
            },
            handlePaper: usePaper().handlePaper
        }
    },
    mounted() {
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        campaignRestaurants: function () {
            return this.campaignRestaurantStore.lists;
        },
        pagination: function () {
            return this.campaignRestaurantStore.pagination;
        },
        paginationPage: function () {
            return this.campaignRestaurantStore.page;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        campaignStatusClass: function (status) {
            return appService.campaignStatusClass(status);
        },
        restaurantVerifyModal: function (campaignRestaurant) {
            useModal().openModal('restaurantVerifyModal');
            this.loading.isActive = true;
            this.campaignRestaurantStore.verify(campaignRestaurant.id);
            this.campaignProps.form = {
                status: campaignRestaurant.status,
                restaurant_id: campaignRestaurant.restaurant_id
            };
            this.loading.isActive = false;
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.campaignProps.search.page = page;
            this.campaignRestaurantStore.fetch(this.campaignProps.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
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
                    this.campaignRestaurantStore.destroy({ campaign: this.campaign, id: id, search: this.campaignProps.search }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('label.restaurant'));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    })
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    }
}
</script>
