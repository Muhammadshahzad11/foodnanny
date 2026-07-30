const OrderTrackerComponent     = () => import("../../components/admin/orderTracker/OrderTrackerComponent.vue");
const OrderTrackerListComponent = () => import("../../components/admin/orderTracker/OrderTrackerListComponent.vue");

export default [
    {
        path: "/admin/order-tracker",
        component: OrderTrackerComponent,
        name: "admin.orderTracker",
        redirect: { name: "admin.orderTracker.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "order-tracker",
            breadcrumb: "order_tracker"
        },
        children: [
            {
                path: "",
                component: OrderTrackerListComponent,
                name: "admin.orderTracker.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "order-tracker",
                    breadcrumb: ""
                }
            },

        ]
    }
];
