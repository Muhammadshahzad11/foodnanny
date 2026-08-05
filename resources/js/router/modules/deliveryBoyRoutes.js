const DeliveryBoyComponent             = () => import("../../components/admin/deliveryBoys/DeliveryBoyComponent.vue");
const DeliveryBoyListComponent         = () => import("../../components/admin/deliveryBoys/DeliveryBoyListComponent.vue");
const DeliveryBoyShowComponent         = () => import("../../components/admin/deliveryBoys/DeliveryBoyShowComponent.vue");
const DeliveryBoyOrderDetailsComponent = () => import("../../components/admin/deliveryBoys/DeliveryBoyOrderDetailsComponent.vue");
const DeliveredOrderShowComponent      = () => import("../../components/admin/deliveryBoys/deliveredOrder/DeliveredOrderShowComponent.vue");

export default [
    {
        path: "/admin/delivery-boys",
        component: DeliveryBoyComponent,
        name: "admin.deliveryBoys",
        redirect: { name: "admin.deliveryBoys.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "delivery-boys",
            breadcrumb: "delivery_boys"
        },
        children: [
            {
                path: "",
                component: DeliveryBoyListComponent,
                name: "admin.deliveryBoys.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "delivery-boys",
                    breadcrumb: ""
                },
            },
            {
                path: "show/:id",
                component: DeliveryBoyShowComponent,
                name: "admin.deliveryBoys.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "delivery-boys",
                    breadcrumb: "view"
                },
            },
            {
                path: "show/:id/:orderId",
                component: DeliveryBoyOrderDetailsComponent,
                name: "admin.deliveryBoys.order.details",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "delivery-boys",
                    breadcrumb: "order_details"
                },
            },
            {
                path: "delivered-order/show/:id/:orderId",
                component: DeliveredOrderShowComponent,
                name: "admin.deliveryBoys.deliveredOrder.details",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "delivery-boys",
                    breadcrumb: "delivered_order_details"
                },
            },
        ],
    },
];
