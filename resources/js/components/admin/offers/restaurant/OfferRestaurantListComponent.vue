<template>
    <div class="db-card">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('label.restaurants') }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent @click.prevent="handlePaper" :method="list" :search="offerProps.search" :page="paginationPage" />
                <OfferRestaurantCreateComponent :props="offerProps" v-if="permissionChecker('offers_create') && offerList.single === enums.askEnum.NO" />
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
                        <th v-if="permissionChecker('offers_delete')" class="db-table-head-th">{{ $t("label.action")}}</th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="offerRestaurants.length > 0">
                    <tr class="db-table-body-tr" v-for="offerRestaurant in offerRestaurants" :key="offerRestaurant">
                        <td class="db-table-body-td">
                            {{ offerRestaurant.offer_restaurant_name }}
                        </td>
                        <td class="db-table-body-td">
                            {{ offerRestaurant.date }}
                        </td>
                        <td class="db-table-body-td">
                            <span :class="offerStatusClass(offerRestaurant.status)">
                                {{ enums.offerStatusEnumArray[offerRestaurant.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td" v-if="permissionChecker('offers_delete')">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmIconDeleteComponent @click="destroy(offerRestaurant.id)" />
                                <SmIconModalVerifyComponent @click="restaurantVerifyModal(offerRestaurant)"
                                    v-if="permissionChecker('offers_create')" />
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
    <OfferRestaurantVerifyComponent :props="offerProps" />
</template>

<script>
import alertService from "../../../../services/alertService.js";
import askEnum from "../../../../enums/modules/askEnum.js";
import offerStatusEnum from "../../../../enums/modules/offerStatusEnum.js";
import appService from "../../../../services/appService.js";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent.vue";
import OfferRestaurantCreateComponent from "./OfferRestaurantCreateComponent.vue";
import SmIconModalVerifyComponent from "../../components/buttons/SmIconModalVerifyComponent.vue";
import OfferRestaurantVerifyComponent from "./OfferRestaurantVerifyComponent.vue";
import { useModal } from "../../../../composables/modal.js";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import { usePaper } from "../../../../composables/paper.js";
import { useOfferRestaurantStore } from "../../../../stores/offerRestaurant.js";
import { useFrontendSettingStore } from "../../../../stores/frontendSetting.js";
import { useOfferStore } from "../../../../stores/offer.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "OfferRestaurantListComponent",
    components: {
        OfferRestaurantCreateComponent,
        SmIconDeleteComponent,
        SmIconModalVerifyComponent,
        OfferRestaurantVerifyComponent,
        PaginationTextComponent,
        PaginationBox,
        PaginationSMBox,
        TableLimitComponent
    },
    props: {
        offer: { type: Number }
    },
    setup() {
        const offerRestaurantStore = useOfferRestaurantStore();
        const frontendSettingStore = useFrontendSettingStore();
        const offerStore = useOfferStore();

        return {
            frontendSettingStore,
            offerRestaurantStore,
            offerStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                askEnum: askEnum,
                offerStatusEnum: offerStatusEnum,
                offerStatusEnumArray: {
                    [offerStatusEnum.PENDING]: this.$t("label.pending"),
                    [offerStatusEnum.APPROVE]: this.$t("label.approved"),
                    [offerStatusEnum.REJECT]: this.$t("label.rejected")
                }
            },
            offerProps: {
                id: this.offer,
                form: {
                    restaurant_id: null,
                    status: null
                },
                search: {
                    id: this.offer,
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
        this.offerStore.view(this.offer).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        offerList: function () {
            return this.offerStore.show;
        },
        offerRestaurants: function () {
            return this.offerRestaurantStore.lists;
        },
        pagination: function () {
            return this.offerRestaurantStore.pagination;
        },
        paginationPage: function () {
            return this.offerRestaurantStore.page;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        offerStatusClass: function (status) {
            return appService.offerStatusClass(status);
        },
        restaurantVerifyModal: function (offerRestaurant) {
            useModal().openModal('restaurantVerifyModal');
            this.loading.isActive = true;
            this.offerRestaurantStore.verify(offerRestaurant.id);
            this.offerProps.form = {
                status: offerRestaurant.status,
                restaurant_id: offerRestaurant.restaurant_id
            };
            this.loading.isActive = false;
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.offerProps.search.page = page;
            this.offerRestaurantStore.fetch(this.offerProps.search).then((res) => {
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
                    this.offerRestaurantStore.destroy({ offer: this.offer, id: id, search: this.offerProps.search }).then((res) => {
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
