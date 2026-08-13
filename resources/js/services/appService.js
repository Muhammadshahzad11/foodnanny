import currencyPositionEnum from "../enums/modules/currencyPositionEnum.js";
import statusEnum from "../enums/modules/statusEnum.js";
import {useAuthStore} from "../stores/auth.js";
import askEnum from "../enums/modules/askEnum.js";
import taxTypeEnum from "../enums/modules/taxTypeEnum.js";
import offerStatusEnum from "../enums/modules/offerStatusEnum.js";
import campaignStatusEnum from "../enums/modules/campaignStatusEnum.js";
import orderStatusEnum from "../enums/modules/orderStatusEnum.js";
import roleEnum from "../enums/modules/roleEnum.js";

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
                        route.meta.access = !!perm.access;
                        if (typeof perm.title !== "undefined") {
                            route.meta.title = perm.title;
                        }
                    } else {
                        // No grant for this route in the active context → block (prevents stale access).
                        route.meta.access = false;
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
        const safeAmount = Number(amount);
        const safeDecimal = Number.isFinite(Number(decimal)) ? Number(decimal) : 2;
        const safeCurrency = currency == null || currency === '' ? '' : String(currency);
        const formatted = (Number.isFinite(safeAmount) ? safeAmount : 0).toFixed(safeDecimal);
        if (position === currencyPositionEnum.LEFT) {
            return safeCurrency + formatted;
        }
        return formatted + safeCurrency;
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
    /**
     * Ray-casting point-in-polygon. polygon: [{lat,lng}, ...]
     */
    pointInPolygon: function (lat, lng, polygon) {
        if (!Array.isArray(polygon) || polygon.length < 3) {
            return false;
        }
        let inside = false;
        for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
            const yi = parseFloat(polygon[i].lng ?? polygon[i].longitude);
            const xi = parseFloat(polygon[i].lat ?? polygon[i].latitude);
            const yj = parseFloat(polygon[j].lng ?? polygon[j].longitude);
            const xj = parseFloat(polygon[j].lat ?? polygon[j].latitude);
            const intersect = ((yi > lng) !== (yj > lng))
                && (lat < ((xj - xi) * (lng - yi) / ((yj - yi) || 1e-12) + xi));
            if (intersect) inside = !inside;
        }
        return inside;
    },
    findMatchingDeliveryZone: function (lat, lng, zones) {
        if (!Array.isArray(zones) || !zones.length) {
            return null;
        }
        const sorted = [...zones].sort((a, b) => (a.id || 0) - (b.id || 0));
        for (const zone of sorted) {
            if (this.pointInPolygon(lat, lng, zone.polygon || [])) {
                return zone;
            }
        }
        return null;
    },
    calculateZoneDeliveryFee: function (zone, distanceKm, setting) {
        const additional = parseFloat(zone.additional_delivery_charge ?? 0) || 0;
        let min = zone.min_delivery_charge != null && zone.min_delivery_charge !== ''
            ? parseFloat(zone.min_delivery_charge) : null;
        let max = zone.max_delivery_charge != null && zone.max_delivery_charge !== ''
            ? parseFloat(zone.max_delivery_charge) : null;
        let perKm = zone.charge_per_km != null && zone.charge_per_km !== ''
            ? parseFloat(zone.charge_per_km) : null;

        const freeKm = parseFloat(setting.delivery_setup_free_delivery_kilometer || 0);
        const basic = parseFloat(setting.delivery_setup_basic_delivery_fee || 0);
        const globalPerKm = parseFloat(setting.delivery_setup_charge_per_kilo || 0);

        if (perKm === null) perKm = globalPerKm;
        if (min === null) min = basic;

        const chargeType = parseInt(zone.charge_type, 10);
        let fee;
        if (chargeType === 5) {
            fee = min + additional;
        } else {
            if (distanceKm > freeKm) {
                fee = ((distanceKm - freeKm) * perKm) + min + additional;
            } else {
                fee = min + additional;
            }
            if (chargeType === 15 || max !== null) {
                if (min !== null) fee = Math.max(fee, min + additional);
                if (max !== null) fee = Math.min(fee, max);
            }
        }
        return Math.max(0, fee);
    },
    calculatePlatformZoneFee: function (zone, distanceKm, subtotal) {
        if (!zone) {
            return 0;
        }
        const minOrder = parseFloat(zone.min_order_amount || 0);
        if (minOrder > 0 && parseFloat(subtotal || 0) < minOrder) {
            return null;
        }
        const freeAbove = parseFloat(zone.free_delivery_above || 0);
        if (freeAbove > 0 && parseFloat(subtotal || 0) >= freeAbove) {
            return 0;
        }
        let fee = parseFloat(zone.base_delivery_fee || 0);
        const freeKm = parseFloat(zone.free_delivery_km || 0);
        const perKm = parseFloat(zone.extra_distance_charge || 0);
        if (distanceKm > freeKm && perKm > 0) {
            fee += (distanceKm - freeKm) * perKm;
        }
        if (parseInt(zone.peak_enabled, 10) === 1) {
            fee += parseFloat(zone.peak_charge || 0);
        }
        return Math.max(0, fee);
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
     * Staff roles go to their admin workspace (waiters land on tables).
     */
    redirectAfterAuth: async function (router, options = {}) {
        const authStore = useAuthStore();
        const carts = options.carts || [];
        const location = options.location;
        const dineInContext = options.dineInContext || null;
        const redirect = router.currentRoute.value?.query?.redirect;
        const roleId = Number(authStore.info?.role_id || 0);

        if (typeof redirect === 'string' && redirect.startsWith('/') && !redirect.startsWith('//')) {
            return router.push(redirect);
        }

        if (roleId && roleId !== roleEnum.CUSTOMER) {
            if (roleId === roleEnum.WAITER) {
                return router.push({name: 'admin.waiter.tables'});
            }

            const defaultPermission = authStore.defaultPermission;
            if (defaultPermission?.url) {
                return router.push({path: '/admin/' + defaultPermission.url});
            }
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
