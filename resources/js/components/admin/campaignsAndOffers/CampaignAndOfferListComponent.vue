<template>
    <div class="col-12">
        <div class="row">
            <div class="col-12 sm:col-6 xl:col-3">
                <button type="button"
                    class="tab-active tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10"
                    @click="handleTab($event, 'campaigns')">
                    <i class="lab lab-line-offers lab-font-size-16"></i>
                    {{ $t('label.campaigns') }}
                </button>
            </div>
            <div class="col-12 sm:col-6 xl:col-3">
                <button type="button"
                    class="tab-button w-full flex items-center gap-2 px-4 h-10 rounded-lg bg-white hover:text-primary hover:bg-primary/10"
                    @click="handleTab($event, 'offers')">
                    <i class="lab lab-line-campaign lab-font-size-16"></i>
                    {{ $t('label.offers') }}
                </button>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="tab-content db-card tab-active" id="campaigns">
            <CampaignListComponent />
        </div>
        <div class="tab-content db-card" id="offers">
            <OfferListComponent />
        </div>
    </div>
</template>
<script>
import CampaignListComponent from "./CampaignListComponent.vue";
import OfferListComponent from "./OfferListComponent.vue";
import { useTab } from "../../../composables/tab.js";


export default {
    name: "CampaignAndOfferListComponent",
    components: {
        CampaignListComponent,
        OfferListComponent
    },
    data() {
        return {
            handleTab: useTab().handleTab,
        }
    },
};
</script>