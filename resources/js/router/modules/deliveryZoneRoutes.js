const DeliveryZoneComponent         = () => import("../../components/admin/restaurantSetting/DeliveryZone/DeliveryZoneComponent.vue");
const DeliveryZoneListComponent     = () => import("../../components/admin/restaurantSetting/DeliveryZone/DeliveryZoneListComponent.vue");
const DeliveryZoneFormComponent     = () => import("../../components/admin/restaurantSetting/DeliveryZone/DeliveryZoneFormComponent.vue");
const DeliveryZoneSettingsComponent = () => import("../../components/admin/restaurantSetting/DeliveryZone/DeliveryZoneSettingsComponent.vue");

export default [
    {
        path: "/admin/delivery-zones",
        component: DeliveryZoneComponent,
        name: "admin.deliveryZones",
        redirect: {name: "admin.deliveryZones.list"},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "delivery-zones",
            breadcrumb: "delivery_zones"
        },
        children: [
            {
                path: "",
                component: DeliveryZoneListComponent,
                name: "admin.deliveryZones.list",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "delivery-zones",
                    breadcrumb: ""
                },
            },
            {
                path: "create",
                component: DeliveryZoneFormComponent,
                name: "admin.deliveryZones.create",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "delivery-zones",
                    breadcrumb: "create"
                },
            },
            {
                path: "edit/:id",
                component: DeliveryZoneFormComponent,
                name: "admin.deliveryZones.edit",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "delivery-zones",
                    breadcrumb: "edit"
                },
            },
            {
                path: "settings/:id",
                component: DeliveryZoneSettingsComponent,
                name: "admin.deliveryZones.settings",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "delivery-zones",
                    breadcrumb: "zone_settings"
                },
            },
        ],
    },
];
