<template>
    <LoadingComponent :props="loading" />
    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t('menu.about_steps') }}</h3>
        </div>
        <div class="db-card-body">
            <div class="row">
                <div class="col-3 sm:col-2">
                    <img class="db-image" alt="about steps" :src="aboutStep.cover">
                </div>
                <div class="col-9 sm:col-10">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">{{ aboutStep.title }}</h3>
                    <label class="db-badge mb-3" :class="statusClass(aboutStep.status)">
                        {{ enums.statusEnumArray[aboutStep.status] }}
                    </label>
                    <p class="db-light-text">
                        {{ aboutStep.description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import appService from "../../../../services/appService.js";
import {useAboutStepStore} from "../../../../stores/aboutStep.js";

export default {
    name: "AboutStepsShowComponent",
    components: {
        LoadingComponent
    },
    setup() {
        const aboutStepStore = useAboutStepStore();
        return {aboutStepStore}
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
        }
    },
    computed: {
        aboutStep: function () {
            return this.aboutStepStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.aboutStepStore.view(this.$route.params.id).then(res => {
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
