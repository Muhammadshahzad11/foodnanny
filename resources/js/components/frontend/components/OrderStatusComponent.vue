<template>
    <h4 v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.CANCELED)"
        class=" text-xl font-medium text-center mb-4">
        {{ $t("label.your_order", {
            order: enums.orderStatusEnumArray[props.status],
        }) }}
    </h4>
    <h4 v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.REJECTED)"
        class=" text-xl font-medium text-center mb-4">
        {{ $t("label.your_order", {
            order: enums.orderStatusEnumArray[props.status],
        }) }}
    </h4>
    <h4 v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.RETURNED)"
        class=" text-xl font-medium text-center mb-4">
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

    <div v-if="parseInt(props.status) !== parseInt(enums.orderStatusEnum.CANCELED) && parseInt(props.status) !== parseInt(enums.orderStatusEnum.RETURNED) && parseInt(props.status) !== parseInt(enums.orderStatusEnum.REJECTED)">
        <div v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.DELIVERED) && parseInt(props.order_type) === parseInt(enums.orderTypeEnum.DELIVERY)">
            <h4 class="text-xl font-medium text-center mb-4">{{ $t('message.enjoy_your_food') }}</h4>
        </div>

        <div v-if="parseInt(props.status) !== parseInt(enums.orderStatusEnum.DELIVERED) && parseInt(props.order_type) === parseInt(enums.orderTypeEnum.DELIVERY)">
            <p class="text-xs text-center mb-2 text-paragraph">{{ $t('label.estimated_delivery_time') }}</p>
            <h4 class="text-xl font-medium text-center mb-4">{{ props.preparation_time }} min</h4>
        </div>

        <img v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.DELIVERED) && parseInt(props.order_type) === parseInt(enums.orderTypeEnum.DELIVERY)"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_delivered" alt="gif">
        <img v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.DELIVERED) && parseInt(props.order_type) === parseInt(enums.orderTypeEnum.TAKEAWAY)"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_complete" alt="gif">
        <img v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.DELIVERED) && parseInt(props.order_type) === parseInt(enums.orderTypeEnum.DINING_TABLE)"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_complete" alt="gif">
        <img v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.PENDING) || parseInt(props.status) === parseInt(enums.orderStatusEnum.ACCEPT)"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_placed" alt="gif">
        <img v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.PREPARING)"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_preparing" alt="gif">
        <img v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.PREPARED)"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_prepared" alt="gif">
        <img v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.OUT_FOR_DELIVERY)"
            class="w-32 h-32 mx-auto mb-3" :src="setting.image_order_out_for_delivery" alt="gif">

        <ul class="w-full flex items-center mb-6" v-if="parseInt(props.order_type) === parseInt(enums.orderTypeEnum.DELIVERY)">
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.PENDING) <=parseInt(props.status)  ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-like flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.PENDING) <=parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.ACCEPT) <= parseInt(props.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-save flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.ACCEPT) <= parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.PREPARING) <= parseInt(props.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-preparing flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.PREPARING) <= parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.PREPARED) <= parseInt(props.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-reserve flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.PREPARED) <= parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.OUT_FOR_DELIVERY) <= parseInt(props.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-on-the-way flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.OUT_FOR_DELIVERY) <= parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.DELIVERED) <= parseInt(props.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-delivered flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.DELIVERED) <= parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
        </ul>

        <ul class="w-full flex items-center mb-6" v-if="parseInt(props.order_type) === parseInt(enums.orderTypeEnum.TAKEAWAY) || parseInt(props.order_type) === parseInt(enums.orderTypeEnum.DINING_TABLE)">
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.PENDING) <=parseInt(props.status)  ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-like flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.PENDING) <=parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.ACCEPT) <= parseInt(props.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-save flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.ACCEPT) <= parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.PREPARING) <= parseInt(props.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-preparing flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.PREPARING) <= parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.PREPARED) <= parseInt(props.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-reserve flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.PREPARED) <= parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
            <li class="group w-full last:w-fit flex items-center">
                <i :class="parseInt(enums.orderStatusEnum.DELIVERED) <= parseInt(props.status) ? 'text-white bg-primary' : 'text-primary bg-[#FFEBDD]'" class="lab lab-fill-delivered flex-shrink-0 w-8 h-8 leading-8 text-center rounded-full"></i>
                <hr :class="parseInt(enums.orderStatusEnum.DELIVERED) <= parseInt(props.status) ? 'bg-primary' : 'bg-[#FFEBDD]'" class="flex-auto w-full h-1 border-0 group-last:hidden"/>
            </li>
        </ul>

        <p v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.PENDING)" class="text-xs text-center">
            {{ $t("message.we_received_your_order") }}
        </p>
        <p v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.ACCEPT)" class="text-xs text-center">
          {{ $t("message.restaurant_has_accepted_your_order") }}
        </p>

        <p v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.PREPARING)" class="text-xs text-center">
            {{ $t("message.meal_is_being_freshly_prepared_by_restaurant") }}
       </p>

        <p v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.PREPARED)" class="text-xs text-center">
            {{ $t("message.your_order_is_ready") }}
       </p>

        <p v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.OUT_FOR_DELIVERY)"
            class="text-xs text-center">{{ $t("message.your_food_is_on_the_move") }}
        </p>

        <p class="text-xs text-center" v-if="parseInt(props.order_type) === parseInt(enums.orderTypeEnum.DELIVERY)">
            <span v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.DELIVERED)">
                {{ $t("message.delivered_successfully_we_hope_you_enjoy_your_meal") }}
            </span>
        </p>

        <p class="text-xs text-center" v-if="parseInt(props.order_type) === parseInt(enums.orderTypeEnum.TAKEAWAY)">
            <span v-if="parseInt(props.status) === parseInt(enums.orderStatusEnum.DELIVERED)">
              {{ $t("message.picked_up_successfully") }}
           </span>
        </p>
    </div>
</template>

<script>
import orderStatusEnum from "../../../enums/modules/orderStatusEnum.js";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum.js";
import { useFrontendSettingStore } from "../../../stores/frontendSetting.js";

export default {
    name: "OrderStatusComponent",
    components: {},
    props: ['props'],
    setup() {
        const frontendSettingStore = useFrontendSettingStore();

        return {
            frontendSettingStore
        }
    },
    data() {
        return {
            statusFlag: true,
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
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway"),
                    [orderTypeEnum.DINING_TABLE]: this.$t("label.dining_table")
                },
                deliveryArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.OUT_FOR_DELIVERY]: this.$t("label.out_for_delivery"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                },
                takeawayArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.ACCEPT]: this.$t("label.accept"),
                    [orderStatusEnum.PREPARING]: this.$t("label.preparing"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                }
            },
            name: "",
        }
    },
    computed: {
        setting: function () {
            return this.frontendSettingStore.lists;
        },
    },
    created() {
        this.$watch('props', (response) => {
            this.name = response?.user?.name;
        })
    },
}
</script>
