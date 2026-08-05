const RefundComponent     = () => import("../../components/admin/refund/RefundComponent.vue");
const RefundListComponent = () => import("../../components/admin/refund/RefundListComponent.vue");

export default [
    {
        path: "/admin/refunds",
        component: RefundComponent,
        name: "admin.refunds",
        redirect: { name: "admin.refunds.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "refunds",
            breadcrumb: "refunds"
        },
        children: [
            {
                path: "",
                component: RefundListComponent,
                name: "admin.refunds.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "refunds",
                    breadcrumb: ""
                }
            }
        ]
    }
];
