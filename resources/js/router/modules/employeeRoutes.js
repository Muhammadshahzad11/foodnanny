const EmployeeComponent             = () => import("../../components/admin/employees/EmployeeComponent.vue");
const EmployeeListComponent         = () => import("../../components/admin/employees/EmployeeListComponent.vue");
const EmployeeShowComponent         = () => import("../../components/admin/employees/EmployeeShowComponent.vue");
const EmployeeOrderDetailsComponent = () => import("../../components/admin/employees/EmployeeOrderDetailsComponent.vue");

export default [
    {
        path: "/admin/employees",
        component: EmployeeComponent,
        name: "admin.employees",
        redirect: {name: "admin.employees.list"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "employees",
            breadcrumb: "employees"
        },
        children: [
            {
                path: "",
                component: EmployeeListComponent,
                name: "admin.employees.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "employees",
                    breadcrumb: ""
                },
            },
            {
                path: "show/:id",
                component: EmployeeShowComponent,
                name: "admin.employees.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "employees",
                    breadcrumb: "view"
                },
            },
            {
                path: "show/:id/:orderId",
                component: EmployeeOrderDetailsComponent,
                name: "admin.employees.order.details",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "employees",
                    breadcrumb: "order_details",
                },
            },
        ],
    },
];
