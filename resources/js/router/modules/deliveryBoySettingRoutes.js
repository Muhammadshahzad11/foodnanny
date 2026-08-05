const DeliveryBoySettingsComponent   = () => import("../../components/admin/deliveryBoySetting/DeliveryBoySettingsComponent.vue");
const DeliveryLocationSetupComponent = () => import("../../components/admin/deliveryBoySetting/DeliveryLocationSetup/DeliveryLocationSetupComponent.vue");
export default [
    {
        path: "/admin/delivery-boy-settings",
        component: DeliveryBoySettingsComponent,
        name: "admin.deliveryBoySettings",
        redirect: { name: "admin.deliveryBoySettings.deliveryLocationSetup" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "delivery-boy-settings",
            breadcrumb: "delivery_boy_settings"
        },
        children: [
            {
                path: "delivery-location-setup",
                component: DeliveryLocationSetupComponent,
                name: "admin.deliveryBoySettings.deliveryLocationSetup",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "delivery-boy-settings",
                    breadcrumb: "delivery_location_setup"
                },
            }
        ]
    }
];
