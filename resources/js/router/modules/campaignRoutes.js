const CampaignComponent     = () => import("../../components/admin/campaigns/CampaignComponent.vue");
const CampaignListComponent = () => import("../../components/admin/campaigns/CampaignListComponent.vue");
const CampaignShowComponent = () => import("../../components/admin/campaigns/CampaignShowComponent.vue");

export default [
    {
        path: '/admin/campaigns',
        component: CampaignComponent,
        name: 'admin.campaigns',
        redirect: {name: 'admin.campaigns.list'},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'campaigns',
            breadcrumb: 'campaigns'
        },
        children: [
            {
                path: '',
                component: CampaignListComponent,
                name: 'admin.campaigns.list',
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: 'campaigns',
                    breadcrumb: ''
                },
            },
            {
                path: "show/:id",
                component: CampaignShowComponent,
                name: "admin.campaign.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "campaigns",
                    breadcrumb: "view"
                },
            },
        ]
    }
]
