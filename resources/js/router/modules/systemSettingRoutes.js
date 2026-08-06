const SystemSettingsComponent     = () => import("../../components/admin/systemSetting/SystemSettingsComponent.vue");
const CompanyComponent            = () => import("../../components/admin/systemSetting/Company/CompanyComponent.vue");
const LanguageComponent           = () => import("../../components/admin/systemSetting/Language/LanguageComponent.vue");
const LanguageListComponent       = () => import("../../components/admin/systemSetting/Language/LanguageListComponent.vue");
const LanguageShowComponent       = () => import("../../components/admin/systemSetting/Language/LanguageShowComponent.vue");
const CurrencyComponent           = () => import("../../components/admin/systemSetting/Currency/CurrencyComponent.vue");
const SmsGatewayComponent         = () => import("../../components/admin/systemSetting/SmsGateway/SmsGatewayComponent.vue");
const AiAgentComponent            = () => import("../../components/admin/systemSetting/AiAgent/AiAgentComponent.vue");
const SiteComponent               = () => import("../../components/admin/systemSetting/Site/SiteComponent.vue");
const OtpComponent                = () => import("../../components/admin/systemSetting/Otp/OtpComponent.vue");
const NotificationComponent       = () => import("../../components/admin/systemSetting/Notification/NotificationComponent.vue");
const SocialMediaComponent        = () => import("../../components/admin/systemSetting/SocialMedia/SocialMediaComponent.vue");
const ThemeComponent              = () => import("../../components/admin/systemSetting/Theme/ThemeComponent.vue");
const NotificationAlertComponent  = () => import("../../components/admin/systemSetting/NotificationAlert/NotificationAlertComponent.vue");
const PaymentGatewayComponent     = () => import("../../components/admin/systemSetting/PaymentGateway/PaymentGatewayComponent.vue");
const StorageComponent            = () => import("../../components/admin/systemSetting/Storage/StorageComponent.vue");
const BenefitComponent            = () => import("../../components/admin/systemSetting/Benefit/BenefitComponent.vue");
const BenefitListComponent        = () => import("../../components/admin/systemSetting/Benefit/BenefitListComponent.vue");
const BenefitShowComponent        = () => import("../../components/admin/systemSetting/Benefit/BenefitShowComponent.vue");
const RiderTipComponent           = () => import("../../components/admin/systemSetting/RiderTip/RiderTipComponent.vue");
const AboutStepsComponent         = () => import("../../components/admin/systemSetting/AboutSteps/AboutStepsComponent.vue");
const AboutStepsListComponent     = () => import("../../components/admin/systemSetting/AboutSteps/AboutStepsListComponent.vue");
const AboutStepsShowComponent     = () => import("../../components/admin/systemSetting/AboutSteps/AboutStepsShowComponent.vue");
const DeliverySetupComponent      = () => import("../../components/admin/systemSetting/DeliverySetup/DeliverySetupComponent.vue");
const MailComponent               = () => import("../../components/admin/systemSetting/Mail/MailComponent.vue");
const FrontendComponent           = () => import("../../components/admin/systemSetting/Frontend/FrontendComponent.vue");
const PageComponent               = () => import("../../components/admin/systemSetting/Page/PageComponent.vue");
const PageListComponent           = () => import("../../components/admin/systemSetting/Page/PageListComponent.vue");
const PageShowComponent           = () => import("../../components/admin/systemSetting/Page/PageShowComponent.vue");
const TermsAndConditionsComponent = () => import("../../components/admin/systemSetting/TermsAndConditions/TermsAndConditionsComponent.vue");
const CookiesComponent            = () => import("../../components/admin/systemSetting/Cookies/CookiesComponent.vue");
const AnalyticComponent           = () => import("../../components/admin/systemSetting/Analytics/AnalyticComponent.vue");
const AnalyticListComponent       = () => import("../../components/admin/systemSetting/Analytics/AnalyticListComponent.vue");
const AnalyticShowComponent       = () => import("../../components/admin/systemSetting/Analytics/AnalyticShowComponent.vue");
const RoleComponent               = () => import("../../components/admin/systemSetting/Role/RoleComponent.vue");
const RoleListComponent           = () => import("../../components/admin/systemSetting/Role/RoleListComponent.vue");
const RoleShowComponent           = () => import("../../components/admin/systemSetting/Role/RoleShowComponent.vue");
const PusherComponent             = () => import("../../components/admin/systemSetting/Pusher/PusherComponent.vue");
const PwaComponent                = () => import("../../components/admin/systemSetting/Pwa/PwaComponent.vue");

