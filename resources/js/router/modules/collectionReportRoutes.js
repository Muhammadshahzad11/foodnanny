const CollectionReportComponent     = () => import("../../components/admin/collectionReport/CollectionReportComponent.vue");
const CollectionReportListComponent = () => import("../../components/admin/collectionReport/CollectionReportListComponent.vue");

export default [
    {
        path: "/admin/collection-report",
        component: CollectionReportComponent,
        name: "admin.collectionReport",
        redirect: { name: "admin.collectionReport.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "collection-report",
            breadcrumb: "collection_report"
        },
        children: [
            {
                path: "",
                component: CollectionReportListComponent,
                name: "admin.collectionReport.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "collection-report",
                    breadcrumb: ""
                }
            }
        ]
    }
];
