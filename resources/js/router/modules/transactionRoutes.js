const TransactionListComponent = () => import("../../components/admin/transactions/TransactionListComponent.vue");

export default [
    {
        path: '/admin/transactions',
        component: TransactionListComponent,
        name: 'admin.transactions.list',
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: 'transactions',
            breadcrumb: 'transactions'
        }
    }
]