export default [
    {
        path: "/admin/system-settings",
        component: SystemSettingsComponent,
        name: "admin.systemSettings",
        redirect: { name: "admin.systemSettings.company" },
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "system-settings",
            breadcrumb: "system_settings"
        },
        children: [
            {
                path: "company",
                component: CompanyComponent,
                name: "admin.systemSettings.company",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "company"
                }
            },
            {
                path: "site",
                component: SiteComponent,
                name: "admin.systemSettings.site",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "site"
                }
            },
            {
                path: "frontend",
                component: FrontendComponent,
                name: "admin.systemSettings.frontend",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "frontend"
                }
            },
            {
                path: "terms-and-conditions",
                component: TermsAndConditionsComponent,
                name: "admin.systemSettings.termsAndConditions",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "terms_and_conditions"
                }
            },
            {
                path: "mail",
                component: MailComponent,
                name: "admin.systemSettings.mail",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "mail"
                }
            },
            {
                path: "delivery-setup",
                component: DeliverySetupComponent,
                name: "admin.systemSettings.deliverySetup",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "delivery_setup"
                }
            },
            {
                path: "otp",
                component: OtpComponent,
                name: "admin.systemSettings.otp",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "otp"
                }
            },
            {
                path: "notification",
                component: NotificationComponent,
                name: "admin.systemSettings.notification",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "notification"
                }
            },
            {
                path: "notification-alert",
                component: NotificationAlertComponent,
                name: "admin.systemSettings.notificationAlert",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "notification_alert"
                }
            },
            {
                path: "pusher",
                component: PusherComponent,
                name: "admin.systemSettings.pusher",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "pusher"
                }
            },
            {
                path: "social-media",
                component: SocialMediaComponent,
                name: "admin.systemSettings.socialMedia",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "social_media"
                }
            },
            {
                path: "cookies",
                component: CookiesComponent,
                name: "admin.systemSettings.cookies",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "cookies"
                }
            },
            {
                path: "analytics",
                component: AnalyticComponent,
                name: "admin.systemSettings.analytic",
                redirect: { name: "admin.systemSettings.analytic.list" },
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "analytics"
                },
                children: [
                    {
                        path: "list",
                        component: AnalyticListComponent,
                        name: "admin.systemSettings.analytic.list",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: ""
                        },
                    },
                    {
                        path: "show/:id",
                        component: AnalyticShowComponent,
                        name: "admin.systemSettings.analytic.show",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: "view"
                        },
                    },
                ]
            },
            {
                path: "theme",
                component: ThemeComponent,
                name: "admin.systemSettings.theme",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "theme"
                }
            },
            {
                path: "currencies",
                component: CurrencyComponent,
                name: "admin.systemSettings.currency",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "currencies"
                }
            },
            {
                path: "rider-tips",
                component: RiderTipComponent,
                name: "admin.systemSettings.riderTip",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "rider_tips"
                }
            },
            {
                path: "cuisines",
                redirect: {name: "admin.cuisines.list"},
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "cuisines",
                    breadcrumb: "cuisines"
                }
            },
            {
                path: "cuisines/list",
                redirect: {name: "admin.cuisines.list"}
            },
            {
                path: "cuisines/show/:id",
                redirect: (to) => ({name: "admin.cuisines.show", params: {id: to.params.id}})
            },
            {
                path: "about-steps",
                component: AboutStepsComponent,
                name: "admin.systemSettings.aboutSteps",
                redirect: { name: "admin.systemSettings.aboutSteps.list" },
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "about_steps"
                },
                children: [
                    {
                        path: "list",
                        component: AboutStepsListComponent,
                        name: "admin.systemSettings.aboutSteps.list",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: ""
                        },
                    },
                    {
                        path: "show/:id",
                        component: AboutStepsShowComponent,
                        name: "admin.systemSettings.aboutSteps.show",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: "view"
                        },
                    },
                ],
            },

            {
                path: "benefits",
                component: BenefitComponent,
                name: "admin.systemSettings.benefit",
                redirect: { name: "admin.systemSettings.benefit.list" },
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "benefits"
                },
                children: [
                    {
                        path: "list",
                        component: BenefitListComponent,
                        name: "admin.systemSettings.benefit.list",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: ""
                        },
                    },
                    {
                        path: "show/:id",
                        component: BenefitShowComponent,
                        name: "admin.systemSettings.benefit.show",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: "view"
                        },
                    },
                ],
            },
            {
                path: "pages",
                component: PageComponent,
                name: "admin.systemSettings.page",
                redirect: { name: "admin.systemSettings.page.list" },
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "pages"
                },
                children: [
                    {
                        path: "list",
                        component: PageListComponent,
                        name: "admin.systemSettings.page.list",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: ""
                        },
                    },
                    {
                        path: "show/:id",
                        component: PageShowComponent,
                        name: "admin.systemSettings.page.show",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: "view"
                        },
                    },
                ],
            },
            {
                path: "role",
                component: RoleComponent,
                name: "admin.settings.role",
                redirect: { name: "admin.settings.role.list" },
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "role_permissions"
                },
                children: [
                    {
                        path: "list",
                        component: RoleListComponent,
                        name: "admin.settings.role.list",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: ""
                        },
                    },
                    {
                        path: "show/:id",
                        component: RoleShowComponent,
                        name: "admin.settings.role.show",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: "view"
                        },
                    },
                ],
            },
            {
                path: "languages",
                component: LanguageComponent,
                name: "admin.systemSettings.language",
                redirect: { name: "admin.systemSettings.language.list" },
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "languages"
                },
                children: [
                    {
                        path: "list",
                        component: LanguageListComponent,
                        name: "admin.systemSettings.language.list",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: ""
                        },
                    },
                    {
                        path: "show/:id",
                        component: LanguageShowComponent,
                        name: "admin.systemSettings.language.show",
                        meta: {
                            template: "admin",
                            auth: true,
                            permissionUrl: "system-settings",
                            breadcrumb: "view"
                        },
                    },
                ],
            },
            {
                path: "sms-gateway",
                component: SmsGatewayComponent,
                name: "admin.systemSettings.smsGateway",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "sms_gateway"
                },
            },
            {
                path: "ai-agent",
                component: AiAgentComponent,
                name: "admin.systemSettings.aiAgent",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "ai_agent"
                },
            },
            {
                path: "payment-gateway",
                component: PaymentGatewayComponent,
                name: "admin.systemSettings.paymentGateway",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "payment_gateway"
                },
            },
            {
                path: "storage",
                component: StorageComponent,
                name: "admin.systemSettings.storage",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "storage"
                },
            },
            {
                path: "pwa",
                component: PwaComponent,
                name: "admin.systemSettings.pwa",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "system-settings",
                    breadcrumb: "pwa"
                }
            }
        ]
    }
];
