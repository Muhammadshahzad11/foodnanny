const SalesReportComponent     = () => import("../../components/admin/salesReport/SalesReportComponent.vue");
const SalesReportListComponent = () => import("../../components/admin/salesReport/SalesReportListComponent.vue");

export default [
    {
        path: "/admin/sales-report",
        component: SalesReportComponent,
        name: "admin.salesReport",
        redirect: { name: "admin.salesReport.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "sales-report",
            breadcrumb: "sales_report"
        },
        children: [
            {
                path: "",
                component: SalesReportListComponent,
                name: "admin.salesReport.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "sales-report",
                    breadcrumb: ""
                }
            }
        ]
    }
];
