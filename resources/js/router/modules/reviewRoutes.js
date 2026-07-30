const ReviewComponent     = () => import("../../components/admin/reviews/ReviewComponent.vue");
const ReviewListComponent = () => import("../../components/admin/reviews/ReviewListComponent.vue");

export default [
    {
        path: "/admin/reviews",
        component: ReviewComponent,
        name: "admin.reviews",
        redirect: {name: "admin.reviews.list"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "reviews",
            breadcrumb: "reviews"
        },
        children: [
            {
                path: "",
                component: ReviewListComponent,
                name: "admin.reviews.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "reviews",
                    breadcrumb: ""
                }
            }
        ]
    }
];
