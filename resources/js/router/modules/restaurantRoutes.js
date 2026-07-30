const RestaurantComponent     = () => import("../../components/admin/restaurants/RestaurantComponent.vue");
const RestaurantListComponent = () => import("../../components/admin/restaurants/RestaurantListComponent.vue");
const RestaurantShowComponent = () => import("../../components/admin/restaurants/RestaurantShowComponent.vue");

export default [
    {
        path: '/admin/restaurants',
        component: RestaurantComponent,
        name: 'admin.restaurants',
        redirect: {name: 'admin.restaurants.list'},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'restaurants',
            breadcrumb: 'restaurants'
        },
        children: [
            {
                path: '',
                component: RestaurantListComponent,
                name: 'admin.restaurants.list',
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: 'restaurants',
                    breadcrumb: ''
                }
            },
            {
                path: "show/:id",
                component: RestaurantShowComponent,
                name: "admin.restaurant.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "restaurants",
                    breadcrumb: "view"
                }
            }
        ]
    }
]
