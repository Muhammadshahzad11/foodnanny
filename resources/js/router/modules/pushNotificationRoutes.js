const PushNotificationComponent     = () => import("../../components/admin/pushNotification/PushNotificationComponent.vue");
const PushNotificationListComponent = () => import("../../components/admin/pushNotification/PushNotificationListComponent.vue");
const PushNotificationShowComponent = () => import("../../components/admin/pushNotification/PushNotificationShowComponent.vue");

export default [
    {
        path: '/admin/push-notifications',
        component: PushNotificationComponent,
        name: 'admin.pushNotification',
        redirect: {name: 'admin.pushNotification.list'},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'push-notifications',
            breadcrumb: 'push_notifications'
        },
        children: [
            {
                path: '',
                component: PushNotificationListComponent,
                name: 'admin.pushNotification.list',
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: 'push-notification',
                    breadcrumb: ''
                }
            },
            {
                path: "show/:id",
                component: PushNotificationShowComponent,
                name: "admin.pushNotification.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "push-notification",
                    breadcrumb: "view"
                }
            }
        ]
    }
]
