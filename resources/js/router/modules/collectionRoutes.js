const CollectionComponent       = () => import("../../components/admin/collections/CollectionComponent.vue");
const CollectionListComponent   = () => import("../../components/admin/collections/CollectionListComponent.vue");
const CollectionCreateComponent = () => import("../../components/admin/collections/CollectionCreateComponent.vue");

export default [
    {
        path: "/admin/collections",
        component: CollectionComponent,
        name: "admin.collections",
        redirect: {name: "admin.collections.list"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "collections",
            breadcrumb: "collections"
        },
        children: [
            {
                path: "",
                component: CollectionListComponent,
                name: "admin.collections.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "collections",
                    breadcrumb: ""
                }
            },
            {
                path: "create",
                component: CollectionCreateComponent,
                name: "admin.collections.create",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "collections",
                    breadcrumb: "create"
                }
            }
        ]
    }
];
