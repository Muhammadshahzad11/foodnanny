const LoginComponent                       = () => import("../../components/frontend/auth/LoginComponent.vue");
const LoginVerifyComponent                 = () => import("../../components/frontend/auth/LoginVerifyComponent.vue");
const ForgotPasswordComponent              = () => import("../../components/frontend/auth/ForgotPasswordComponent.vue");
const SignupPhoneComponent                 = () => import("../../components/frontend/auth/SignupPhoneComponent.vue");
const GuestLoginComponent                  = () => import("../../components/frontend/auth/GuestLoginComponent.vue");
const DeliveryBoyPhoneComponent            = () => import("../../components/frontend/auth/DeliveryBoyPhoneComponent.vue");
const DeliveryBoyVerifyComponent           = () => import("../../components/frontend/auth/DeliveryBoyVerifyComponent.vue");
const DeliveryBoyRegisterComponent         = () => import("../../components/frontend/auth/DeliveryBoyRegisterComponent.vue");
const RestaurantPhoneComponent             = () => import("../../components/frontend/auth/RestaurantPhoneComponent.vue");
const RestaurantVerifyComponent            = () => import("../../components/frontend/auth/RestaurantVerifyComponent.vue");
const RestaurantOwnerComponent             = () => import("../../components/frontend/auth/RestaurantOwnerComponent.vue");
const RestaurantInfoComponent              = () => import("../../components/frontend/auth/RestaurantInfoComponent.vue");
const SignupVerifyComponent                = () => import("../../components/frontend/auth/SignupVerifyComponent.vue");
const SignupRegisterComponent              = () => import("../../components/frontend/auth/SignupRegisterComponent.vue");
const GuestVerifyComponent                 = () => import("../../components/frontend/auth/GuestVerifyComponent.vue");
const ForgotPasswordEmailVerifyComponent   = () => import("../../components/frontend/auth/ForgotPasswordEmailVerifyComponent.vue");
const ForgotPasswordResetPasswordComponent = () => import("../../components/frontend/auth/ForgotPasswordResetPasswordComponent.vue");

export default [
    {
        path: '/login',
        component: LoginComponent,
        name: 'auth.login',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        },
    },
    {
        path: '/login/verify',
        component: LoginVerifyComponent,
        name: 'auth.loginVerify',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        },
    },
    {
        path: '/forgot-password',
        component: ForgotPasswordComponent,
        name: 'auth.forgotPassword',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/forgot-password/verify',
        name: 'auth.forgotPasswordEmailVerify',
        component: ForgotPasswordEmailVerifyComponent,
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/forgot-password/reset-password',
        name: 'auth.forgotPasswordResetPassword',
        component: ForgotPasswordResetPasswordComponent,
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/signup',
        component: SignupPhoneComponent,
        name: 'auth.signupPhone',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/signup/verify',
        component: SignupVerifyComponent,
        name: 'auth.signupVerify',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/signup/register',
        component: SignupRegisterComponent,
        name: 'auth.signupRegister',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/guest-login',
        component: GuestLoginComponent,
        name: 'auth.guestLogin',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/guest-login/verify',
        component: GuestVerifyComponent,
        name: 'auth.guestLoginVerify',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/signup-restaurant',
        component: RestaurantPhoneComponent,
        name: 'auth.signupRestaurant',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/signup-restaurant/verify',
        component: RestaurantVerifyComponent,
        name: 'auth.signupRestaurantVerify',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/signup-restaurant/owner',
        component: RestaurantOwnerComponent,
        name: 'auth.signupRestaurantOwner',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/signup-restaurant/info',
        component: RestaurantInfoComponent,
        name: 'auth.signupRestaurantInfo',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/signup-delivery-boy',
        component: DeliveryBoyPhoneComponent,
        name: 'auth.signupDeliveryBoy',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/signup-delivery-boy/verify',
        component: DeliveryBoyVerifyComponent,
        name: 'auth.signupDeliveryBoyVerify',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
    {
        path: '/signup-delivery-boy/register',
        component: DeliveryBoyRegisterComponent,
        name: 'auth.signupDeliveryBoyRegister',
        meta: {
            template: "frontend",
            auth: false,
            mode: "main"
        }
    },
];
