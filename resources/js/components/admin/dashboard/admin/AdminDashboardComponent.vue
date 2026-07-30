<template>
    <OverviewComponent/>

    <div class="row">
        <SalesSummaryComponent/>
        <OrdersSummaryComponent/>
        <RevenueComponent/>
        <TopCustomersComponent/>
        <TopDeliveryBoysComponent/>
        <MostPopularRestaurantsComponent/>
    </div>
</template>
<script>
import OverviewComponent from "./OverviewComponent.vue";
import SalesSummaryComponent from "./SalesSummaryComponent.vue";
import OrdersSummaryComponent from "./OrdersSummaryComponent.vue";
import TopCustomersComponent from "./TopCustomersComponent.vue";
import TopDeliveryBoysComponent from "./TopDeliveryBoysComponent.vue";
import MostPopularRestaurantsComponent from "./MostPopularRestaurantsComponent.vue";
import RevenueComponent from "./RevenueComponent.vue";

export default {
    name: "AdminDashboardComponent",
    components: {
        RevenueComponent,
        TopCustomersComponent,
        SalesSummaryComponent,
        OverviewComponent,
        OrdersSummaryComponent,
        TopDeliveryBoysComponent,
        MostPopularRestaurantsComponent
    }
}
</script>
