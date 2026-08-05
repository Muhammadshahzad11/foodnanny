const MessageComponent = () => import("../../components/admin/messages/MessageComponent.vue");

export default [
    {
        path: "/admin/messages",
        component: MessageComponent,
        name: "admin.messages",
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "messages",
            breadcrumb: "messages"
        }
    }
];
