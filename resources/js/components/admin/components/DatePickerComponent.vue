<template>
    <VueDatePicker v-model="modelValue" :inputClassName="className" menuClassName="foodnanny-menu"
                   :placeholder="inputStyle === 'filter' ? '' : $t('label.select_date_range')"
                   :presetDates="range ? presetDates : []" :enableTimePicker="false" :autoApply="true" :range="range"
                   utc="false"
                   :teleport="inputStyle === 'filter'">
        <template #input-icon>
            <i class="lab-line-calendar"></i>
            <i class="lab-line-chevron-down"></i>
        </template>
        <template #clear-icon="{ clear }"><i @click="clear" class="lab-line-cross"></i></template>
        <template #preset-date-range-button="{ label, value, presetDate }">
            <button type="button" @click="presetDate(value)" @keyup.enter.prevent="presetDate(value)"
                    @keyup.space.prevent="presetDate(value)"
                    class="text-xs font-medium px-2 py-2 rounded-md tracking-wide capitalize text-center bg-gray-100">
                {{ label }}
            </button>
        </template>
    </VueDatePicker>
</template>

<script>
import {
    endOfMonth,
    endOfYear,
    startOfMonth,
    startOfYear,
    startOfWeek,
    endOfWeek,
    subWeeks,
    subMonths,
    subYears
} from 'date-fns';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    name: "DatePickerComponent",
    props: {
        inputStyle: {
            type: String,
            required: true,
            validator: (propValue) => ['box', 'read', 'filter'].includes(propValue)
        },
        range: {
            type: Boolean
        }
    },
    components: {
        VueDatePicker
    },
    data() {
        return {
            modelValue: null,
            presetDates: [
                {
                    label: 'Today',
                    value: [new Date(), new Date()],
                    slot: 'preset-date-range-button'
                },
                {
                    label: 'Last Week',
                    value: [startOfWeek(subWeeks(new Date(), 1)), endOfWeek(subWeeks(new Date(), 1))],
                    slot: 'preset-date-range-button'
                },
                {
                    label: 'This Month',
                    value: [startOfMonth(new Date()), endOfMonth(new Date())],
                    slot: 'preset-date-range-button'
                },
                {
                    label: 'Last Month',
                    value: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))],
                    slot: 'preset-date-range-button'
                },
                {
                    label: 'This Year',
                    value: [startOfYear(new Date()), endOfYear(new Date())],
                    slot: 'preset-date-range-button'
                },
                {
                    label: 'Last Year',
                    value: [startOfYear(subYears(new Date(), 1)), endOfYear(subYears(new Date(), 1))],
                    slot: 'preset-date-range-button'
                },
            ]
        }
    },
    computed: {
        className(state) {
            if (state.inputStyle === 'box') {
                return 'foodnanny-input box'
            } else if (state.inputStyle === 'read') {
                const date = new Date();
                const startDate = new Date(date.getFullYear(), date.getMonth(), 1);
                const endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                this.modelValue = [startDate, endDate];
                return 'foodnanny-input read'
            } else if (state.inputStyle === 'filter') {
                return 'foodnanny-input filter'
            } else return 'foodnanny-input'
        }
    },
}
</script>
