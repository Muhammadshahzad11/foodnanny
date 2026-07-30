const ItemsReportComponent     = () => import("../../components/admin/itemsReport/ItemsReportComponent.vue");
const ItemsReportListComponent = () => import("../../components/admin/itemsReport/ItemsReportListComponent.vue");

export default [
    {
        path: "/admin/items-report",
        component: ItemsReportComponent,
        name: "admin.itemsReport",
        redirect: { name: "admin.itemsReport.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "items-report",
            breadcrumb: "items_report"
        },
        children: [
            {
                path: "",
                component: ItemsReportListComponent,
                name: "admin.itemsReport.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "items-report",
                    breadcrumb: ""
                }
            }
        ]
    }
];
