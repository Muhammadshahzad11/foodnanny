const OfferComponent     = () => import("../../components/admin/offers/OfferComponent.vue");
const OfferListComponent = () => import("../../components/admin/offers/OfferListComponent.vue");
const OfferShowComponent = () => import("../../components/admin/offers/OfferShowComponent.vue");

export default [
    {
        path: '/admin/offers',
        component: OfferComponent,
        name: 'admin.offers',
        redirect: {name: 'admin.offers.list'},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'offers',
            breadcrumb: 'offers'
        },
        children: [
            {
                path: '',
                component: OfferListComponent,
                name: 'admin.offers.list',
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: 'offers',
                    breadcrumb: ''
                },
            },
            {
                path: "show/:id",
                component: OfferShowComponent,
                name: "admin.offer.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "offers",
                    breadcrumb: "view"
                },
            },
        ]
    }
]
