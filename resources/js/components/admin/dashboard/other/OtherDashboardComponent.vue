<template>
    <OverviewComponent/>
    <div class="row">
        <TopDeliveryBoysComponent/>
        <MostPopularRestaurantsComponent/>
    </div>
</template>
<script>




import OverviewComponent from "./OverviewComponent.vue";
import MostPopularRestaurantsComponent from "../admin/MostPopularRestaurantsComponent.vue";
import TopDeliveryBoysComponent from "../admin/TopDeliveryBoysComponent.vue";

export default {
    name: "OtherDashboardComponent",
    components: {
        TopDeliveryBoysComponent,
        MostPopularRestaurantsComponent,
        OverviewComponent,
    }
}
</script>
