const OnlineOrderComponent     = () => import("../../components/admin/onlineOrders/OnlineOrderComponent.vue");
const OnlineOrderListComponent = () => import("../../components/admin/onlineOrders/OnlineOrderListComponent.vue");
const OnlineOrderShowComponent = () => import("../../components/admin/onlineOrders/OnlineOrderShowComponent.vue");

export default [
    {
        path: '/admin/online-orders',
        component: OnlineOrderComponent,
        name: 'admin.order',
        redirect: {name: 'admin.order.list'},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'online-orders',
            breadcrumb: 'online_orders'
        },
        children: [
            {
                path: '',
                component: OnlineOrderListComponent,
                name: 'admin.order.list',
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: 'online-orders',
                    breadcrumb: ''
                },
            },
            {
                path: "show/:id",
                component: OnlineOrderShowComponent,
                name: "admin.order.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "online-orders",
                    breadcrumb: "view"
                },
            }
        ]
    }
]
