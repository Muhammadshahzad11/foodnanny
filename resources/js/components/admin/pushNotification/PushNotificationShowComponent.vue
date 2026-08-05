<template>
    <LoadingComponent :props="loading" />
    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t('menu.push_notifications') }}</h3>
        </div>
        <div class="db-card-body">
            <div class="row">
                <div v-if="pushNotification.image" class="col-12 sm:col-4">
                    <img class="db-image" alt="push_notification" :src="pushNotification.image">
                </div>
                <div class="col-12 sm:col-8" :class="pushNotification.image ? 'md:pl-8' : ''">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">{{ pushNotification.title }}</h3>
                    <label class="db-badge mb-3 db-table-badge text-green-600 bg-green-100">
                        {{ pushNotification.customer }}
                    </label>
                    <p class="db-light-text" v-html="pushNotification.description"></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../common/LoadingComponent.vue";
import { usePushNotificationStore } from "../../../stores/pushNotification.js";

export default {
    name: "PushNotificationShowComponent",
    components: {
        LoadingComponent
    },
    setup() {
        const pushNotificationStore = usePushNotificationStore();
        return {
            pushNotificationStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            }
        }
    },
    computed: {
        pushNotification: function () {
            return this.pushNotificationStore.show;
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.pushNotificationStore.view(this.$route.params.id).then(res => {
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    }
}
</script>
