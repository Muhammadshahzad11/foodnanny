<template>
    <div class="row">
        <div class="col-12">
            <div class="flex flex-col sm:flex-row items-start rounded-lg bg-white">
                <div :class="isMessageOpen ? 'mobile:translate-x-0' : 'mobile:-translate-x-full'"
                     class="w-60 md:w-72 flex-shrink-0 mobile:fixed mobile:bottom-0 mobile:left-0 mobile:top-0 mobile:z-50 mobile:h-dvh mobile:shadow-paper mobile:bg-white transition-all duration-300">
                    <div class="h-16 py-4 px-4 flex items-center justify-between gap-2 border-b border-gray-100">
                        <h3 class="text-xl font-semibold capitalize">Messages</h3>
                        <button @click="isMessageOpen = false" type="button"
                                class="w-5 aspect-square rounded-full flex sm:hidden items-center justify-center bg-danger">
                            <i class="lab-line-cross text-sm text-white"></i>
                        </button>
                    </div>
                    <ul class="h-[calc(100dvh-65px)] sm:h-[calc(100dvh-160px)] px-2 py-2 overflow-y-auto thin-scrolling db-message-list">
                        <li @click="showMessage(user)" v-for="user in users"
                            :class="user.user_id === selectedUser?.user_id ? 'active' : ''"
                            class="p-2 flex items-center gap-3 rounded-lg transition cursor-pointer hover:bg-gray-100">
                            <img class="flex-shrink-0 w-9 h-9 object-cover rounded" :src="user.image" alt="avatar">
                            <dl class="flex-auto">
                                <dt class="flex gap-1 mb-0.5">
                                    <h4 class="text-sm font-medium capitalize">{{ user.name }}</h4>
                                </dt>
                                <dt class="text-xs text-paragraph">{{ $t('label.order_id') }}:
                                    <span class="text-[#374151]">#{{ user.order_serial_no }}</span>
                                </dt>
                            </dl>
                        </li>
                    </ul>
                </div>

                <div class="w-full flex-auto ltr:sm:border-l rtl:sm:border-r border-gray-100">
                    <div class="h-16 px-4 flex items-center gap-3 border-t sm:border-t-0 border-b border-gray-100">
                        <img v-if="Object.keys(selectedUser).length > 0"
                             class="flex-shrink-0 w-9 h-9 object-cover rounded" :src="selectedUser?.image" alt="avatar">
                        <dl v-if="Object.keys(selectedUser).length > 0" class="flex-auto">
                            <dt class="text-sm font-medium capitalize mb-1">{{ selectedUser?.name }}</dt>
                            <dt class="text-xs text-paragraph">
                                {{ $t('label.order_id') }}:
                                <span class="text-[#374151]">#{{ selectedUser?.order_serial_no }}</span>
                            </dt>
                        </dl>
                        <button v-if="!isMessageOpen" @click="isMessageOpen = true" type="button"
                                class="block sm:hidden fixed top-1/2 -translate-y-1/2 left-0 z-50 h-12 px-2 py-1 rounded-r-full bg-primary shadow-paper text-white">
                            <i class="lab-line-message text-lg"></i>
                        </button>
                    </div>
                    <ul ref="mainChatBox" class="chat-list h-[calc(100dvh-225px)] sm:h-[calc(100dvh-240px)] px-4">
                        <li v-for="(message, index) in messages" :key="index"
                            :class="['chat-item', message.self ? 'chat-user' : 'chat-admin']">
                            <img class="chat-avatar" :src="message.image" alt="avatar"/>
                            <div class="chat-group">
                                <div class="chat-group-text">
                                    <p v-for="(text, i) in message.messages" :key="i" class="chat-text">
                                        {{ text }}
                                    </p>
                                </div>
                                <div class="chat-group-meta">
                                    <span class="chat-meta">{{ message.timestamp }}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <form class="chat-footer px-4" @submit.prevent="addMessage">
                        <input type="text" v-model="text" :placeholder="$t('label.type_message')"
                               class="w-full px-4 py-3 h-12 rounded-full resize-none thin-scrolling bg-[#f7f7fc]">
                        <button type="submit" class="chat-footer-sent">
                            <i class="lab-fill-send text-3xl text-paragraph/80 hover:text-paragraph transition"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {useMessageStore} from "../../../stores/message.js";
import {forEach} from "lodash";
import messageChannelTypeEnum from "../../../enums/modules/messageChannelTypeEnum.js";
import ENV from "../../../config/env.js";

export default {
    name: "MessageComponent",
    setup() {
        const messageStore = useMessageStore();

        return {
            messageStore
        }
    },
    data() {
        return {
            enums: {
                messageChannelTypeEnum: messageChannelTypeEnum
            },
            isMessageOpen: false,
            selectedUser: {},
            text: ""
        }
    },
    computed: {
        users: function () {
            return this.messageStore.lists;
        },
        messages: function () {
            const messageArrays = [];
            let children        = {};
            const messages      = this.messageStore.messages;

            messages.forEach((msg, index) => {
                if ((Object.keys(children).length > 0 && children.user_id !== msg.user_id) || index === 0) {
                    children = {
                        user_id: msg.user_id,
                        image: msg.image,
                        timestamp: msg.created_at,
                        self: msg.self,
                        messages: []
                    }
                    messageArrays.push(children);
                }
                children.timestamp = msg.created_at;
                children.messages.push(msg.text);
            })

            this.$nextTick(() => {
                const el = this.$refs.mainChatBox
                if (el) {
                    el.scrollTop = el.scrollHeight
                }
            })

            return messageArrays;
        }
    },
    created() {
        this.userFetch();
    },
    methods: {
        userFetch: function () {
            return this.messageStore.fetch().then(res => {
                if (res.data.data.length > 0) {
                    this.showMessage(res.data.data[0]);
                    res.data.data.forEach((item) => {
                        this.eventBind(item.id, item.user_id);
                    });
                }
            }).catch()
        },
        showMessage: function (user) {
            this.selectedUser = user;
            this.messageStore.show(user.id).then().catch()
        },
        addMessage: function () {
            try {
                if (Object.keys(this.selectedUser).length > 0) {
                    if (this.text) {
                        this.messageStore.save({
                            order_id: this.selectedUser?.id,
                            text: this.text
                        }).then(res => {
                            this.text = "";
                        }).catch()
                    }
                }
            } catch (error) {
            }
        },
        eventBind: function (orderId, userId) {
            const PUSHER_KEY     = ENV.PUSHER_KEY;
            const PUSHER_CLUSTER = ENV.PUSHER_CLUSTER;
            if (PUSHER_KEY && PUSHER_CLUSTER) {
                window.Echo.private(`chat.${orderId}.${this.enums.messageChannelTypeEnum.DELIVERY}.${userId}`).listen('NewChatMessage', (e) => {
                    if (this.selectedUser.user_id === userId) {
                        this.messageStore.show(orderId).then().catch()
                    }
                });
            }
        }
    }
}
</script>
