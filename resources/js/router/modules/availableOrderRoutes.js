const AvailableOrderComponent     = () => import("../../components/admin/availableOrders/AvailableOrderComponent.vue");
const AvailableOrderListComponent = () => import("../../components/admin/availableOrders/AvailableOrderListComponent.vue");

export default [
    {
        path: '/admin/available-orders',
        component: AvailableOrderComponent,
        name: 'admin.availableOrder',
        redirect: {name: 'admin.availableOrder.list'},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'available-orders',
            breadcrumb: 'available_orders'
        },
        children: [
            {
                path: '',
                component: AvailableOrderListComponent,
                name: 'admin.availableOrder.list',
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: 'available-orders',
                    breadcrumb: ''
                },
            },
        ]
    }
]
