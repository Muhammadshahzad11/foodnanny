const CustomerComponent             = () => import("../../components/admin/customers/CustomerComponent.vue");
const CustomerListComponent         = () => import("../../components/admin/customers/CustomerListComponent.vue");
const CustomerShowComponent         = () => import("../../components/admin/customers/CustomerShowComponent.vue");
const CustomerOrderDetailsComponent = () => import("../../components/admin/customers/CustomerOrderDetailsComponent.vue");

export default [
    {
        path: "/admin/customers",
        component: CustomerComponent,
        name: "admin.customers",
        redirect: {name: "admin.customers.list"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "customers",
            breadcrumb: "customers"
        },
        children: [
            {
                path: "",
                component: CustomerListComponent,
                name: "admin.customers.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "customers",
                    breadcrumb: ""
                }
            },
            {
                path: "show/:id",
                component: CustomerShowComponent,
                name: "admin.customers.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "customers",
                    breadcrumb: "view"
                }
            },
            {
                path: "show/:id/:orderId",
                component: CustomerOrderDetailsComponent,
                name: "admin.customers.order.details",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "customers",
                    breadcrumb: "order_details",
                }
            },
        ],
    },
];
