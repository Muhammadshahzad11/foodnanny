<template>
    <OverviewComponent/>

    <div class="row">
        <PayoutBalanceComponent/>
        <CollectionBalanceComponent/>
    </div>

    <div class="row">
        <ActiveOrdersComponent />
    </div>
</template>
<script>


import OverviewComponent from "./OverviewComponent.vue";
import PayoutBalanceComponent from "./PayoutBalanceComponent.vue";
import CollectionBalanceComponent from "./CollectionBalanceComponent.vue";
import ActiveOrdersComponent from "./ActiveOrdersComponent.vue";

export default {
    name: "DeliveryBoyDashboardComponent",
    components: {
        ActiveOrdersComponent,
        CollectionBalanceComponent,
        PayoutBalanceComponent,
        OverviewComponent
    }
}
</script>
