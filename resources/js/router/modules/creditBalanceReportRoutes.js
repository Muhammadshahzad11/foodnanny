const CreditBalanceReportComponent = () => import("../../components/admin/creditBalanceReport/CreditBalanceReportComponent.vue");

export default [
    {
        path: "/admin/credit-balance-report",
        component: CreditBalanceReportComponent,
        name: "admin.creditBalanceReport",
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "credit-balance-report",
            breadcrumb: "credit_balance_report"
        }
    }
];
