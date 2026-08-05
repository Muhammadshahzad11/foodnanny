const TableComponent = () => import("../../components/admin/tables/TableComponent.vue");
const TableListComponent = () => import("../../components/admin/tables/TableListComponent.vue");
const TableShowComponent = () => import("../../components/admin/tables/TableShowComponent.vue");

export default [
    {
        path: "/admin/tables",
        component: TableComponent,
        name: "admin.tables",
        redirect: {name: "admin.tables.list"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "tables",
            breadcrumb: "tables"
        },
        children: [
            {
                path: "",
                component: TableListComponent,
                name: "admin.tables.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "tables",
                    breadcrumb: ""
                },
            },
            {
                path: "show/:id",
                component: TableShowComponent,
                name: "admin.tables.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "tables",
                    breadcrumb: "view"
                },
            },
        ],
    },
];
