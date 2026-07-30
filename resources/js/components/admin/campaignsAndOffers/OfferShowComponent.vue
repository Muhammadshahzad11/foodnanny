<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t("label.offer") }}</h3>
            </div>
            <div class="db-card-body">
                <div class="row">
                    <div class="col-12 sm:col-4">
                        <img class="db-image" alt="campaign" :src="offer.image">
                    </div>
                    <div class="col-12 sm:col-7 md:pl-8">
                        <ul class="db-list single">
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.title') }}</span>
                                <span class="db-list-item-text">{{ offer.title }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.discount') }}</span>
                                <span class="db-list-item-text">{{ offer.flat_amount }}%</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.date') }}</span>
                                <span class="db-list-item-text">{{ offer.convert_date }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.time') }}</span>
                                <span class="db-list-item-text">{{ offer.convert_time }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.location') }}</span>
                                <span class="db-list-item-text">{{ offer.location }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.latitude') }}</span>
                                <span class="db-list-item-text">{{ offer.latitude }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.longitude') }}</span>
                                <span class="db-list-item-text">{{ offer.longitude }}</span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.type') }}</span>
                                <span class="db-list-item-text">
                                    {{ enums.offerTypeEnumArray[offer.type] }}
                                </span>
                            </li>
                            <li class="db-list-item">
                                <span class="db-list-item-title">{{ $t('label.status') }}</span>
                                <span class="db-list-item-text">{{ enums.statusEnumArray[offer.status] }}</span>
                            </li>
                            <li class="db-list-item" v-if="offer.apply_status">
                                <span class="db-list-item-title">{{ $t('label.apply_status') }}</span>
                                <span class="db-list-item-text">{{ enums.offerStatusEnumArray[offer.apply_status]
                                }}</span>
                            </li>
                            <li class="db-list-item !items-start">
                                <span class="db-list-item-title">{{ $t('label.description') }}</span>
                                <span v-html="offer.description"></span>
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
import askEnum from "../../../enums/modules/askEnum.js";
import offerTypeEnum from "../../../enums/modules/offerTypeEnum.js";
import offerStatusEnum from "../../../enums/modules/offerStatusEnum.js";
import { useCampaignAndOfferStore } from "../../../stores/campaignAndOffer.js";

export default {
    name: "OfferShowComponent",
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
                offerTypeEnum: offerTypeEnum,
                offerStatusEnum: offerStatusEnum,
                askEnum: askEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                },
                offerTypeEnumArray: {
                    [offerTypeEnum.REGULAR]: this.$t("label.regular"),
                    [offerTypeEnum.PREMIER]: this.$t("label.premier")
                },
                offerStatusEnumArray: {
                    [offerStatusEnum.PENDING]: this.$t("label.pending"),
                    [offerStatusEnum.APPROVE]: this.$t("label.approved"),
                    [offerStatusEnum.REJECT]: this.$t("label.rejected"),
                },
            },
        }
    },
    computed: {
        offer: function () {
            return this.campaignAndOfferStore.showOffer;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.campaignAndOfferStore.fetchShowOffer(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
}

</script>
