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

        <!-- Progress bar (not clickable — tracker only) -->
        <ul class="w-full flex items-center mb-3">
            <li v-for="(step, index) in timelineSteps" :key="step.status" class="group w-full last:w-fit flex items-center">
                <span
                    :class="[
                        isOtpCurrent(step)
                            ? 'w-10 h-10 text-white bg-primary ring-4 ring-primary/25 scale-110'
                            : (isStepReached(step.status) ? 'w-8 h-8 text-white bg-primary' : 'w-8 h-8 text-primary bg-[#FFEBDD]'),
                        'flex-shrink-0 inline-flex items-center justify-center rounded-full transition'
                    ]"
                >
                    <svg v-if="step.status === 'otp'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="w-4 h-4">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <i v-else :class="[step.icon, 'text-sm leading-none']"></i>
                </span>
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
                    :class="isOtpCurrent(step) ? 'text-primary font-semibold' : (isStepReached(step.status) ? 'text-heading' : 'text-paragraph')"
                >
                    {{ step.label }}
                </span>
            </li>
        </ul>

        <!-- Big delivery PIN — always visible for delivery, not a clickable tab -->
        <div v-if="showOtpCard" class="mb-5 rounded-2xl border-2 p-4 text-center"
             :class="isOtpLive
                ? 'border-primary bg-gradient-to-br from-emerald-50 via-white to-amber-50 shadow-[0_10px_30px_rgba(26,183,89,0.18)]'
                : 'border-dashed border-primary/40 bg-[#FFF8F2]'">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-primary mb-3">
                {{ $t('label.your_delivery_otp') }}
            </p>
            <div v-if="otpDigits.length" class="flex justify-center gap-2 sm:gap-3 mb-3">
                <span
                    v-for="(digit, i) in otpDigits"
                    :key="'d'+i"
                    class="w-12 h-14 sm:w-14 sm:h-16 rounded-xl bg-white border-2 border-primary text-heading text-3xl sm:text-4xl font-bold flex items-center justify-center shadow-sm"
                >{{ digit }}</span>
            </div>
            <p v-else class="text-sm text-paragraph mb-3">••••</p>
            <p class="text-xs sm:text-sm leading-5 text-heading font-medium mb-3">
                {{ isOtpLive ? $t('message.share_this_otp_with_rider') : $t('message.otp_ready_give_when_rider_arrives') }}
            </p>
            <button
                v-if="otpDigits.length"
                type="button"
                class="inline-flex items-center gap-1.5 rounded-full bg-primary text-white text-xs font-semibold px-4 py-2"
                @click="copyOtp"
            >
                {{ otpCopied ? $t('button.copied') : $t('button.copy_otp') }}
            </button>
        </div>

        <p v-if="statusNum === enums.orderStatusEnum.PENDING" class="text-xs text-center">
            {{ $t("message.we_received_your_order") }}
        </p>
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

export default {
    name: "OrderStatusComponent",
    props: ['props'],
    setup() {
        const frontendSettingStore = useFrontendSettingStore();
        return { frontendSettingStore };
    },
    data() {
        return {
            otpCopied: false,
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
        isDelivered() {
            return this.statusNum === orderStatusEnum.DELIVERED;
        },
        showOtpCard() {
            return this.isDelivery
                && this.statusNum >= orderStatusEnum.PREPARED
                && this.statusNum < orderStatusEnum.DELIVERED;
        },
        isOtpLive() {
            return this.statusNum >= orderStatusEnum.OUT_FOR_DELIVERY
                && this.statusNum < orderStatusEnum.DELIVERED;
        },
        otpDigits() {
            const code = String(this.props?.delivery_otp || '').replace(/\D/g, '');
            return code.length ? code.split('') : [];
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
                steps.push({
                    status: 'otp',
                    label: this.$t('label.track_otp'),
                    icon: '',
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
            if (stepStatus === 'otp') {
                return this.statusNum >= orderStatusEnum.OUT_FOR_DELIVERY;
            }
            return this.statusNum >= parseInt(stepStatus, 10);
        },
        isOtpCurrent(step) {
            return step.status === 'otp' && this.isOtpLive;
        },
        async copyOtp() {
            const code = String(this.props?.delivery_otp || '');
            if (!code) return;
            try {
                await navigator.clipboard.writeText(code);
            } catch (e) {
                const input = document.createElement('input');
                input.value = code;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
            }
            this.otpCopied = true;
            setTimeout(() => { this.otpCopied = false; }, 2000);
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
