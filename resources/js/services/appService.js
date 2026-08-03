import currencyPositionEnum from "../enums/modules/currencyPositionEnum.js";
import statusEnum from "../enums/modules/statusEnum.js";
import {useAuthStore} from "../stores/auth.js";
import askEnum from "../enums/modules/askEnum.js";
import taxTypeEnum from "../enums/modules/taxTypeEnum.js";
import offerStatusEnum from "../enums/modules/offerStatusEnum.js";
import campaignStatusEnum from "../enums/modules/campaignStatusEnum.js";
import orderStatusEnum from "../enums/modules/orderStatusEnum.js";

export default {
    phoneNumber: function (e) {
        let char = String.fromCharCode(e.keyCode);
        if (/^[+]?[0-9]*$/.test(char)) return true;
        else e.preventDefault();
    },
    textShortener: function (text, number = 30) {
        if (text) {
            if (!(text.length < number)) {
                return text.substring(0, number) + "..";
            }
        }
        return text;
    },
    statusClass: function (status) {
        if (status === statusEnum.ACTIVE) {
            return "db-table-badge text-green-600 bg-green-100";
        } else {
            return "db-table-badge text-red-600 bg-red-100";
        }
    },
    requestHandler: function (requests) {
        let i        = 1;
        let what     = "?";
        let response = "";
        for (let request in requests) {
            if (requests[request] !== "" && requests[request] !== null) {
                if (i !== 1) {
                    response += "&";
                }
                response += request + "=" + requests[request];
            }
            i++;
        }
        if (response) {
            response = what + response;
        }
        return response;
    },
    askClass: function (ask) {
        if (ask === askEnum.YES) {
            return "db-table-badge text-green-600 bg-green-100";
        } else {
            return "db-table-badge text-red-600 bg-red-100";
        }
    },
    handlePaper(event) {
        const currGroup  = event.currentTarget.parentElement
        const isActivate = currGroup.className.includes('active')
        if (!isActivate) currGroup.classList.add('active')
        else currGroup.classList.remove('active')
    },
    onlyNumber: function (e) {
        let res = (e.charCode !== 8 && e.charCode === 0 || (e.charCode >= 48 && e.charCode <= 57));
        if (res)
            return true;
        else
            e.preventDefault();
    },
    floatNumber: function (e) {
        let char = String.fromCharCode(e.keyCode);
        if (/^[.]?[0-9]*$/.test(char)) return true;
        else e.preventDefault();
    },
    permissionChecker: function (permissionName) {
        const authStore    = useAuthStore();
        let i, permissions = authStore.permission;

        for (i = 0; i < permissions.length; i++) {
            if (typeof permissions[i].name !== "undefined" && permissions[i].name) {
                if (permissions[i].name === permissionName) {
                    return permissions[i].access;
                }
            }
        }
    },
    recursiveRouter: function (routes, permission) {
        if (!routes || !Array.isArray(routes) || routes.length === 0) {
            return;
        }

        if (!permission || !Array.isArray(permission) || permission.length === 0) {
            return;
        }

        try {
            const routesToUpdate = [];

            const collectRoutes = (routeArray) => {
                if (!Array.isArray(routeArray)) return;

                routeArray.forEach((route) => {
                    if (route && typeof route === "object") {
                        if (route.meta?.permissionUrl && typeof route.meta.permissionUrl === "string") {
                            routesToUpdate.push(route);
                        }

                        if (route.children && Array.isArray(route.children) && route.children.length > 0) {
                            collectRoutes(route.children);
                        }
                    }
                });
            };

            collectRoutes(routes);

            if (routesToUpdate.length === 0) {
                return;
            }

            const permissionMap = new Map();
            permission.forEach((perm) => {
                if (perm && typeof perm === "object" && perm.url && typeof perm.url === "string") {
                    permissionMap.set(perm.url, perm);
                }
            });

            if (permissionMap.size === 0) {
                return;
            }

            routesToUpdate.forEach((route) => {
                try {
                    const perm = permissionMap.get(route.meta.permissionUrl);
                    if (perm) {
                        if (typeof perm.access !== "undefined") {
                            route.meta.access = perm.access;
                        }
                        if (typeof perm.title !== "undefined") {
                            route.meta.title = perm.title;
                        }
                    }
                } catch (routeError) {}
            });
        } catch (error) {}
    },
    sideDrawerShow: function (id = 'sideDrawer') {
        const drawerDivs = document?.querySelectorAll(".drawer");
        const drawerSets = document?.querySelectorAll("[data-drawer]");
        drawerSets?.forEach((drawerSet) => {
            const targetElm = document?.querySelector(drawerSet?.dataset?.drawer);
            drawerSets?.forEach(drawerBtn => drawerBtn?.classList?.remove("active"));
            drawerDivs?.forEach(drawerDiv => drawerDiv?.classList?.remove("active"));
            targetElm?.classList?.add("active");
            drawerSet?.classList?.add("active");
            document.body.style.overflowY = "hidden";
            document?.querySelector(".backdrop")?.classList?.add("active");
        });
    },
    taxTypeClass: function (type) {
        if (type === taxTypeEnum.FIXED) {
            return "db-table-badge text-blue-500 bg-blue-100";
        } else {
            return "db-table-badge text-orange-500 bg-orange-100";
        }
    },
    orderStatusClass: function (status) {
        if (status === orderStatusEnum.ACCEPT || status === orderStatusEnum.PREPARING || status === orderStatusEnum.PREPARED) {
            return "text-[10px] leading-none capitalize px-2 py-1 rounded-md text-[#00a63e] bg-[#dcfce7]";
        } else if (status === orderStatusEnum.PENDING) {
            return "text-[10px] leading-none capitalize px-2 py-1 rounded-md text-[#F6A609] bg-[#FFEEC6]";
        } else if (status === orderStatusEnum.OUT_FOR_DELIVERY) {
            return "text-[10px] leading-none capitalize px-2 py-1 rounded-md text-[#008BBA] bg-[#BDEFFF]";
        } else if (status === orderStatusEnum.DELIVERED) {
            return "text-[10px] leading-none capitalize px-2 py-1 rounded-md text-primary bg-orange-100";
        } else {
            return "text-[10px] leading-none capitalize px-2 py-1 rounded-md text-[#FB4E4E] bg-[#FFDADA]";
        }
    },
    orderStatusClassForTable: function (status) {
        if (status === orderStatusEnum.ACCEPT || status === orderStatusEnum.PREPARING || status === orderStatusEnum.PREPARED) {
            return "db-table-badge text-[#2AC769] bg-[#CBFFE0]";
        } else if (status === orderStatusEnum.PENDING) {
            return "db-table-badge text-[#F6A609] bg-[#FFEEC6]";
        } else if (status === orderStatusEnum.OUT_FOR_DELIVERY) {
            return "db-table-badge text-[#008BBA] bg-[#BDEFFF]";
        } else if (status === orderStatusEnum.DELIVERED) {
            return "db-table-badge text-primary bg-orange-100";
        } else {
            return "db-table-badge text-[#FB4E4E] bg-[#FFDADA]";
        }
    },
    currencyFormat(amount, decimal, currency, position) {
        if (position === currencyPositionEnum.LEFT) {
            return currency + parseFloat(amount).toFixed(decimal);
        } else {
            return parseFloat(amount).toFixed(decimal) + currency;
        }
    },
    distance: function (lat1, lng1, lat2, lng2) {
        let radiationLat1  = Math.PI * lat1 / 180
        let radiationLat2  = Math.PI * lat2 / 180
        let theta          = lng1 - lng2;
        let radiationTheta = Math.PI * theta / 180
        let distance       = Math.sin(radiationLat1) * Math.sin(radiationLat2) + Math.cos(radiationLat1) * Math.cos(radiationLat2) * Math.cos(radiationTheta);
        distance           = Math.acos(distance)
        distance           = distance * 180 / Math.PI
        distance           = distance * 60 * 1.1515
        distance           = distance * 1.609344
        return distance;
    },
    campaignStatusClass: function (status) {
        if (status === campaignStatusEnum.PENDING) {
            return "db-table-badge text-orange-500 bg-orange-100";
        } else if (status === campaignStatusEnum.APPROVE) {
            return "db-table-badge text-green-600 bg-green-100";
        } else {
            return "db-table-badge text-red-600 bg-red-100";
        }
    },
    offerStatusClass: function (status) {
        if (status === offerStatusEnum.PENDING) {
            return "db-table-badge text-orange-500 bg-orange-100";
        } else if (status === offerStatusEnum.APPROVE) {
            return "db-table-badge text-green-600 bg-green-100";
        } else {
            return "db-table-badge text-red-600 bg-red-100";
        }
    },
    /**
     * After login/guest auth, return user to intended page (checkout, restaurant, etc.).
     */
    redirectAfterAuth: async function (router, options = {}) {
        const carts = options.carts || [];
        const location = options.location;
        const dineInContext = options.dineInContext || null;
        const redirect = router.currentRoute.value?.query?.redirect;

        if (typeof redirect === 'string' && redirect.startsWith('/') && !redirect.startsWith('//')) {
            return router.push(redirect);
        }

        if (carts.length > 0) {
            return router.push({name: 'frontend.checkout'});
        }

        if (dineInContext?.isActive && dineInContext.context?.restaurant_slug) {
            return router.push({
                name: 'frontend.singleRestaurant',
                params: {slug: dineInContext.context.restaurant_slug},
            });
        }

        if (location) {
            return router.push({name: 'frontend.restaurant'});
        }

        return router.push({name: 'frontend.home'});
    }
}
