<template>
    <LoadingComponent :props="loading"/>

    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("label.cuisine") }}</h3>
        </div>
        <div class="db-card-body">
            <div class="row">
                <div class="col-12 lg:col-3 sm:col-5">
                    <img class="db-image" alt="cuisine" :src="cuisine.image"/>
                </div>
                <div class="col-12 sm:col-7 md:pl-8">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">
                        {{ cuisine.name }}
                    </h3>
                    <label class="db-badge  mb-3" :class="statusClass(cuisine.status)">
                        {{ enums.statusEnumArray[cuisine.status] }}
                    </label>
                    <p class="db-light-text" v-html="cuisine.description"></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import statusEnum from "../../../../enums/modules/statusEnum.js";
import {useCuisineStore} from "../../../../stores/cuisine.js";
import appService from "../../../../services/appService.js";

export default {
    name: "CuisineShowComponent",
    components: {LoadingComponent},
    setup() {
        const cuisineStore = useCuisineStore();
        return {cuisineStore}
    },
    data() {
        return {
            loading: {
                isActive: false,
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
        cuisine: function () {
            return this.cuisineStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        if (typeof this.$route.params.id !== "undefined") {
            this.cuisineStore.view(this.$route.params.id).then((res) => {
                this.loading.isActive = false;
            }).catch((error) => {
                this.loading.isActive = false;
            })
        }
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        }
    }
}
</script>
