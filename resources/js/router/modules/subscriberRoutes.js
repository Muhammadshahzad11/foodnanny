const SubscriberComponent     = () => import("../../components/admin/subscribers/SubscriberComponent.vue");
const SubscriberListComponent = () => import("../../components/admin/subscribers/SubscriberListComponent.vue");

export default [
    {
        path: "/admin/subscribers",
        component: SubscriberComponent,
        name: "admin.subscribers",
        redirect: { name: "admin.subscribers.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "subscribers",
            breadcrumb: "subscribers"
        },
        children: [
            {
                path: "",
                component: SubscriberListComponent,
                name: "admin.subscribers.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "subscribers",
                    breadcrumb: ""
                }
            }
        ]
    }
];
