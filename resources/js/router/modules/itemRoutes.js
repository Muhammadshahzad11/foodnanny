const ItemComponent     = () => import("../../components/admin/items/ItemComponent.vue");
const ItemListComponent = () => import("../../components/admin/items/ItemListComponent.vue");
const ItemShowComponent = () => import("../../components/admin/items/ItemShowComponent.vue");

export default [
    {
        path: '/admin/items',
        component: ItemComponent,
        name: 'admin.items',
        redirect: {name: 'admin.items.list'},
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'items',
            breadcrumb: 'items'
        },
        children: [
            {
                path: '',
                component: ItemListComponent,
                name: 'admin.items.list',
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: 'items',
                    breadcrumb: ''
                },
            },
            {
                path: "show/:id",
                component: ItemShowComponent,
                name: "admin.item.show",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "items",
                    breadcrumb: "view"
                },
            }
        ]
    }
]
