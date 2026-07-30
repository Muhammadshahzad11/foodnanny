const PayoutComponent       = () => import("../../components/admin/payouts/PayoutComponent.vue");
const PayoutListComponent   = () => import("../../components/admin/payouts/PayoutListComponent.vue");
const PayoutCreateComponent = () => import("../../components/admin/payouts/PayoutCreateComponent.vue");
const PayoutShowComponent   = () => import("../../components/admin/payouts/PayoutShowComponent.vue");

export default [
    {
        path: "/admin/payouts",
        component: PayoutComponent,
        name: "admin.payouts",
        redirect: {name: "admin.payouts.list"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "payouts",
            breadcrumb: "payouts"
        },
        children: [
            {
                path: "",
                component: PayoutListComponent,
                name: "admin.payouts.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "payouts",
                    breadcrumb: ""
                }
            },
            {
                path: "create",
                component: PayoutCreateComponent,
                name: "admin.payouts.create",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "payouts",
                    breadcrumb: "create"
                }
            },
            {
                path: "show/:id",
                component: PayoutShowComponent,
                name: "admin.payouts.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "payouts",
                    breadcrumb: "view"
                }
            }
        ]
    }
]
