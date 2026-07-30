const CashoutComponent       = () => import("../../components/admin/cashouts/CashoutComponent.vue");
const CashoutListComponent   = () => import("../../components/admin/cashouts/CashoutListComponent.vue");
const CashoutCreateComponent = () => import("../../components/admin/cashouts/CashoutCreateComponent.vue");
const CashoutShowComponent   = () => import("../../components/admin/cashouts/CashoutShowComponent.vue");

export default [
    {
        path: "/admin/cashouts",
        component: CashoutComponent,
        name: "admin.cashouts",
        redirect: {name: "admin.cashouts.list"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "cashouts",
            breadcrumb: "cashouts"
        },
        children: [
            {
                path: "",
                component: CashoutListComponent,
                name: "admin.cashouts.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "cashouts",
                    breadcrumb: ""
                }
            },
            {
                path: "create",
                component: CashoutCreateComponent,
                name: "admin.cashouts.create",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "cashouts",
                    breadcrumb: "create"
                }
            },
            {
                path: "show/:id",
                component: CashoutShowComponent,
                name: "admin.cashouts.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "cashouts",
                    breadcrumb: "view"
                }
            }
        ]
    }
];
