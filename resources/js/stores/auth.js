import axios from 'axios';
import { defineStore } from "pinia";


export const useAuthStore = defineStore("auth", {
    persist: true,
    state: () => ({
        status: false,
        token: null,
        info: {},
        adminMenu: [],
        restaurantMenu: [],
        adminPermission: [],
        restaurantPermission: [],
        permission: [],
        defaultPermission: {},
        adminDefaultPermission: {},
        restaurantDefaultPermission: {},
        resetInfo: {
            email: null,
            token: null,
            verify: false
        },
        phoneLoginInfo: {
            code: null,
            phone: null,
        }
    }),
    actions: {
        profile: function () {
            return new Promise((resolve, reject) => {
                axios.get('/profile').then(res => {
                    this.info = res.data.data;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        applyLoginPayload: function (data) {
            this.status = true;
            this.token = data.token;
            this.info = data.user;
            this.adminMenu = data.admin_menu;
            this.restaurantMenu = data.restaurant_menu;
            this.adminPermission = data.admin_permission;
            this.restaurantPermission = data.restaurant_permission;
            this.permission = data.permission;
            this.defaultPermission = Object.keys(data.admin_default_permission || {}).length > 0
                ? data.admin_default_permission
                : data.restaurant_default_permission;
            this.adminDefaultPermission = data.admin_default_permission;
            this.restaurantDefaultPermission = data.restaurant_default_permission;
            this.clearPhoneLoginInfo();
        },
        login: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/login', payload).then((res) => {
                    this.applyLoginPayload(res.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        setPhoneLoginInfo: function (payload) {
            this.phoneLoginInfo = {
                code: payload?.code || null,
                phone: payload?.phone || null,
            };
        },
        clearPhoneLoginInfo: function () {
            this.phoneLoginInfo = {code: null, phone: null};
        },
        sendPhoneLoginOtp: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/login-phone/otp', payload).then((res) => {
                    if (res.data?.token) {
                        this.applyLoginPayload(res.data);
                    } else {
                        this.setPhoneLoginInfo(payload);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        phoneLogin: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/login-phone', payload).then((res) => {
                    this.applyLoginPayload(res.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        logout: function () {
            return new Promise((resolve, reject) => {
                axios.post('auth/logout').then((res) => {
                    this.status = false;
                    this.token = null;
                    this.info = {};
                    this.adminMenu = [];
                    this.restaurantMenu = [];
                    this.adminPermission = [];
                    this.restaurantPermission = [];
                    this.permission = [];
                    this.defaultPermission = {};
                    this.adminDefaultPermission = {};
                    this.restaurantDefaultPermission = {};
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        isAuth: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/is-auth', payload).then((res) => {
                    if (res.data.status === false) {
                        this.status = false;
                        this.token = null;
                        this.info = {};
                        this.adminMenu = [];
                        this.restaurantMenu = [];
                        this.adminPermission = [];
                        this.restaurantPermission = [];
                        this.permission = [];
                        this.defaultPermission = {};
                        this.adminDefaultPermission = {};
                        this.restaurantDefaultPermission = {};
                    }
                    resolve(res)
                }).catch((err) => {
                    reject(err)
                })
            })
        },
        permissionSwitch: function () {
            return new Promise((resolve, reject) => {
                axios.post('admin/permission-switch').then((res) => {
                    this.permission = res.data.data;
                    this.refreshMenus().then(() => resolve(res)).catch(reject);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        forgotPassword: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/forgot-password', payload).then(res => {
                    this.resetInfo.email = payload.email;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        verifyCode: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/forgot-password/verify-code', payload).then((res) => {
                    this.resetInfo.token = payload.code;
                    this.resetInfo.verify = true;
                    resolve(res);
                }).catch((err) => {
                    this.resetInfo.token = null;
                    this.resetInfo.verify = false;
                    reject(err);
                });
            });
        },
        resetPassword: function (payload) {
            return new Promise((resolve, reject) => {
                axios.post('auth/forgot-password/reset-password', payload).then((res) => {
                    this.resetInfo.email = null;
                    this.resetInfo.token = null;
                    this.resetInfo.verify = false;
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        updateAuthInfo: function (payload) {
            return new Promise((resolve, reject) => {
                if (this.info.id === payload.id) {
                    this.info = payload;
                    resolve(payload);
                } else {
                    reject('user data not match');
                }
            });
        },
        refreshMenus: function () {
            return new Promise((resolve, reject) => {
                axios.get('auth/menus').then((res) => {
                    this.adminMenu = res.data.admin_menu;
                    this.restaurantMenu = res.data.restaurant_menu;
                    if (res.data.permission) {
                        this.permission = res.data.permission;
                    }
                    if (res.data.admin_permission) {
                        this.adminPermission = res.data.admin_permission;
                    }
                    if (res.data.restaurant_permission) {
                        this.restaurantPermission = res.data.restaurant_permission;
                    }
                    if (res.data.admin_default_permission) {
                        this.adminDefaultPermission = res.data.admin_default_permission;
                    }
                    if (res.data.restaurant_default_permission) {
                        this.restaurantDefaultPermission = res.data.restaurant_default_permission;
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    }
})
