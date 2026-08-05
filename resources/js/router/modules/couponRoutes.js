const CouponComponent     = () => import("../../components/admin/coupons/CouponComponent.vue");
const CouponListComponent = () => import("../../components/admin/coupons/CouponListComponent.vue");
const CouponShowComponent = () => import("../../components/admin/coupons/CouponShowComponent.vue");

export default [
    {
        path: "/admin/coupons",
        component: CouponComponent,
        name: "admin.coupons",
        redirect: { name: "admin.coupons.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "coupons",
            breadcrumb: "coupons"
        },
        children: [
            {
                path: "",
                component: CouponListComponent,
                name: "admin.coupons.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "coupons",
                    breadcrumb: ""
                },
            },
            {
                path: "show/:id",
                component: CouponShowComponent,
                name: "admin.coupon.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "coupons",
                    breadcrumb: "view"
                },
            },
        ],
    },
];
