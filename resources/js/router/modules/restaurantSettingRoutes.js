const RestaurantSettingsComponent = () => import("../../components/admin/restaurantSetting/RestaurantSettingsComponent.vue");
const OrderSetupComponent         = () => import("../../components/admin/restaurantSetting/OrderSetup/OrderSetupComponent.vue");
const MyRestaurantComponent       = () => import("../../components/admin/restaurantSetting/MyRestaurant/MyRestaurantComponent.vue");
const TimeSlotListComponent       = () => import("../../components/admin/restaurantSetting/TimeSlot/TimeSlotListComponent.vue");
const ItemCategoryComponent       = () => import("../../components/admin/restaurantSetting/ItemCategory/ItemCategoryComponent.vue");
const ItemCategoryListComponent   = () => import("../../components/admin/restaurantSetting/ItemCategory/ItemCateogryListComponent.vue");
const ItemCategoryShowComponent   = () => import("../../components/admin/restaurantSetting/ItemCategory/ItemCategoryShowComponent.vue");
const ItemAttributeComponent      = () => import("../../components/admin/restaurantSetting/ItemAttribute/ItemAttributeComponent.vue");
const ItemAttributeListComponent  = () => import("../../components/admin/restaurantSetting/ItemAttribute/ItemAttributeListComponent.vue");
const TaxComponent                = () => import("../../components/admin/restaurantSetting/Tax/TaxComponent.vue");
const TaxListComponent            = () => import("../../components/admin/restaurantSetting/Tax/TaxListComponent.vue");

export default [
    {
        path: "/admin/restaurant-settings",
        component: RestaurantSettingsComponent,
        name: "admin.restaurantSettings",
        redirect: { name: "admin.restaurantSettings.myRestaurant" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "restaurant-settings",
            breadcrumb: "restaurant_settings"
        },
        children: [
            {
                path: "my-restaurant",
                component: MyRestaurantComponent,
                name: "admin.restaurantSettings.myRestaurant",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "restaurant-settings",
                    breadcrumb: "my_restaurant"
                },
            },
            {
                path: "order-setup",
                component: OrderSetupComponent,
                name: "admin.restaurantSettings.orderSetup",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "restaurant-settings",
                    breadcrumb: "order_setup"
                },
            },
            {
                path: "time-slots",
                component: TimeSlotListComponent,
                name: "admin.restaurantSettings.timeSlot",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "restaurant-settings",
                    breadcrumb: "time_slots"
                }
            }, 
            {
                path: "item-categories",
                component: ItemCategoryComponent,
                name: "admin.restaurantSettings.itemCategory",
                redirect: { name: "admin.restaurantSettings.itemCategory.list" },
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "restaurant-settings",
                    breadcrumb: "item_categories"
                },
                children: [
                    {
                        path: "list",
                        component: ItemCategoryListComponent,
                        name: "admin.restaurantSettings.itemCategory.list",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "restaurant-settings",
                            breadcrumb: ""
                        },
                    },
                    {
                        path: "show/:id",
                        component: ItemCategoryShowComponent,
                        name: "admin.restaurantSettings.itemCategory.show",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "restaurant-settings",
                            breadcrumb: "view"
                        },
                    },
                ],
            },
            {
                path: "item-attributes",
                component: ItemAttributeComponent,
                name: "admin.restaurantSettings.itemAttribute",
                redirect: { name: "admin.restaurantSettings.itemAttribute.list" },
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "restaurant-settings",
                    breadcrumb: "item_attributes"
                },
                children: [
                    {
                        path: "list",
                        component: ItemAttributeListComponent,
                        name: "admin.restaurantSettings.itemAttribute.list",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "restaurant-settings",
                            breadcrumb: ""
                        },
                    },
                ],
            },
            {
                path: "taxes",
                component: TaxComponent,
                name: "admin.restaurantSettings.tax",
                redirect: { name: "admin.restaurantSettings.tax.list" },
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "restaurant-settings",
                    breadcrumb: "taxes"
                },
                children: [
                    {
                        path: "list",
                        component: TaxListComponent,
                        name: "admin.restaurantSettings.tax.list",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "restaurant-settings",
                            breadcrumb: ""
                        },
                    },
                ],
            },
        ],
    },
];
