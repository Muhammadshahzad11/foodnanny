<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="flex flex-col items-start sm:flex-row sm:items-center gap-1.5 mb-6">
            <button type="button"
                class="tab-active tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10"
                @click="handleTab($event, 'information')">
                <i class="lab lab-line-info-circle lab-font-size-16"></i>
                {{ $t('label.information') }}
            </button>
            <button type="button"
                class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10"
                @click="handleTab($event, 'translations')">
                <i class="lab lab-line-language lab-font-size-16"></i>
                {{ $t('label.translations') }}
            </button>
        </div>

        <div class="tab-content db-card tab-active" id="information">
            <div class="db-card">
                <div class="db-card-header">
                    <h3 class="db-card-title">{{ $t('menu.item_categories') }}</h3>
                </div>
                <div class="db-card-body">
                    <div class="row">
                        <div class="col-12 sm:col-3">
                            <img class="db-image" alt="category" :src="itemCategory.cover">
                        </div>
                        <div class="col-12 sm:col-7 md:pl-8">
                            <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">{{ itemCategory.name }}</h3>
                            <label class="db-badge mb-3" :class="statusClass(itemCategory.status)">
                                {{ enums.statusEnumArray[itemCategory.status] }}
                            </label>
                            <p class="w-full" v-html="itemCategory.description"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content db-card" id="translations">
            <ItemCategoryTranslationComponent />
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import appService from "../../../../services/appService.js";
import { useTab } from "../../../../composables/tab.js";
import { useItemCategoryStore } from "../../../../stores/itemCategory.js";
import ItemCategoryTranslationComponent from "./ItemCategoryTranslationComponent.vue";

export default {
    name: "ItemCategoryShowComponent",
    components: {
        LoadingComponent,
        ItemCategoryTranslationComponent
    },
    setup() {
        const itemCategoryStore = useItemCategoryStore();
        return {
            itemCategoryStore
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
            handleTab: useTab().handleTab
        }
    },
    computed: {
        itemCategory: function () {
            return this.itemCategoryStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.itemCategoryStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        }
    }
}
</script>
