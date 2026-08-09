<template>
    <h4 v-if="isTerminalBad"
        class="text-xl font-medium text-center mb-4">
        {{ $t("label.your_order", {
            order: enums.orderStatusEnumArray[props.status],
        }) }}
    </h4>

    <img v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.CANCELED)" class="w-32 h-32 mx-auto mb-3"
        :src="setting.image_order_canceled" alt="gif">
    <img v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.REJECTED)" class="w-32 h-32 mx-auto mb-3"
        :src="setting.image_order_rejected" alt="gif">
    <img v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.RETURNED)" class="w-32 h-32 mx-auto mb-3"
        :src="setting.image_order_returned" alt="gif">

    <div v-if="!isTerminalBad">
        <div v-if="isDelivered && isDelivery" class="text-center mb-4">
            <h4 class="text-xl font-medium">{{ $t('message.enjoy_your_food') }}</h4>
        </div>
        <div v-if="isDelivered && isTakeaway" class="text-center mb-4">
            <h4 class="text-xl font-medium">{{ $t('message.picked_up_successfully') }}</h4>
        </div>
        <div v-if="isDelivered && isDining" class="text-center mb-4">
            <h4 class="text-xl font-medium">{{ $t('message.dine_in_order_completed') }}</h4>
            <p class="text-xs text-paragraph mt-1">{{ $t('message.dine_in_pay_at_counter_reminder') }}</p>
        </div>

        <div v-if="!isDelivered && Number(props.preparation_time) > 0" class="text-center mb-4">
            <p class="text-xs text-paragraph mb-1">
                {{ isDelivery ? $t('label.estimated_delivery_time') : $t('label.estimated_preparation_time') }}
            </p>
            <h4 class="text-xl font-medium">{{ props.preparation_time }} min</h4>
        </div>

        <img v-if="isDelivered && isDelivery" class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_delivered" alt="gif">
        <img v-if="isDelivered && (isTakeaway || isDining)" class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_complete" alt="gif">
        <img v-if="statusNum === enums.orderStatusEnum.PENDING || statusNum === enums.orderStatusEnum.ACCEPT"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_placed" alt="gif">
        <img v-if="statusNum === enums.orderStatusEnum.PREPARING"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_preparing" alt="gif">
        <img v-if="statusNum === enums.orderStatusEnum.PREPARED"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_prepared" alt="gif">
        <img v-if="statusNum === enums.orderStatusEnum.OUT_FOR_DELIVERY"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_out_for_delivery" alt="gif">

        <!-- Progress bar -->
        <ul class="w-full flex items-center mb-3">
            <li v-for="(step, index) in timelineSteps" :key="step.status" class="group w-full last:w-fit flex items-center">
                <i
                    :class="[
                        step.icon,
                        isStepReached(step.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]',
                        'flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full'
                    ]"
                ></i>
                <hr
                    v-if="index < timelineSteps.length - 1"
                    :class="isStepReached(timelineSteps[index + 1].status) ? 'bg-primary' : 'bg-[#FFEBDD]'"
                    class="flex-auto w-full h-1 border-0"
                />
            </li>
        </ul>

        <!-- Step labels -->
        <ul class="w-full flex mb-5 gap-0.5">
            <li
                v-for="step in timelineSteps"
                :key="'label-' + step.status"
                class="flex-1 text-center px-0.5"
            >
                <span
                    class="block text-[10px] sm:text-xs leading-tight font-medium"
                    :class="isStepReached(step.status) ? 'text-heading' : 'text-paragraph'"
                >
                    {{ step.label }}
                </span>
            </li>
        </ul>

        <p v-if="statusNum === enums.orderStatusEnum.PENDING" class="text-xs text-center">
            {{ $t("message.we_received_your_order") }}
        </p>
        <div
            v-if="showScanMenuCancelNotice"
            class="mt-4 mx-auto max-w-md overflow-hidden rounded-2xl border-2 border-amber-400 bg-amber-50 text-left"
        >
            <div class="flex gap-2.5 p-3">
                <i class="lab-line-clock text-lg text-amber-600 mt-0.5"></i>
                <p class="text-xs leading-5 text-heading font-medium">
                    {{ $t('message.scan_menu_cancel_window') }}
                </p>
            </div>
        </div>
        <p v-if="statusNum === enums.orderStatusEnum.ACCEPT" class="text-xs text-center">
            {{ $t("message.restaurant_has_accepted_your_order") }}
        </p>
        <p v-if="statusNum === enums.orderStatusEnum.PREPARING" class="text-xs text-center">
            {{ $t("message.meal_is_being_freshly_prepared_by_restaurant") }}
        </p>
        <p v-if="statusNum === enums.orderStatusEnum.PREPARED" class="text-xs text-center">
            {{ isDining ? $t("message.your_food_is_ready_for_table") : $t("message.your_order_is_ready") }}
        </p>
        <p v-if="statusNum === enums.orderStatusEnum.OUT_FOR_DELIVERY" class="text-xs text-center">
            {{ $t("message.your_food_is_on_the_move") }}
        </p>
        <p v-if="isDelivered && isDelivery" class="text-xs text-center">
            {{ $t("message.delivered_successfully_we_hope_you_enjoy_your_meal") }}
        </p>

        <p v-if="props.updated_at || props.updated_at_iso" class="text-[11px] text-center text-paragraph mt-4">
            {{ $t('label.last_updated') }}:
            <span class="text-heading font-medium">{{ props.updated_at || formatIso(props.updated_at_iso) }}</span>
        </p>
    </div>
