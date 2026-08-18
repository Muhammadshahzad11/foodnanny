<template>
    <section class="pt-6 pb-24 md:pb-20">
        <div class="container">
            <div v-if="offerAndCampaign && offerAndCampaign.id" class="relative">
                <OfferCampaignBlock
                    :item="bannerItem"
                    :restaurants="offerAndCampaignRestaurants || []"
                    :offer-restaurant="findRestaurants"
                    :linkable="false"
                    variant="grid"
                />
                <button @click.prevent="openModal('offer-info-modal')"
                        class="w-8 h-8 rounded-full text-center shadow-filter absolute top-5 ltr:right-5 rtl:left-5 bg-white z-10">
                    <i class="lab-line-info-circle text-xl leading-8 text-primary"></i>
                </button>
            </div>
        </div>
    </section>

    <div id="offer-info-modal"
         class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-lg w-full rounded-xl mx-auto bg-white transition-all duration-300">
            <div class="flex items-center justify-between gap-4 py-4 px-6">
                <h3 class="text-lg font-semibold capitalize">{{ $t('label.offer_details') }}</h3>
                <button @click.prevent="closeModal('offer-info-modal')" class="lab-line-circle-cross text-lg text-danger"></button>
            </div>
            <div class="px-6 py-4">
                <h3 class="flex items-center gap-1.5 mb-4">
                    <i class="lab-line-offers text-xl text-primary"></i>
                    <span class="text-xl font-semibold">{{ offerAndCampaign.title }}</span>
                </h3>
                <p class="text-sm mb-1 text-heading">{{ $t('message.new_and_existing_customers') }}</p>
                <p class="text-sm mb-4 text-heading">{{ $t('message.valid_for_selected_restaurants') }}</p>
                <p class="text-sm mb-1 text-heading">{{ $t('label.valid_from') }} {{ offerAndCampaign.start_date }} - {{ offerAndCampaign.end_date }}</p>
                <p v-if="hasDiscount" class="text-sm mb-4 text-heading">{{
                        $t('label.discount')
                    }} {{ offerAndCampaign.percentage }}</p>
                <p v-if="offerAndCampaign.description" class="ql-ul-set text-xs"
                   v-html="offerAndCampaign.description"></p>
            </div>
        </div>
    </div>
</template>

<script>
import offerAndCampaignEnum from "../../../enums/modules/offerAndCampaignEnum.js";
import {useModal} from "../../../composables/modal.js";
import {useFrontendOfferStore} from "../../../stores/frontendOffer.js";
import {useFrontendCampaignStore} from "../../../stores/frontendCampaign.js";
import router from "../../../router/index.js";
import {useCommonStore} from "../../../stores/common.js";
import OfferCampaignBlock from "../components/OfferCampaignBlock.vue";

export default {
    name: "OfferAndCampaign",
    setup() {
        const {openModal, closeModal} = useModal();
        const commonStore             = useCommonStore();
        const frontendOfferStore      = useFrontendOfferStore();
        const frontendCampaignStore   = useFrontendCampaignStore();

        return {
            openModal,
            closeModal,
            commonStore,
            frontendOfferStore,
            frontendCampaignStore
        }
    },
    data() {
        return {
            type: null,
            offerAndCampaignEnum: offerAndCampaignEnum
        }
    },
    components: {OfferCampaignBlock},

    computed: {
        latitude: function () {
            return this.commonStore.latitude;
        },
        longitude: function () {
            return this.commonStore.longitude;
        },
        orderType: function () {
            return this.commonStore.order_type;
        },
        offerAndCampaign: function () {
            if (this.$route.params.type === this.offerAndCampaignEnum.OFFER) {
                return this.frontendOfferStore.show;
            } else if (this.$route.params.type === this.offerAndCampaignEnum.CAMPAIGN) {
                return this.frontendCampaignStore.show;
            }
        },
        bannerItem() {
            return {
                ...(this.offerAndCampaign || {}),
                type: this.type || this.$route.params.type,
            };
        },
        hasDiscount() {
            return this.type === this.offerAndCampaignEnum.OFFER
                && parseFloat(this.offerAndCampaign?.amount || 0) > 0;
        },
        offerAndCampaignRestaurants: function () {
            if (this.$route.params.type === this.offerAndCampaignEnum.OFFER) {
                return this.frontendOfferStore.showRestaurants;
            } else if (this.$route.params.type === this.offerAndCampaignEnum.CAMPAIGN) {
                return this.frontendCampaignStore.showRestaurants;
            }
        },
        findRestaurants: function () {
            return this.frontendOfferStore.find;
        }
    },
    mounted() {
        this.frontendOfferStore.fetchFind({
            latitude: this.latitude,
            longitude: this.longitude,
            delivery_order_type: this.orderType
        });

        if (Object.keys(this.$route.params).length > 0 && typeof this.$route.params.slug === 'string') {
            this.type = this.$route.params.type;
            if (this.type === this.offerAndCampaignEnum.OFFER) {
                this.frontendOfferStore.view({
                    slug: this.$route.params.slug,
                    latitude: this.latitude,
                    longitude: this.longitude,
                    delivery_order_type: this.orderType
                }).then(res => {
                    if (Object.keys(res.data.data).length === 0) {
                        router.push({name: "frontend.restaurant"});
                    }
                }).catch((err) => {
                    router.push({name: "frontend.restaurant"});
                });
            } else if (this.type === this.offerAndCampaignEnum.CAMPAIGN) {
                this.frontendCampaignStore.view({
                    slug: this.$route.params.slug,
                    latitude: this.latitude,
                    longitude: this.longitude,
                    delivery_order_type: this.orderType
                }).then(res => {
                    if (Object.keys(res.data.data).length === 0) {
                        router.push({name: "frontend.restaurant"});
                    }
                }).catch((err) => {
                    router.push({name: "frontend.restaurant"});
                });
            } else {
                router.push({name: "frontend.restaurant"});
            }
        }
    }
}
</script>
