const AdministratorComponent             = () => import("../../components/admin/administrators/AdministratorComponent.vue");
const AdministratorListComponent         = () => import("../../components/admin/administrators/AdministratorListComponent.vue");
const AdministratorShowComponent         = () => import("../../components/admin/administrators/AdministratorShowComponent.vue");
const AdministratorOrderDetailsComponent = () => import("../../components/admin/administrators/AdministratorOrderDetailsComponent.vue");

export default [
    {
        path: "/admin/administrators",
        component: AdministratorComponent,
        name: "admin.administrators",
        redirect: {name: "admin.administrators.list"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "administrators",
            breadcrumb: "administrators"
        },
        children: [
            {
                path: "",
                component: AdministratorListComponent,
                name: "admin.administrators.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "administrators",
                    breadcrumb: ""
                }
            },
            {
                path: "show/:id",
                component: AdministratorShowComponent,
                name: "admin.administrators.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "administrators",
                    breadcrumb: "view"
                }
            },
            {
                path: "show/:id/:orderId",
                component: AdministratorOrderDetailsComponent,
                name: "admin.administrators.order.details",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "administrators",
                    breadcrumb: "order_details",
                },
            },
        ],
    },
];
