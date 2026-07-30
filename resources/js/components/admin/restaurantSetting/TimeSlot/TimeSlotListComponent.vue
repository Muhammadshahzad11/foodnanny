<template>
    <LoadingComponent :props="loading"/>

    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">{{ $t("menu.time_slots") }}</h3>
        </div>
        <div class="db-card-body py-0">
            <ul class="flex flex-col" v-if="enums.dayEnums.length > 0">
                <li class="flex sm:items-center flex-col sm:flex-row border-b border-[#EFF0F6]"
                    v-for="dayEnum in enums.dayEnums" :key="dayEnum">
                    <p class="capitalize pt-5 sm:pt-0 w-24 flex-shrink-0 text-sm text-[#374151]">
                        {{ dayEnum.name }}
                    </p>
                    <div class="flex items-center mr-4 flex-wrap border-l border-[#EFF0F6]">
                        <div class="flex items-center flex-wrap" v-for="timeSlot in timeSlots" :key="timeSlot">
                            <div class="relative flex items-start gap-8 py-2 px-3 rounded-lg border border-[#EFF0F6] time-slot-gap" v-if="dayEnum.id === timeSlot.day">
                                <SmTimeSlotDeleteComponent @click="destroy(timeSlot.id)"/>
                                <div>
                                    <p class="text-xs whitespace-nowrap capitalize mb-1.5">
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
                                    <p class="text-xs whitespace-nowrap capitalize mb-1.5">
                                        {{ $t("label.closing_time") }}
                                    </p>
                                    <p class="flex items-center gap-1">
                                        <i class="lab lab-fill-clock lab-font-size-16 lab-font-color-3"></i>
                                        <span class="text-xs whitespace-nowrap text-[#374151]">
                                            {{ timeSlot.closing_time }}
                                        </span>
                                    </p>
                                </div>
                            </div>
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
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent.vue";
import PaginationBox from "../../components/pagination/PaginationBox.vue";
import PaginationSMBox from "../../components/pagination/PaginationSMBox.vue";
import appService from "../../../../services/appService.js";
import TableLimitComponent from "../../components/TableLimitComponent.vue";
import SmTimeSlotDeleteComponent from "../../components/buttons/SmTimeSlotDeleteComponent.vue";
import dayEnum from "../../../../enums/modules/dayEnum.js";
import {useTimeSlotStore} from "../../../../stores/timeSlot.js";
import VueSimpleAlert from "vue3-simple-alert";

export default {
    name: "TimeSlotListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
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
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "closing_time",
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
            return this.timeSlotStore.lists;
        },
        pagination: function () {
            return this.timeSlotStore.pagination;
        },
        paginationPage: function () {
            return this.timeSlotStore.page;
        }
    },
    methods: {
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        list: function (page = 1) {
            this.loading.isActive  = true;
            this.props.search.page = page;
            this.timeSlotStore. fetch(this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
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
            ).then((res) => {
                try {
                    this.loading.isActive = true;
                    this.timeSlotStore.destroy({id: id, search: this.props.search}).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.time_slots"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    }
};
</script>
