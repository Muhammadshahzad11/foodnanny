const WaiterComponent = () => import("../../components/admin/waiter/WaiterComponent.vue");
const WaiterDashboardComponent = () => import("../../components/admin/waiter/WaiterDashboardComponent.vue");
const WaiterTablesComponent = () => import("../../components/admin/waiter/WaiterTablesComponent.vue");
const WaiterOrderPadComponent = () => import("../../components/admin/waiter/WaiterOrderPadComponent.vue");
const WaiterOrderShowComponent = () => import("../../components/admin/waiter/WaiterOrderShowComponent.vue");

export default [
    {
        path: "/admin/waiter",
        component: WaiterComponent,
        name: "admin.waiter",
        redirect: {name: "admin.waiter.dashboard"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "waiter",
            breadcrumb: "waiter"
        },
        children: [
            {
                path: "",
                component: WaiterDashboardComponent,
                name: "admin.waiter.dashboard",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "waiter",
                    breadcrumb: ""
                },
            },
            {
                path: "tables",
                component: WaiterTablesComponent,
                name: "admin.waiter.tables",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "waiter",
                    breadcrumb: "tables"
                },
            },
            {
                path: "tables/:id",
                component: WaiterOrderPadComponent,
                name: "admin.waiter.table.order",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "waiter",
                    breadcrumb: "order"
                },
            },
            {
                path: "orders/:id",
                component: WaiterOrderShowComponent,
                name: "admin.waiter.orders.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "waiter",
                    breadcrumb: "view"
                },
            },
        ],
    },
];
