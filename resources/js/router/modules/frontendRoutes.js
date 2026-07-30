const HomeComponent             = () => import("../../components/frontend/home/HomeComponent.vue");
const MyOrderComponent          = () => import("../../components/frontend/account/myOrder/MyOrderComponent.vue");
const EditProfileComponent      = () => import("../../components/frontend/account/editProfile/EditProfileComponent.vue");
const FavoriteComponent         = () => import("../../components/frontend/account/myFavorite/FavoriteComponent.vue");
const AddressComponent          = () => import("../../components/frontend/account/address/AddressComponent.vue");
const ChangePasswordComponent   = () => import("../../components/frontend/account/changePassword/ChangePasswordComponent.vue");
const CheckoutComponent         = () => import("../../components/frontend/checkout/CheckoutComponent.vue");
const RestaurantComponent       = () => import("../../components/frontend/restaurant/RestaurantComponent.vue");
const OfferAndCampaign          = () => import("../../components/frontend/offerAndCampaign/OfferAndCampaign.vue");
const SingleRestaurantComponent = () => import("../../components/frontend/restaurant/SingleRestaurantComponent.vue");
const PageComponent             = () => import("../../components/frontend/page/PageComponent.vue");
const OrderDetailsComponent     = () => import("../../components/frontend/account/myOrder/OrderDetailsComponent.vue");

export default [
    {
        path: "/home",
        component: HomeComponent,
        name: "frontend.home",
        meta: {
            template: "frontend",
            auth: false,
            mode : "main"
        }
    },
    {
        path: "/restaurant",
        component: RestaurantComponent,
        name: "frontend.restaurant",
        meta: {
            template: "frontend",
            auth: false,
            mode : "option"
        }
    },
    {
        path: "/restaurant/:slug",
        component: SingleRestaurantComponent,
        name: "frontend.singleRestaurant",
        meta: {
            template: "frontend",
            auth: false,
            mode : "restaurant"
        }
    },
    {
        path: "/offer-and-campaign/:slug/:type",
        component: OfferAndCampaign,
        name: "frontend.offerAndCampaign",
        meta: {
            template: "frontend",
            auth: false,
            mode : "restaurant"
        }
    },
    {
        path: "/my-orders",
        component: MyOrderComponent,
        name: "frontend.myOrder",
        meta: {
            template: "frontend",
            auth: true,
            mode : "main"
        }
    },
    {
        path: "/my-orders/:id",
        component: OrderDetailsComponent,
        name: "frontend.myOrder.details",
        meta: {
            template: "frontend",
            auth: true,
            mode : "main"
        },
    },
    {
        path: "/edit-profile",
        component: EditProfileComponent,
        name: "frontend.editProfile",
        meta: {
            template: "frontend",
            auth: true,
            mode : "main"
        }
    },
    {
        path: "/favorite",
        component: FavoriteComponent,
        name: "frontend.favorite",
        meta: {
            template: "frontend",
            auth: true,
            mode : "restaurant"
        },
    },
    {
        path: "/address",
        component: AddressComponent,
        name: "frontend.address",
        meta: {
            template: "frontend",
            auth: true,
            mode : "main"
        }
    },
    {
        path: "/change-password",
        component: ChangePasswordComponent,
        name: "frontend.changePassword",
        meta: {
            template: "frontend",
            auth: true,
            mode : "main"
        }
    },
    {
        path: "/page/:slug",
        component: PageComponent,
        name: "frontend.page",
        meta: {
            template: "frontend",
            auth: false,
            mode : "main"
        }
    },
    {
        path: "/checkout",
        component: CheckoutComponent,
        name: "frontend.checkout",
        meta: {
            template: "frontend",
            auth: true,
            mode : "restaurant"
        }
    }
];
