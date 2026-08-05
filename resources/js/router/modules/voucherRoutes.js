const VoucherComponent     = () => import("../../components/admin/vouchers/VoucherComponent.vue");
const VoucherListComponent = () => import("../../components/admin/vouchers/VoucherListComponent.vue");
const VoucherShowComponent = () => import("../../components/admin/vouchers/VoucherShowComponent.vue");

export default [
    {
        path: "/admin/vouchers",
        component: VoucherComponent,
        name: "admin.vouchers",
        redirect: { name: "admin.vouchers.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "vouchers",
            breadcrumb: "vouchers"
        },
        children: [
            {
                path: "",
                component: VoucherListComponent,
                name: "admin.vouchers.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "vouchers",
                    breadcrumb: ""
                }
            },
            {
                path: "show/:id",
                component: VoucherShowComponent,
                name: "admin.voucher.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "vouchers",
                    breadcrumb: "view"
                }
            }
        ]
    }
];
