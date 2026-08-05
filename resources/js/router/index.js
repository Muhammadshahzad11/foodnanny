import {createRouter, createWebHistory} from "vue-router";
import frontendRoutes from "./modules/frontendRoutes.js";
import authRoutes from "./modules/authRoutes.js";
import appService from "../services/appService.js";
import {useAuthStore} from "../stores/auth.js";
import NotFoundComponent from "../components/common/NotFoundComponent.vue";
import ExceptionComponent from "../components/common/ExceptionComponent.vue";
import DashboardComponent from "../components/admin/dashboard/DashboardComponent.vue";
import profileRoutes from "./modules/profileRoutes.js";
import systemSettingRoutes from "./modules/systemSettingRoutes.js";
import administratorRoutes from "./modules/administratorRoutes.js";
import deliveryBoyRoutes from "./modules/deliveryBoyRoutes.js"
import restaurantSettingRoutes from "./modules/restaurantSettingRoutes.js"
import couponRoutes from "./modules/couponRoutes.js"
import employeeRoutes from "./modules/employeeRoutes.js";
import tableRoutes from "./modules/tableRoutes.js";
import waiterRoutes from "./modules/waiterRoutes.js";
import kitchenRoutes from "./modules/kitchenRoutes.js";
import customerRoutes from "./modules/customerRoutes.js";
import restaurantOwnerRoutes from "./modules/restaurantOwnerRoutes.js";
import restaurantRoutes from "./modules/restaurantRoutes.js";
import cuisineRoutes from "./modules/cuisineRoutes.js";
import offerRoutes from "./modules/offerRoutes.js";
import campaignAndOfferRoutes from "./modules/campaignAndOfferRoutes.js";
import payoutRoutes from "./modules/payoutRoutes.js";
import transactionRoutes from "./modules/transactionRoutes.js";
import collectionRoutes from "./modules/collectionRoutes.js";
import collectionReportRoutes from "./modules/collectionReportRoutes.js";
import creditBalanceReportRoutes from "./modules/creditBalanceReportRoutes.js";
import subscriberRoutes from "./modules/subscriberRoutes.js";
import messageRoutes from "./modules/messageRoutes.js";
import pushNotificationRoutes from "./modules/pushNotificationRoutes.js";
import itemRoutes from "./modules/itemRoutes.js";
import campaignRoutes from "./modules/campaignRoutes.js";
import voucherRoutes from "./modules/voucherRoutes.js";
import onlineOrderRoutes from "./modules/onlineOrderRoutes.js";
import posOrderRoutes from "./modules/posOrderRoutes.js";
import posRoutes from "./modules/posRoutes.js";
import deliveryBoySettingRoutes from "./modules/deliveryBoySettingRoutes.js";
import activeOrderRoutes from "./modules/activeOrderRoutes.js";
import availableOrderRoutes from "./modules/availableOrderRoutes.js";
import itemsReportRoutes from "./modules/itemsReportRoutes.js";
import salesReportRoutes from "./modules/salesReportRoutes.js";
import returnOrdersRoutes from "./modules/returnOrdersRoutes.js";
import reviewRoutes from "./modules/reviewRoutes.js";
import refundRoutes from "./modules/refundRoutes.js";
import cashoutRoutes from "./modules/cashoutRoutes.js";
import orderTrackerRoutes from "./modules/orderTrackerRoutes.js"

const baseRoutes = [
    {
        path: "/",
        redirect: {name: "frontend.home"},
        name: "root"
    },
    {
        path: "/:pathMatch(.*)*",
        name: "route.notFound",
        component: NotFoundComponent,
        meta: {
            template: "frontend",
        },
    },
    {
        path: "/404",
        name: "route.404",
        component: NotFoundComponent,
        meta: {
            template: "frontend",
        },
    },
    {
        path: "/exception",
        name: "route.exception",
        component: ExceptionComponent,
        meta: {
            template: "admin"
        }
    },
    {
        path: "/admin/dashboard",
        component: DashboardComponent,
        name: "admin.dashboard",
        meta: {
            template: "admin",
            auth: true,
            permissionUrl: "dashboard",
            breadcrumb: "dashboard"
        },
    }
];

const routes = baseRoutes.concat(
    frontendRoutes,
    authRoutes,
    profileRoutes,
    systemSettingRoutes,
    administratorRoutes,
    deliveryBoyRoutes,
    restaurantSettingRoutes,
    couponRoutes,
    employeeRoutes,
    tableRoutes,
    waiterRoutes,
    kitchenRoutes,
    customerRoutes,
    restaurantOwnerRoutes,
    restaurantRoutes,
    cuisineRoutes,
    offerRoutes,
    campaignAndOfferRoutes,
    payoutRoutes,
    transactionRoutes,
    collectionRoutes,
    collectionReportRoutes,
    creditBalanceReportRoutes,
    subscriberRoutes,
    messageRoutes,
    pushNotificationRoutes,
    itemRoutes,
    campaignRoutes,
    voucherRoutes,
    onlineOrderRoutes,
    posOrderRoutes,
    posRoutes,
    deliveryBoySettingRoutes,
    activeOrderRoutes,
    availableOrderRoutes,
    itemsReportRoutes,
    salesReportRoutes,
    returnOrdersRoutes,
    reviewRoutes,
    refundRoutes,
    cashoutRoutes,
    orderTrackerRoutes
);

appService.recursiveRouter(routes, localStorage.getItem('auth') ? JSON.parse(localStorage.getItem('auth'))?.permission : [])
const router = createRouter({
    linkActiveClass: "active",
    mode: 'history',
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return {left: 0, top: 0}
    }
});

router.beforeEach((to, from, next) => {
        const authStore = useAuthStore();
        if (to.meta.auth) {
            if (!authStore.status) {
                next({
                    name: "auth.login",
                    query: {redirect: to.fullPath},
                });
            } else {
                if (to.meta.template === 'admin') {
                    if (typeof to.meta.access !== "undefined" && to.meta.access) {
                        next();
                    } else {
                        next({
                            name: "route.exception",
                        });
                    }
                } else {
                    next();
                }
            }
        } else if (to.name === "auth.login" && authStore.status) {
            const redirect = to.query?.redirect;
            if (typeof redirect === 'string' && redirect.startsWith('/') && !redirect.startsWith('//')) {
                next(redirect);
            } else {
                next({name: "frontend.home"});
            }
        } else {
            next();
        }
    }
)
export default router;
