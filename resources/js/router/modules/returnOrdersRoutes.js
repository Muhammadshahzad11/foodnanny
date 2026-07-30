const ReturnOrderComponent     = () => import("../../components/admin/returnOrders/ReturnOrderComponent.vue");
const ReturnOrderListComponent = () => import("../../components/admin/returnOrders/ReturnOrderListComponent.vue");
const ReturnOrderShowComponent = () => import("../../components/admin/returnOrders/ReturnOrderShowComponent.vue");

export default [
    {
        path: "/admin/return-orders",
        component: ReturnOrderComponent,
        name: "admin.returnOrders",
        redirect: { name: "admin.returnOrders.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "return-orders",
            breadcrumb: "return_orders"
        },
        children: [
            {
                path: "",
                component: ReturnOrderListComponent,
                name: "admin.returnOrders.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "return-orders",
                    breadcrumb: ""
                }
            },
            {
                path: "show/:id",
                component: ReturnOrderShowComponent,
                name: "admin.returnOrders.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "return-orders",
                    breadcrumb: "view"
                },
            }
        ]
    }
];
