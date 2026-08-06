<template>
    <LoadingComponent :props="loading"/>

    <div class="db-card">
        <div class="db-card-header border-none !items-center gap-3">
            <div>
                <h3 class="db-card-title">{{ $t("menu.time_slots") }}</h3>
                <p class="text-sm text-[#6E7191] mt-1">{{ $t("message.time_slots_hint") }}</p>
            </div>
            <button
                v-if="!timeSlots.length"
                type="button"
                class="db-btn text-white bg-primary whitespace-nowrap"
                @click="generateDefaults"
            >
                {{ $t("button.generate_default_time_slots") }}
            </button>
        </div>

        <div class="db-card-body py-0">
            <div v-if="!timeSlots.length" class="py-10 text-center text-[#6E7191]">
                {{ $t("message.no_time_slots_yet") }}
            </div>

            <ul class="flex flex-col" v-if="enums.dayEnums.length > 0">
                <li class="flex sm:items-start flex-col sm:flex-row border-b border-[#EFF0F6] py-4 gap-3"
                    v-for="dayEnum in enums.dayEnums" :key="dayEnum.id">
                    <p class="capitalize w-28 flex-shrink-0 text-sm font-medium text-[#374151] pt-2">
                        {{ dayEnum.name }}
                    </p>
                    <div class="flex items-center flex-wrap gap-2 flex-1 border-l border-[#EFF0F6] sm:pl-4">
                        <div
                            v-for="timeSlot in slotsForDay(dayEnum.id)"
                            :key="timeSlot.id"
                            class="relative flex items-start gap-6 py-2.5 px-3 rounded-lg border border-[#EFF0F6] bg-white"
                        >
                            <SmTimeSlotDeleteComponent @click="destroy(timeSlot.id)"/>
                            <div>
                                <p class="text-xs whitespace-nowrap capitalize mb-1.5 text-[#6E7191]">
                                    {{ $t("label.opening_time") }}
                                </p>
                                <p class="flex items-center gap-1">
                                    <i class="lab lab-fill-clock lab-font-size-16 lab-font-color-3"></i>
                                    <span class="text-xs whitespace-nowrap text-[#374151]">
                                        {{ timeSlot.opening_time }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs whitespace-nowrap capitalize mb-1.5 text-[#6E7191]">
                                    {{ $t("label.closing_time") }}
                                </p>
                                <p class="flex items-center gap-1">
                                    <i class="lab lab-fill-clock lab-font-size-16 lab-font-color-3"></i>
                                    <span class="text-xs whitespace-nowrap text-[#374151]">
                                        {{ timeSlot.closing_time }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs whitespace-nowrap capitalize mb-1.5 text-[#6E7191]">
                                    {{ $t("label.status") }}
                                </p>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700">
                                    {{ $t("label.open") }}
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="!slotsForDay(dayEnum.id).length"
                            class="text-xs text-[#A0A3BD] italic py-2"
                        >
                            {{ $t("label.closed") }}
                        </div>

                        <TimeSlotCreateComponent :props="props" :day="dayEnum.id"/>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../../common/LoadingComponent.vue";
import TimeSlotCreateComponent from "./TimeSlotCreateComponent.vue";
import alertService from "../../../../services/alertService.js";
import SmTimeSlotDeleteComponent from "../../components/buttons/SmTimeSlotDeleteComponent.vue";
import dayEnum from "../../../../enums/modules/dayEnum.js";
import {useTimeSlotStore} from "../../../../stores/timeSlot.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "TimeSlotListComponent",
    components: {
        TimeSlotCreateComponent,
        LoadingComponent,
        SmTimeSlotDeleteComponent
    },
    setup() {
        const timeSlotStore = useTimeSlotStore();
        return {
            timeSlotStore
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                dayEnums: dayEnum
            },
            props: {
                form: {
                    opening_time: "",
                    closing_time: "",
                    day: ""
                },
                search: {
                    paginate: 0,
                    order_column: "day",
                    order_type: "asc"
                }
            }
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        timeSlots: function () {
            return this.timeSlotStore.lists || [];
        }
    },
    methods: {
        slotsForDay(dayId) {
            return this.timeSlots.filter((slot) => Number(slot.day) === Number(dayId));
        },
        list: function () {
            this.loading.isActive = true;
            this.timeSlotStore.fetch(this.props.search).then(() => {
                this.loading.isActive = false;
            }).catch(() => {
                this.loading.isActive = false;
            });
        },
        generateDefaults: function () {
            this.loading.isActive = true;
            this.timeSlotStore.generateDefaults(this.props.search).then((res) => {
                this.loading.isActive = false;
                alertService.success(res.data?.message || this.$t("message.default_time_slots_generated"));
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response?.data?.message || this.$t("message.something_wrong"));
            });
        },
        destroy: function (id) {
            return new VueSimpleAlert.confirm(
                this.$t("message.delete_record"),
                this.$t("message.are_you_sure"),
                "warning",
                {
                    confirmButtonText: this.$t("button.yes_delete"),
                    cancelButtonText: this.$t("button.no_cancel"),
                    confirmButtonColor: "#1AB759",
                    cancelButtonColor: "#E93C3C"
                }
            ).then(() => {
                try {
                    this.loading.isActive = true;
                    this.timeSlotStore.destroy({id: id, search: this.props.search}).then(() => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.time_slots"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response?.data?.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err);
                }
            }).catch(() => {
                this.loading.isActive = false;
            });
        }
    }
};
</script>
