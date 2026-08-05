const ActiveOrderComponent     = () => import("../../components/admin/activeOrders/ActiveOrderComponent.vue");
const ActiveOrderListComponent = () => import("../../components/admin/activeOrders/ActiveOrderListComponent.vue");
const ActiveOrderShowComponent = () => import("../../components/admin/activeOrders/ActiveOrderShowComponent.vue");

export default [
    {
        path: '/admin/active-orders',
        component: ActiveOrderComponent,
        name: 'admin.activeOrder',
        redirect: {name: 'admin.activeOrder.list'},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'active-orders',
            breadcrumb: 'active_orders'
        },
        children: [
            {
                path: '',
                component: ActiveOrderListComponent,
                name: 'admin.activeOrder.list',
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: 'active-orders',
                    breadcrumb: ''
                },
            },
            {
                path: "show/:id",
                component: ActiveOrderShowComponent,
                name: "admin.activeOrder.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "active-orders",
                    breadcrumb: "view"
                },
            }
        ]
    }
]
