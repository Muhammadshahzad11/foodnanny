<template>
    <LoadingComponent :props="loading"/>

    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("label.page") }}</h3>
        </div>
        <div class="db-card-body">
            <div class="row">
                <div class="col-12 sm:col-5" v-if="page.image">
                    <img class="db-image" alt="page" :src="page.image"/>
                </div>
                <div class="col-12 md:pl-8" :class="page.image ? 'sm:col-7' : 'sm:col-12'">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">
                        {{ page.title }}
                    </h3>
                    <label class="db-badge mb-3" :class="statusClass(page.status)">
                        {{ enums.statusEnumArray[page.status] }}
                    </label>
                    <p class="db-light-text" v-html="page.description"></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import appService from "../../../../services/appService.js";
import {usePageStore} from "../../../../stores/page.js";

export default {
    name: "PageShowComponent",
    components: {
        LoadingComponent
    },
    setup() {
        const pageStore = usePageStore();
        return {
            pageStore
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
            }
        };
    },
    computed: {
        page: function () {
            return this.pageStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.pageStore.view(this.$route.params.id).then((res) => {
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
};
</script>
