const PosOrderComponent     = () => import("../../components/admin/posOrders/PosOrderComponent.vue");
const PosOrderListComponent = () => import("../../components/admin/posOrders/PosOrderListComponent.vue");
const PosOrderShowComponent = () => import("../../components/admin/posOrders/PosOrderShowComponent.vue");

export default [
    {
        path: "/admin/pos-orders",
        component: PosOrderComponent,
        name: "admin.pos.orders",
        redirect: { name: "admin.pos.orders.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'pos',
            breadcrumb: 'pos_orders'
        },
        children: [
            {
                path: "",
                component: PosOrderListComponent,
                name: "admin.pos.orders.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "pos",
                    breadcrumb: ""
                },
            },
            {
                path: "show/:id",
                component: PosOrderShowComponent,
                name: "admin.pos.orders.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "pos",
                    breadcrumb: "view"
                },
            }
        ],
    },
];
