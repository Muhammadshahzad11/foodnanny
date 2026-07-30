<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t('label.campaign') }}</h3>
            </div>
            <div class="db-card-body">
                <div class="row">
                    <div class="col-12 sm:col-5">
                        <img class="db-image" alt="campaign" :src="campaign.image">
                    </div>
                    <div class="col-12 sm:col-7 md:pl-8">
                        <ul class="db-list single">
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.title') }}</span>
                                <span class="db-list-item-text">{{ campaign.title }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.date') }}</span>
                                <span class="db-list-item-text">{{ campaign.convert_date }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.time') }}</span>
                                <span class="db-list-item-text">{{ campaign.convert_time }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.type') }}</span>
                                <span class="db-list-item-text">
                                    {{ enums.campaignTypeEnumArray[campaign.type] }}
                                </span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.amount') }}</span>
                                <span class="db-list-item-text">{{ campaign.flat_amount }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.status') }}</span>
                                <span class="db-list-item-text">{{ enums.statusEnumArray[campaign.status] }}</span>
                            </li>
                            <li class="db-list-item" v-if="campaign.apply_status">
                                <span class="db-list-item-title">{{ $t('label.apply_status') }}</span>
                                <span class="db-list-item-text">{{ enums.campaignStatusEnumArray[campaign.apply_status]
                                }}</span>
                            </li>
                            <li class="db-list-item !items-start">
                                <span class="db-list-item-title">{{ $t('label.description') }}</span>
                                <span v-html="campaign.description"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../components/common/LoadingComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum.js";
import campaignTypeEnum from "../../../enums/modules/campaignTypeEnum.js";
import campaignStatusEnum from "../../../enums/modules/campaignStatusEnum.js";
import { useCampaignAndOfferStore } from "../../../stores/campaignAndOffer.js";

export default {
    name: "CampaignShowComponent",
    components: {
        LoadingComponent,
    },
    setup() {
        const campaignAndOfferStore = useCampaignAndOfferStore();
        return {
            campaignAndOfferStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                campaignTypeEnum: campaignTypeEnum,
                campaignStatusEnum: campaignStatusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                },
                campaignTypeEnumArray: {
                    [campaignTypeEnum.FREE]: this.$t("label.free"),
                    [campaignTypeEnum.PAID]: this.$t("label.paid"),
                },
                campaignStatusEnumArray: {
                    [campaignStatusEnum.PENDING]: this.$t("label.pending"),
                    [campaignStatusEnum.APPROVE]: this.$t("label.approved"),
                    [campaignStatusEnum.REJECT]: this.$t("label.rejected"),
                },
            },
        }
    },
    computed: {
        campaign: function () {
            return this.campaignAndOfferStore.showCampaign;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.campaignAndOfferStore.fetchShowCampaign(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
}

</script>