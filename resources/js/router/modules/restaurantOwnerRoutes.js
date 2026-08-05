const RestaurantOwnerComponent             = () => import("../../components/admin/restaurantOwners/RestaurantOwnerComponent.vue");
const RestaurantOwnerListComponent         = () => import("../../components/admin/restaurantOwners/RestaurantOwnerListComponent.vue");
const RestaurantOwnerShowComponent         = () => import("../../components/admin/restaurantOwners/RestaurantOwnerShowComponent.vue");
const RestaurantOwnerOrderDetailsComponent = () => import("../../components/admin/restaurantOwners/RestaurantOwnerOrderDetailsComponent.vue");

export default [
    {
        path: "/admin/restaurant-owners",
        component: RestaurantOwnerComponent,
        name: "admin.restaurantOwners",
        redirect: { name: "admin.restaurantOwners.list" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "restaurant-owners",
            breadcrumb: "restaurant_owners"
        },
        children: [
            {
                path: "",
                component: RestaurantOwnerListComponent,
                name: "admin.restaurantOwners.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "restaurant-owners",
                    breadcrumb: ""
                },
            },
            {
                path: "show/:id",
                component: RestaurantOwnerShowComponent,
                name: "admin.restaurantOwners.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "restaurant-owners",
                    breadcrumb: "view"
                },
            },
            {
                path: "show/:id/:orderId",
                component: RestaurantOwnerOrderDetailsComponent,
                name: "admin.restaurantOwners.order.details",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "restaurant-owners",
                    breadcrumb: "order_details",
                }
            }
        ]
    }
];
