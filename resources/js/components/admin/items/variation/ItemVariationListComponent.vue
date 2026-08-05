<template>
    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t('label.variation') }}</h3>
            <div class="db-card-filter">
                <ItemVariationCreateComponent :props="variationProps" v-if="permissionChecker('items_create')" />
            </div>
        </div>
        <div class="db-card-body">
            <div class="db-card mb-5" v-if="variations.length > 0" v-for="variation in variations" :key="variation">
                <div class="db-card-header border-none">
                    <h3 class="db-card-title">{{ variation.name }}</h3>
                </div>
                <div class="db-table-responsive">
                    <table class="db-table stripe table-auto sm:table-fixed w-full">
                        <thead class="db-table-head">
                            <tr class="db-table-head-tr">
                                <th class="db-table-head-th">{{ $t("label.name") }}</th>
                                <th class="db-table-head-th">{{ $t("label.additional_price") }}</th>
                                <th class="db-table-head-th">{{ $t("label.status") }}</th>
                                <th v-if="permissionChecker('items_edit') || permissionChecker('items_delete')"
                                    class="db-table-head-th">{{ $t("label.action") }}</th>
                            </tr>
                        </thead>
                        <tbody class="db-table-body" v-if="variation.children">
                            <tr class="db-table-body-tr" v-for="child in variation.children" :key="child">
                                <td class="db-table-body-td">
                                    {{ child.name }}
                                </td>
                                <td class="db-table-body-td">
                                    {{ child.flat_price }}
                                </td>
                                <td class="db-table-body-td">
                                    <span :class="statusClass(child.status)">
                                        {{ enums.statusEnumArray[child.status] }}
                                    </span>
                                </td>
                                <td class="db-table-body-td"
                                    v-if="permissionChecker('items_edit') || permissionChecker('items_delete')">
                                    <SmIconModalEditComponent @click="edit(child)"
                                        v-if="permissionChecker('items_edit')" />
                                    <SmIconDeleteComponent @click="destroy(child.id)"
                                        v-if="permissionChecker('items_delete')" />
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
            </div>
            <div v-else class="p-4">
                <img class="m-auto not-found max-w-[300px]" :src="setting.data_not_found" alt="Not Found">
                <span class="block mt-3 text-center text-lg">{{ $t('message.no_data_found') }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import alertService from "../../../../services/alertService.js";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import appService from "../../../../services/appService.js";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent.vue";
import SmIconModalEditComponent from "../../components/buttons/SmIconModalEditComponent.vue";
import ItemVariationCreateComponent from "./ItemVariationCreateComponent.vue";
import { useModal } from "../../../../composables/modal.js";
import { useFrontendSettingStore } from "../../../../stores/frontendSetting.js";
import {useItemVariationStore} from "../../../../stores/itemVariation.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "ItemVariationListComponent",
    components: {
        ItemVariationCreateComponent, SmIconModalEditComponent, SmIconDeleteComponent
    },
    props: {
        item: { type: Number }
    },
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        const itemVariationStore   = useItemVariationStore();

        return {
            frontendSettingStore,
            itemVariationStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            },
            variationProps: {
                id: 0,
                form: {
                    name: "",
                    price: null,
                    item_attribute_id: null,
                    caution: "",
                    status: statusEnum.ACTIVE
                },
                search: {
                    id: 0,
                    paginate: 0,
                    order_column: 'id',
                    order_type: 'asc'
                }
            }
        }
    },
    mounted() {
        this.variationProps.id = this.item;
        this.variationProps.search.id = this.item;
        this.list();
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        variations: function () {
            return this.itemVariationStore.listGroupByAttributes;
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        list: function () {
            this.loading.isActive = true;
            this.itemVariationStore.fetchListGroupByAttributes(this.variationProps.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (itemVariation) {
            useModal().openModal('modal');
            this.loading.isActive = true;
            this.itemVariationStore.edit(itemVariation.id);
            this.loading.isActive = false;
            this.variationProps.form = {
                name: itemVariation.name,
                price: itemVariation.flat_price,
                item_attribute_id: itemVariation.item_attribute_id,
                caution: itemVariation.caution,
                status: itemVariation.status
            };
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
                    this.itemVariationStore.destroy({ item: this.item, id: id, search: this.variationProps.search }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('label.variation'));
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
