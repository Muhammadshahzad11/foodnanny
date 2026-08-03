const KitchenComponent = () => import("../../components/admin/kitchen/KitchenComponent.vue");
const KitchenDashboardComponent = () => import("../../components/admin/kitchen/KitchenDashboardComponent.vue");
const KitchenQueueComponent = () => import("../../components/admin/kitchen/KitchenQueueComponent.vue");
const KitchenOrderShowComponent = () => import("../../components/admin/kitchen/KitchenOrderShowComponent.vue");

export default [
    {
        path: "/admin/kitchen",
        component: KitchenComponent,
        name: "admin.kitchen",
        redirect: {name: "admin.kitchen.dashboard"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "kitchen",
            breadcrumb: "kitchen"
        },
        children: [
            {
                path: "",
                component: KitchenDashboardComponent,
                name: "admin.kitchen.dashboard",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "kitchen",
                    breadcrumb: ""
                },
            },
            {
                path: "queue",
                component: KitchenQueueComponent,
                name: "admin.kitchen.queue",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "kitchen",
                    breadcrumb: "queue"
                },
            },
            {
                path: "orders/:id",
                component: KitchenOrderShowComponent,
                name: "admin.kitchen.orders.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "kitchen",
                    breadcrumb: "view"
                },
            },
        ],
    },
];
