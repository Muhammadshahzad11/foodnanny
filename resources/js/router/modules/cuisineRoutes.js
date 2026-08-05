const CuisineComponent     = () => import("../../components/admin/systemSetting/Cuisine/CuisineComponent.vue");
const CuisineListComponent = () => import("../../components/admin/systemSetting/Cuisine/CuisineListComponent.vue");
const CuisineShowComponent = () => import("../../components/admin/systemSetting/Cuisine/CuisineShowComponent.vue");

export default [
    {
        path: "/admin/cuisines",
        component: CuisineComponent,
        name: "admin.cuisines",
        redirect: {name: "admin.cuisines.list"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "cuisines",
            breadcrumb: "cuisines"
        },
        children: [
            {
                path: "list",
                component: CuisineListComponent,
                name: "admin.cuisines.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "cuisines",
                    breadcrumb: ""
                }
            },
            {
                path: "show/:id",
                component: CuisineShowComponent,
                name: "admin.cuisines.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "cuisines",
                    breadcrumb: "view"
                }
            }
        ]
    }
];
