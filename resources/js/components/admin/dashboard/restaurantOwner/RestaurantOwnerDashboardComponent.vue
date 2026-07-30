<template>
    <OverviewComponent/>

    <div class="row">
        <SalesSummaryComponent/>
        <OrdersSummaryComponent/>
        <CustomerStatsComponent/>
        <MostPopularItemsComponent/>
    </div>
</template>
<script>
import SalesSummaryComponent from "../admin/SalesSummaryComponent.vue";
import OrdersSummaryComponent from "../admin/OrdersSummaryComponent.vue";
import CustomerStatsComponent from "./CustomerStatsComponent.vue";
import MostPopularItemsComponent from "./MostPopularItemsComponent.vue";
import OverviewComponent from "./OverviewComponent.vue";

export default {
    name: "RestaurantOwnerDashboardComponent",
    components: {
        OverviewComponent,
        MostPopularItemsComponent,
        CustomerStatsComponent,
        OrdersSummaryComponent,
        SalesSummaryComponent
    }
}
</script>
