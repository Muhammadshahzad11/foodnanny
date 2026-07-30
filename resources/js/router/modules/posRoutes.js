const PosComponent = () => import("../../components/admin/pos/PosComponent.vue");

export default [
    {
        path: "/admin/pos",
        component: PosComponent,
        name: "admin.pos",
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "pos"
        },
    },
];
