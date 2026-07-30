const CampaignAndOfferComponent     = () => import("../../components/admin/campaignsAndOffers/CampaignAndOfferComponent.vue");
const CampaignAndOfferListComponent = () => import("../../components/admin/campaignsAndOffers/CampaignAndOfferListComponent.vue");
const CampaignShowComponent         = () => import("../../components/admin/campaignsAndOffers/CampaignShowComponent.vue");
const OfferShowComponent            = () => import("../../components/admin/campaignsAndOffers/OfferShowComponent.vue");

export default [
    {
        path: '/admin/campaigns-and-offers',
        component: CampaignAndOfferComponent,
        name: 'admin.campaignsAndOffers',
        redirect: {name: 'admin.campaignsAndOffers.list'},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'campaigns-and-offers',
            breadcrumb: 'campaigns_and_offers'
        },
        children: [
            {
                path: '',
                component: CampaignAndOfferListComponent,
                name: 'admin.campaignsAndOffers.list',
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: 'campaigns-and-offers',
                    breadcrumb: ''
                },
            },
            {
                path: "show/campaign/:id",
                component: CampaignShowComponent,
                name: "admin.campaignsAndOffers.showCampaign",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "campaigns-and-offers",
                    breadcrumb: "view"
                },
            },
            {
                path: "show/offer/:id",
                component: OfferShowComponent,
                name: "admin.campaignsAndOffers.showOffer",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "campaigns-and-offers",
                    breadcrumb: "view"
                },
            },
        ]
    }
]
