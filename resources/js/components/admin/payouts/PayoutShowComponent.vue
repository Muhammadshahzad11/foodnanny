<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t('label.payout') }}</h3>
            </div>
            <div class="db-card-body">
                <div class="row">
                    <div class="col-12 sm:col-7">
                        <ul class="db-list single">
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.name') }}</span>
                                <span class="db-list-item-text">{{ payout.name }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.email') }}</span>
                                <span class="db-list-item-text">{{ payout.email }}</span>
                            </li>
                            <li v-if="payout.phone" class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.phone') }}</span>
                                <span class="db-list-item-text">{{ payout.country_code + payout.phone }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.amount') }}</span>
                                <span class="db-list-item-text">{{ payout.amount }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.date') }}</span>
                                <span class="db-list-item-text">{{ payout.date }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import { useFrontendSettingStore } from "../../../stores/frontendSetting.js";
import { usePayoutStore } from "../../../stores/payout.js";

export default {
    name: "PayoutShowComponent",
    components: {
        LoadingComponent
    },
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        const payoutStore = usePayoutStore();

        return {
            frontendSettingStore,
            payoutStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            }
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
        payout: function () {
            return this.payoutStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.payoutStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    }
}
</script>
