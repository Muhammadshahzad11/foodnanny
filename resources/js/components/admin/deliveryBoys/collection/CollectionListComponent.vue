<template>
    <LoadingComponent :props="loading"/>
    <div class="db-card">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.collections") }}</h3>
        </div>
        <div class="db-table-responsive">
            <table class="db-table stripe" id="print">
                <thead class="db-table-head">
                <tr class="db-table-head-tr">
                    <th class="db-table-head-th">{{ $t("label.name") }}</th>
                    <th class="db-table-head-th">{{ $t("label.destination_user_name") }}</th>
                    <th class="db-table-head-th">{{ $t("label.date") }}</th>
                    <th class="db-table-head-th">{{ $t("label.amount") }}</th>
                </tr>
                </thead>
                <tbody class="db-table-body" v-if="collections.length > 0">
                <tr class="db-table-body-tr" v-for="collection in collections" :key="collection">
                    <td class="db-table-body-td">
                        {{ collection.source_user_name }}
                    </td>
                    <td class="db-table-body-td">
                        {{ collection.destination_user_name }}
                    </td>
                    <td class="db-table-body-td">
                        {{ collection.date }}
                    </td>
                    <td class="db-table-body-td">
                        {{ collection.flat_amount }}
                    </td>
                </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                <tr class="db-table-body-tr">
                    <td class="db-table-body-td" colspan="5">
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
            <PaginationSMBox :pagination="collectionPagination" :method="list"/>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <PaginationTextComponent :props="{ page: collectionPage }"/>
                <PaginationBox :pagination="collectionPagination" :method="list"/>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import {useFrontendSettingStore} from "../../../../stores/frontendSetting.js";
import {useDeliveryBoyStore} from "../../../../stores/deliveryBoy.js";

export default {
    name: "CollectionListComponent",
    components: {
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent
    },
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        const deliveryBoyStore     = useDeliveryBoyStore();
        return {
            frontendSettingStore,
            deliveryBoyStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    user_id: null
                }
            }
        };
    },
    mounted() {
        if (this.$route.params.id) {
            this.props.search.user_id = this.$route.params.id;
        }
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        collections: function () {
            return this.deliveryBoyStore.deliveryBoyCollections;
        },
        collectionPagination: function () {
            return this.deliveryBoyStore.collectionPagination;
        },
        collectionPage: function () {
            return this.deliveryBoyStore.collectionPage;
        }
    },
    methods: {
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.deliveryBoyStore.fetchDeliveryBoyCollection(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    }
};
</script>