</template>

<script>
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import { useFrontendSettingStore } from "../../../stores/frontendSetting.js";
import { isScanMenuOrder } from "../../../utils/orderHelpers.js";

export default {
    name: "OrderStatusComponent",
    props: ['props'],
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        return { frontendSettingStore };
    },
    data() {
        return {
            enums: {
                orderStatusEnum: orderStatusEnum,
                orderTypeEnum: orderTypeEnum,
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t("label.out_for_delivery"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                    [orderStatusEnum.RETURNED]: this.$t("label.returned"),
                },
            },
        };
    },
    computed: {
        setting() {
            return this.frontendSettingStore.lists;
        },
        statusNum() {
            return parseInt(this.props?.status, 10) || 0;
        },
        orderTypeNum() {
            return parseInt(this.props?.order_type, 10) || 0;
        },
        isDelivery() {
            return this.orderTypeNum === orderTypeEnum.DELIVERY;
        },
        isTakeaway() {
            return this.orderTypeNum === orderTypeEnum.TAKEAWAY;
        },
        isDining() {
            return this.orderTypeNum === orderTypeEnum.DINING_TABLE;
        },
        showScanMenuCancelNotice() {
            return this.statusNum === orderStatusEnum.PENDING;
        },
        isDelivered() {
            return this.statusNum === orderStatusEnum.DELIVERED;
        },
        isTerminalBad() {
            return [
                orderStatusEnum.CANCELED,
                orderStatusEnum.REJECTED,
                orderStatusEnum.RETURNED,
            ].includes(this.statusNum);
        },
        timelineSteps() {
            const steps = [
                { status: orderStatusEnum.PENDING, label: this.$t('label.track_order_received'), icon: 'lab lab-fill-like' },
                { status: orderStatusEnum.ACCEPT, label: this.$t('label.track_accepted'), icon: 'lab lab-fill-save' },
                { status: orderStatusEnum.PREPARING, label: this.$t('label.track_preparing'), icon: 'lab lab-fill-preparing' },
                { status: orderStatusEnum.PREPARED, label: this.$t('label.track_ready'), icon: 'lab lab-fill-reserve' },
            ];
            if (this.isDelivery) {
                steps.push({
                    status: orderStatusEnum.OUT_FOR_DELIVERY,
                    label: this.$t('label.track_on_the_way'),
                    icon: 'lab lab-fill-on-the-way',
                });
            }
            steps.push({
                status: orderStatusEnum.DELIVERED,
                label: this.$t('label.track_completed'),
                icon: 'lab lab-fill-delivered',
            });
            return steps;
        },
    },
    methods: {
        isStepReached(stepStatus) {
            return this.statusNum >= parseInt(stepStatus, 10);
        },
        formatIso(iso) {
            if (!iso) return '';
            try {
                return new Date(iso).toLocaleString();
            } catch (e) {
                return iso;
            }
        },
    },
};
</script>
