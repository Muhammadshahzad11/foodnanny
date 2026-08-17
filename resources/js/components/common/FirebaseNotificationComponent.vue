<template>
    <div id="notification-modal" :class="notificationStatus ? 'modal-active overflow-hidden' : ''"
         class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="max-w-sm w-full rounded-xl p-6 mx-auto bg-white transition-all duration-300 relative">
            <div class="flex gap-1 justify-between">
                <h3 class="modal-title">{{ $t("label.notification") }}</h3>
                <button class="" @click="closeNotificationModal">
                    <i class="lab-line-circle-cross text-lg text-danger"></i>
                </button>
            </div>

            <h3 class="text-md font-normal leading-8 mb-6 mt-4">
                {{ notificationMessage }}
            </h3>

            <router-link @click="closeNotificationModal($event)" v-if="notificationUrl" :to="{ path: notificationUrl }" class="db-btn h-[38px] shadow-[0px_6px_10px_rgb(var(--primary)/0.24)] bg-primary text-white">
                {{ $t('button.let_me_check') }}
            </router-link>
        </div>
    </div>
</template>
<script>
import {initializeApp} from "firebase/app";
import {getMessaging, getToken, onMessage} from "firebase/messaging";
import {useFrontendSettingStore} from "../../stores/frontendSetting.js";
import {useAuthStore} from "../../stores/auth.js";
import axios from "axios";
import orderStatusEnum from "../../enums/modules/orderStatusEnum.js";
import {useRoute} from "vue-router";
import {playNotificationSound, playOrderAlertSound} from "../../services/notificationSound.js";

export default {
    name: "FirebaseNotificationComponent",
    setup() {
        const route                = useRoute();
        const authStore            = useAuthStore();
        const frontendSettingStore = useFrontendSettingStore();

        return {
            route,
            authStore,
            frontendSettingStore
        }
    },
    data() {
        return {
            notificationMessage: "",
            notificationUrl: "/my-orders",
            notificationStatus: false
        }
    },
    computed: {
        logged: function () {
            return this.authStore.status;
        }
    },
    mounted() {
        window.setTimeout(() => {
            const frontendNotification = this.frontendSettingStore.lists;
            if (this.logged && frontendNotification.notification_fcm_api_key && frontendNotification.notification_fcm_auth_domain && frontendNotification.notification_fcm_project_id && frontendNotification.notification_fcm_storage_bucket && frontendNotification.notification_fcm_messaging_sender_id && frontendNotification.notification_fcm_app_id && frontendNotification.notification_fcm_measurement_id) {
                initializeApp({
                    apiKey: frontendNotification.notification_fcm_api_key,
                    authDomain: frontendNotification.notification_fcm_auth_domain,
                    projectId: frontendNotification.notification_fcm_project_id,
                    storageBucket: frontendNotification.notification_fcm_storage_bucket,
                    messagingSenderId: frontendNotification.notification_fcm_messaging_sender_id,
                    appId: frontendNotification.notification_fcm_app_id,
                    measurementId: frontendNotification.notification_fcm_measurement_id
                });

                const messaging = getMessaging();
                Notification.requestPermission().then((permission) => {
                    if (permission === 'granted') {
                        getToken(messaging, {vapidKey: frontendNotification.notification_fcm_public_vapid_key}).then((currentToken) => {
                            if (currentToken) {
                                axios.post('/frontend/device-token/web', {token: currentToken}).then().catch((error) => {
                                    if (error.response.data.message === 'Unauthenticated.') {
                                        this.authStore.logout();
                                    }
                                });
                            }
                        }).catch(err => {
                        });
                    }
                }).catch(err => {
                });

                onMessage(messaging, (payload) => {
                    const notificationTitle   = payload.notification.title;
                    const notificationOptions = {
                        body: payload.notification.body,
                        icon: '/images/required/firebase-logo.png'
                    };
                    new Notification(notificationTitle, notificationOptions);
                    const orderStatusArray = [
                        orderStatusEnum.ACCEPT,
                        orderStatusEnum.PREPARING,
                        orderStatusEnum.PREPARED,
                        orderStatusEnum.OUT_FOR_DELIVERY,
                        orderStatusEnum.DELIVERED,
                        orderStatusEnum.REJECTED,
                        orderStatusEnum.RETURNED
                    ];

                    if ((payload.data.topic_name === 'regular-order' && orderStatusArray.includes(parseInt(payload.data.order_status))) || (payload.data.topic_name === 'new-order' || payload.data.topic_name === 'delivery-boy-order')) {
                        this.notificationStatus  = true;
                        this.notificationUrl     = payload.data.url;
                        this.notificationMessage = payload.notification.body;
                        if (payload.data.topic_name === 'new-order') {
                            playOrderAlertSound();
                        } else {
                            playNotificationSound({volume: 0.8});
                        }
                    }
                });
            }
        }, 3000)
    },
    methods: {
        closeNotificationModal: function () {
            if (this.notificationUrl.trim() === this.route.fullPath.trim()) {
                window.location.reload();
            }
            this.notificationUrl     = "";
            this.notificationMessage = "";
            this.notificationStatus  = false;
        },
    }
}
</script>
